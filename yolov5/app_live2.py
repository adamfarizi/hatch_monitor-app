import cv2
import numpy as np
import torch
from flask import Flask, Response
from models.common import DetectMultiBackend
from utils.general import check_img_size, non_max_suppression, scale_boxes
from utils.torch_utils import select_device
import pathlib
import os
import requests
from dotenv import load_dotenv
import mysql.connector
from datetime import datetime
from mysql.connector import Error

temp = pathlib.PosixPath
pathlib.PosixPath = pathlib.WindowsPath

# Load environment variables from .env file
load_dotenv()

app = Flask(__name__)

# Initialize YOLOv5 model
weights = 'best.pt'
device = select_device('')
model = DetectMultiBackend(weights, device=device)
model.eval()  # Set the model to evaluation mode

imgsz = check_img_size((640, 640), s=model.stride)

# Define colors for each class
fertil_color = (176, 96, 11)    # Brownish color
infertil_color = (26, 0, 210)  # Blue color

def get_db_connection():
    try:
        connection = mysql.connector.connect(
            host=os.getenv('DB_HOST'),
            user=os.getenv('DB_USERNAME'),
            password=os.getenv('DB_PASSWORD'),
            database=os.getenv('DB_DATABASE')
        )
        if connection.is_connected():
            return connection
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

def get_esp32_cam_url():
    connection = get_db_connection()
    if connection:
        try:
            cursor = connection.cursor(dictionary=True)
            cursor.execute("SELECT link2 FROM master LIMIT 1")  # Replace with your actual query
            result = cursor.fetchone()
            if result:
                return result['link2']
            else:
                print("No URL found in the database.")
                return None
        except Error as e:
            print(f"Error fetching data from database: {e}")
            return None
        finally:
            cursor.close()
            connection.close()
    else:
        return None
    
def insert_to_db(class_name, akurasi):
    try:
        connection = get_db_connection()
        cursor = connection.cursor()

        query = """
            INSERT INTO live (waktu, class, akurasi, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s)
        """
        current_time = datetime.now()
        data = (current_time, class_name, akurasi, current_time, current_time)
        
        cursor.execute(query, data)
        connection.commit()
        
        # Send broadcast to Laravel
        send_broadcast_to_laravel(class_name, akurasi)

        print("Data inserted successfully and broadcast sent")
    except mysql.connector.Error as error:
        print(f"Failed to insert into MySQL table {error}")
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()

def send_broadcast_to_laravel(class_name, akurasi):
    # URL Laravel API endpoint
    laravel_api_url = "http://localhost:8000/api/broadcast-detection"
    
    # Data to send to Laravel
    payload = {
        'waktu': datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
        'class': class_name,
        'akurasi': akurasi,
    }
    
    try:
        response = requests.post(laravel_api_url, json=payload)
        if response.status_code == 200:
            print("Broadcast successful")
        else:
            print(f"Failed to send broadcast. Status code: {response.status_code}")
    except Exception as e:
        print(f"Error during broadcast: {e}")

def process_frame(frame):
    # Convert to RGB and resize
    frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    frame_resized = cv2.resize(frame_rgb, (640, 640))

    # Convert to tensor
    img = torch.from_numpy(frame_resized).to(device)
    img = img.permute(2, 0, 1).unsqueeze(0)  # HWC to CHW format and add batch dimension
    img = img.half() if model.fp16 else img.float()  # Convert to float
    img /= 255  # Normalize to [0, 1]

    # Perform inference
    with torch.no_grad():
        pred = model(img, augment=False)

    # Process predictions
    pred = non_max_suppression(pred, conf_thres=0.25, iou_thres=0.45)

    # Define class names
    names = model.names if hasattr(model, 'names') else ['unknown']

    for i, det in enumerate(pred):
        if det is not None:
            det = det.cpu()  # Move tensor to CPU if needed
            det = det[0] if det.ndim == 3 else det  # Get detections if batch dimension exists
            if len(det):
                try:
                    det[:, :4] = scale_boxes(img.shape[2:], det[:, :4], frame.shape[:2]).round()
                except Exception as e:
                    print(f'Error during scale_boxes: {e}')
                    continue

                for *xyxy, conf, cls in reversed(det):
                    label = f'{names[int(cls)]} {conf:.2f}'
                    # Choose color based on class
                    if names[int(cls)] == 'fertil':
                        color = fertil_color
                    elif names[int(cls)] == 'infertil':
                        color = infertil_color
                    else:
                        color = (0, 255, 0)  # Default color for unknown classes
                        
                    # Insert detected class and accuracy into database
                    insert_to_db(names[int(cls)], float(conf))
                        
                    frame = cv2.rectangle(frame, (int(xyxy[0]), int(xyxy[1])), (int(xyxy[2]), int(xyxy[3])), color, 2)
                    frame = cv2.putText(frame, label, (int(xyxy[0]), int(xyxy[1])-10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 2)

                    # Print Hasil
                    print(f'Prediction output: {names[int(cls)]} {conf:.2f}')
    return frame

def gen_frames():
    URL = get_esp32_cam_url()  # Fetch URL for each request
    if URL:
        cap = cv2.VideoCapture(URL + ":81/stream")
        while True:
            if cap.isOpened():
                ret, frame = cap.read()
                if ret:
                    frame = process_frame(frame)
                    # Encode the frame as JPEG
                    ret, buffer = cv2.imencode('.jpg', frame)
                    frame = buffer.tobytes()
                    yield (b'--frame\r\n'
                            b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')
    else:
        print("URL is not set. Cannot fetch video stream.")
        yield (b'--frame\r\n'
                b'Content-Type: image/jpeg\r\n\r\n' + b'\r\n')  # Return empty frame

@app.route('/video_feed')
def video_feed():
    return Response(gen_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
