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
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title">
                        Data Kondisi Harian Telur
                    </h5>
                    <div class="filter">
                        <form method="POST"
                            action="{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian/index_create') }}">
                            @csrf
                            <div class="row">
                                {{-- Webcam --}}
                                <div id="my_camera" style="display: none;"></div>

                                <div class="col-md-6">
                                    <input type="hidden" name="image" class="image-tag">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Penetasan
                            </button>
                        </form>
                    </div>
                </div>
                <div class="d-flex flex-column flex-lg-row mb-5 pb-3">
                    <div class="col-12 col-lg-6">
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
                    <div class="col-12 col-lg-6">
                        <div id="container" class="container mb-1" style="height: 84%;">
                            <p class="fw-semibold">Hasil Capture <span class="ms-5 fw-normal">:</span></p>
                            <div id="imageWrapper" class="bg-dark h-100 text-center text-white"
                                style="border-radius: 25px; position: relative; overflow: hidden; max-width:450px;">
                                <div id="imageResult" style="width: 100%; height: 100%; object-fit: cover;">
                                    <div class="d-flex justify-content-center align-items-center h-100">
                                        <div id="loading" class="spinner-border text-light" role="status">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="window.location.reload()" class="btn btn-primary mt-3 w-100"
                                style="max-width:450px;">
                                <i class="bi bi-camera-fill me-1"></i> Ambil Gambar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="container p-4" style="border-radius: 15px; background-color: #f6f9ff;">
                    {{-- Card Harian --}}
                    @forelse ($harians as $harian)
                        <div class="card">
                            <div class="card-body">
                                {{-- Nav --}}
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title">
                                        {{ \Carbon\Carbon::parse($harian->waktu_harian)->locale('id')->translatedFormat('l, j F Y (H:i)') }}
                                    </h5>
                                    <div class="filter">
                                        <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                class="bi bi-three-dots" style="font-size: 20px;"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                            <li><a class="dropdown-item"
                                                    href="{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian/' . $harian->id_harian . '/edit') }}"><i
                                                        class="bi bi-pencil-square"></i> Edit</a></li>
                                            <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#deleteData{{ $harian->id_harian }}"><i
                                                        class="bi bi-trash"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                                {{-- Content --}}
                                <div class="row">
                                    <div class="col-md-6">
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
                                                                <li>Jumlah : {{ $infertil->jumlah_infertil }} telur</li>
                                                                <li>
                                                                    <a class="" href="#"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#buktiScan{{ $harian->id_harian }}">
                                                                        <i class="bi bi-camera-fill me-1"></i>Bukti
                                                                        scan</a>
                                                                </li>
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
                                    <div class="col-md-6">
                                        <p class="fw-semibold">Bukti Capture <span class="ms-5 fw-normal">:</span></p>
                                        <div class="mb-1" style="max-height: 40vh; max-width: 450px;">
                                            @if ($harian->bukti_harian)
                                                <img src="{{ asset('images/capture/' . $harian->bukti_harian) }}"
                                                    class="img-fluid" alt="Bukti Harian"
                                                    style=" width: 100%; height: 40vh; object-fit: cover; border-radius: 25px;">
                                            @else
                                                <div class="bg-dark h-100 text-center text-white"
                                                    style="border-radius: 25px;">
                                                    <p style="padding-top: 18vh; padding-bottom: 18vh;">
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

    {{-- Modal Bukti Scan --}}
    @foreach ($harians as $harian)
        <div class="modal fade" id="buktiScan{{ $harian->id_harian }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" style="color: #012970;">Bukti Scan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @foreach ($harian->infertil as $infertil)
                            <div class="bg-dark h-100 text-center text-white"
                                style="border-radius: 25px; position: relative; overflow: hidden; max-height: 40vh; max-width:450px;">
                                @php
                                    $scanPath = public_path('images/scan/' . $infertil->bukti_infertil);
                                    $capturePath = asset('images/capture/' . $infertil->bukti_infertil);
                                    $imageUrl = file_exists($scanPath)
                                        ? asset('images/scan/' . $infertil->bukti_infertil)
                                        : $capturePath;
                                @endphp
                                <div class="bg-dark h-100 text-center text-white"
                                    style="border-radius: 25px; position: relative; overflow: hidden; max-height: 40vh; max-width:450px;">
                                    <img src="{{ $imageUrl }}" class="img-fluid" alt="Bukti Harian"
                                        style="width: 100%; height: 40vh; object-fit: cover; border-radius: 25px;">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@section('js')
    {{-- ESP Cam Style --}}
    <script>
        window.onload = function() {
            adjustContainerHeight();
        };

        window.onresize = function() {
            adjustContainerHeight();
        };

        function adjustContainerHeight() {
            var container = document.getElementById("container");
            var imageWrapper = document.getElementById("imageWrapper");
            var screenWidth = window.innerWidth;

            if (screenWidth <= 576) {
                container.style.height = "";
                container.style.maxHeight = "40vh";
                imageWrapper.style.maxHeight = "40vh";
            } else {
                container.style.height = "40vh";
                imageWrapper.style.height = "40vh";
            }
        }
    </script>

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

    {{-- ESP Cam --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            capturePhoto();
        });

        var link = '{{ $link1 }}';

        function capturePhoto() {
            fetch(`${link}/capture`)
                .then(response => response.text())
                .then(data => {
                    setTimeout(() => {
                        const imageUrl = `${link}/saved-photo`;
                        fetchImageAsDataUri(imageUrl);
                    }, 5000); // wait 5 seconds for the photo to be saved
                })
                .catch(error => console.error('Error:', error));
        }

        function fetchImageAsDataUri(imageUrl) {
            fetch(imageUrl)
                .then(response => response.blob())
                .then(blob => {
                    const reader = new FileReader();
                    reader.onloadend = function() {
                        const dataUri = reader.result;
                        displayImage(dataUri);
                        document.querySelector('.image-tag').value = dataUri;
                    };
                    reader.readAsDataURL(blob);
                })
                .catch(error => console.error('Error:', error));
        }

        function displayImage(dataUri) {
            const imageResult = document.getElementById('imageResult');
            imageResult.innerHTML = '<img src="' + dataUri +
                '" style="width: 100%; height: 100%; object-fit: cover;">';
        }
    </script> --}}
@endsection
