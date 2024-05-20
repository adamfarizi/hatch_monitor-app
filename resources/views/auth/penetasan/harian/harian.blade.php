@extends('auth/app')
@section('pagetittle')
    <div class="pagetitle">
        <h1>@yield('title', $title)</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('penetasan') }}">Penetasan</a></li>
                <li class="breadcrumb-item active">@yield('title', $title)</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection
@section('content')
    <section class="section">
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                <b>Sukses ! </b> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $err)
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-octagon me-1"></i>
                    <b>Gagal ! </b> {{ $err }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                        aria-label="Close"></button>
                </div>
            @endforeach
        @endif
        <div class="card">
            <div class="card-body">
                <h5 class="card-title row">
                    <div class="col">Data Kondisi Harian Telur</div>
                    <div class="col text-end">
                        <form method="POST"
                            action="{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian/index_create') }}">
                            @csrf
                            <div class="row">
                                {{-- Webcam --}}
                                <div id="my_camera" style="display: none;"></div>

                                {{-- Espcam --}}
                                {{-- <img id="espCamImage" src="http://192.168.88.140" alt="ESP-CAM Image"> --}}

                                <div class="col-md-6">
                                    <input type="hidden" name="image" class="image-tag">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Penetasan
                            </button>
                        </form>
                    </div>
                </h5>
                <div class="d-flex mb-3">
                    <div class="col">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Tanggal Mulai</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($penetasan->tanggal_mulai)->locale('id')->translatedFormat('l, j F Y (H:i)') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tanggal Akhir Scan</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($penetasan->batas_scan)->locale('id')->translatedFormat('l, j F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tanggal Selesai</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($penetasan->tanggal_selesai)->locale('id')->translatedFormat('l, j F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Jumlah Telur</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->jumlah_telur }} butir</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Prediksi Menetas</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->prediksi_menetas }} butir</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Total Menetas</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->total_menetas }} butir</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Rata-rata Suhu</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->rata_rata_suhu }} °C</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Rata-rata Kelembaban</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->rata_rata_kelembaban }} %</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <div class="col container">
                            <div class="container mb-1" style="height: 40vh">
                                <div class="bg-secondary h-100 text-center text-white"
                                    style="border-radius: 25px; position: relative; overflow: hidden; width:450px;">
                                    <div id="imageResult" onclick="window.location.reload()"
                                        style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container p-4" style="border-radius: 15px; background-color: #f6f9ff;">
                    {{-- Card Harian --}}
                    @forelse ($harians as $harian)
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title d-flex">
                                    <div class="col">
                                        {{ \Carbon\Carbon::parse($harian->waktu_harian)->locale('id')->translatedFormat('l, j F Y (H:i)') }}
                                    </div>
                                    <div class="col text-end">
                                        <div class="filter">
                                            <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                    class="bi bi-three-dots"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <li><a class="dropdown-item"
                                                        href="{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian/' . $harian->id_harian . '/edit') }}"><i
                                                            class="bi bi-pencil-square"></i></span>Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteData{{ $harian->id_harian }}"><i
                                                            class="bi bi-trash"></i></span>Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </h5>
                                <div class="d-flex">
                                    <div class="col">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-semibold">Telur Menetas</td>
                                                    <td>:</td>
                                                    <td>{{ $harian->menetas }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Suhu</td>
                                                    <td>:</td>
                                                    <td>{{ $harian->suhu_harian }} °C</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Kelembaban</td>
                                                    <td>:</td>
                                                    <td>{{ $harian->kelembaban_harian }} %</td>
                                                </tr>
                                                @foreach ($harian->infertil as $infertil)
                                                    <tr>
                                                        <td colspan="3">
                                                            <p class="fw-semibold mb-0">Infertil</p>
                                                            <ul>
                                                                {{-- <li>Nomor Telur : A1, B2, B3</li> --}}
                                                                <li>Jumlah : {{ $infertil->jumlah_infertil }}</li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="3">
                                                        <p class="fw-semibold mb-0">Deskripsi</p>
                                                        {{ $harian->deskripsi }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col container">
                                        <div class="container mb-1" style="height: 40vh">
                                            @if ($harian->bukti_harian)
                                                <img src="{{ asset('images/scan/' . $harian->bukti_harian) }}"
                                                    class="img-fluid" alt="Bukti Harian" style="border-radius: 25px;">
                                            @else
                                                <div class="bg-secondary h-100 text-center text-white"
                                                    style="border-radius: 25px;">
                                                    <p style="padding-top: 19vh">
                                                        <i class="bi bi-exclamation-circle-fill"></i>
                                                        <span>Belum ada gambar</span>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="container"
                            style="border: 5px dashed #D0D4DB; background-color: transparent; border-radius: 10px;">
                            <div class="align-items-center">
                                <div class="text-center my-4" style="color: #D0D4DB">
                                    <h4 class="fw-bold">
                                        <span><i class="bi bi-info-circle me-3"></i></span>Belum ada data penetasan
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Delete --}}
    @foreach ($harians as $harian)
        <div class="modal fade" id="deleteData{{ $harian->id_harian }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form
                    action="{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian/' . $harian->id_harian . '/delete') }}"
                    method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="container text-center p-3">
                                <img src="{{ asset('assets/img/local/danger.png') }}" width="80px" alt="">
                                <h3 class="mt-4 fw-bold">Anda yakin ingin hapus data ini ?</h3>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
@section('js')
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const imageContainer = document.getElementById('imageContainer');
            const imageIcon = document.getElementById('imageIcon');
            const imageStatus = document.getElementById('imageStatus');
            const webcamVideo = document.getElementById('webcamVideo');
            let imageDataUrl = null;

            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    webcamVideo.srcObject = stream;

                    // Capture image automatically after a short delay
                    setTimeout(captureImage, 100); // Adjust the delay as needed
                })
                .catch(error => {
                    console.error('Error accessing webcam:', error);
                    imageIcon.classList.add('bi-exclamation-circle-fill');
                    imageStatus.textContent = 'Error: Webcam tidak tersedia';
                });

            function captureImage() {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                const video = document.getElementById('webcamVideo');
                const containerWidth = imageContainer.offsetWidth;
                const containerHeight = imageContainer.offsetHeight;

                canvas.width = containerWidth;
                canvas.height = containerHeight;

                // Scale video to fit container
                const scaleX = containerWidth / video.videoWidth;
                const scaleY = containerHeight / video.videoHeight;
                const scale = Math.max(scaleX, scaleY);
                const xOffset = (containerWidth - video.videoWidth * scale) / 2;
                const yOffset = (containerHeight - video.videoHeight * scale) / 2;

                context.drawImage(video, xOffset, yOffset, video.videoWidth * scale, video.videoHeight * scale);
                imageDataUrl = canvas.toDataURL('image/png');

                // Stop video stream
                const stream = webcamVideo.srcObject;
                const tracks = stream.getTracks();
                tracks.forEach(track => track.stop());

                // Update UI
                const imageOverlay = document.getElementById('imageOverlay');
                imageOverlay.innerHTML = '';
                const capturedImage = new Image();
                capturedImage.src = imageDataUrl;
                capturedImage.style.maxWidth = '100%';
                capturedImage.style.maxHeight = '100%';
                imageOverlay.appendChild(capturedImage);
                imageIcon.classList.remove('bi-exclamation-circle-fill');
                imageIcon.classList.add('bi-check-circle-fill');
                imageStatus.textContent = 'Gambar berhasil diambil';
            }
        });
    </script> --}}

    {{-- Webcam --}}
    <script language="JavaScript">
        Webcam.set({
            width: 450,
            height: 280,
            dest_width: 450,
            dest_height: 280,
            image_format: 'jpeg',
            jpeg_quality: 100
        });

        Webcam.attach('#my_camera');

        setTimeout(take_snapshot, 2000); // Sesuaikan delay jika diperlukan

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                $(".image-tag").val(data_uri);

                const imageResult = document.getElementById('imageResult');
                imageResult.innerHTML = '<img src="' + data_uri +
                    '" style="width: 100%; height: 100%; object-fit: cover;">';

                // Sembunyikan kamera setelah tangkapan gambar dilakukan
                Webcam.reset();
                document.getElementById('my_camera').style.display = 'none';
            });
        }
    </script>

    {{-- ESP cam --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imgElement = document.getElementById('espCamImage');

            // Function to take screenshot
            function takeScreenshot() {
                // Use html2canvas to take a screenshot of the image element
                html2canvas(imgElement).then(canvas => {
                    // Convert the canvas to a data URL (base64 string)
                    const imgData = canvas.toDataURL('image/png');

                    // Get the div where the screenshot will be displayed
                    const imageResultDiv = document.getElementById('imageResult');

                    // Create an image element to display the screenshot
                    const img = document.createElement('img');
                    img.src = imgData;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';

                    // Clear the div and append the screenshot image
                    imageResultDiv.innerHTML = '';
                    imageResultDiv.appendChild(img);
                }).catch(error => {
                    console.error('Error taking screenshot:', error);
                });
            }

            // Listen for the image to load
            imgElement.addEventListener('load', function() {
                // Take screenshot after the image has loaded
                takeScreenshot();
            });
        });
    </script> --}}
@endsection
