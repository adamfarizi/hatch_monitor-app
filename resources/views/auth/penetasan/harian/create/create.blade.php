@extends('auth/app')
@section('pagetittle')
    <div class="pagetitle">
        <h1>@yield('title', $title)</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('penetasan') }}">Penetasan</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian') }}">Cek
                        Kondisi Harian</a></li>
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
                <h5 class="card-title">Tambah Data Kondisi Harian Telur</h5>
                <form action="{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian/create') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <label for="waktu_harian" class="col-sm-2 col-form-label">Waktu <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="datetime-local" class="form-control" id="waktu_harian" name="waktu_harian"
                                value="{{ now() }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Infertil <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col input-group mb-3">
                                    <input type="number" class="form-control" aria-describedby="basic-addon2"
                                        id="jumlah_infertil" name="jumlah_infertil" value="{{ $infertil }}" readonly>
                                    <span class="input-group-text" id="basic-addon2">Telur</span>
                                </div>
                            </div>
                            <div class="container mb-3" style="height: 40vh">
                                <div class="col input-group">
                                    <input type="hidden" class="form-control" aria-describedby="basic-addon2"
                                        id="bukti_scan" name="bukti_scan" value="{{ $imagePath }}" readonly>
                                </div>
                                <div class="bg-secondary h-100 text-center text-white"
                                    style="border-radius: 25px; position: relative; overflow: hidden; width:450px;">
                                    <div style="width: 100%; height: 100%; object-fit: cover;">
                                        <img src="{{ asset('images/scan/'.$imagePath) }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="menetas" class="col-sm-2 col-form-label">Menetas <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="menetas" name="menetas" required>
                        </div>
                    </div>
                    <fieldset class="row mb-3">
                        <legend class="col-form-label col-sm-2 pt-0">Suhu <span class="text-danger">*</span></legend>
                        <div class="col-sm-10 d-flex">
                            <div class="col form-check">
                                <input class="form-check-input" type="radio" name="suhu_radio" id="suhu_scan_radio"
                                    value="scan" checked="">
                                <label class="form-check-label mb-2" for="suhu_scan_radio">Scan</label>
                                <div class="row mb-3" id="textSectionSuhu">
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" aria-describedby="basic-addon2"
                                            id="suhu_scan" name="suhu_scan" value="{{ $suhu }}" readonly>
                                        <span class="input-group-text" id="basic-addon2">°C</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col form-check">
                                <input class="form-check-input" type="radio" name="suhu_radio" id="suhu_manual_radio"
                                    value="manual">
                                <label class="form-check-label mb-2" for="suhu_manual_radio">Manual</label>
                                <div class="row mb-3" id="formSectionSuhu" style="display: none;">
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" aria-describedby="basic-addon2"
                                            id="suhu_manual" name="suhu_manual">
                                        <span class="input-group-text" id="basic-addon2">°C</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="row mb-3">
                        <legend class="col-form-label col-sm-2 pt-0">Kelembaban <span class="text-danger">*</span>
                        </legend>
                        <div class="col-sm-10 d-flex">
                            <div class="col form-check">
                                <input class="form-check-input" type="radio" name="kelembaban_radio"
                                    id="kelembaban_scan_radio" value="scan" checked="">
                                <label class="form-check-label mb-2" for="kelembaban_scan_radio">Scan</label>
                                <div class="row mb-3" id="textSectionKelembaban">
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" aria-describedby="basic-addon2"
                                            id="kelembaban_scan" name="kelembaban_scan" value="{{ $kelembaban }}"
                                            readonly>
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col form-check">
                                <input class="form-check-input" type="radio" name="kelembaban_radio"
                                    id="kelembaban_manual_radio" value="manual">
                                <label class="form-check-label mb-2" for="kelembaban_manual_radio">Manual</label>
                                <div class="row mb-3" id="formSectionKelembaban" style="display: none;">
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" aria-describedby="basic-addon2"
                                            id="kelembaban_manual" name="kelembaban_manual">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row mb-3">
                        <label for="deskripsi" class="col-sm-2 col-form-label">Deskripsi catatan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" style="height: 100px" id="deskripsi" name="deskripsi"></textarea>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var radio1 = document.getElementById('suhu_scan_radio');
            var radio2 = document.getElementById('suhu_manual_radio');
            var textSectionSuhu = document.getElementById('textSectionSuhu');
            var formSectionSuhu = document.getElementById('formSectionSuhu');
            var radio3 = document.getElementById('kelembaban_scan_radio');
            var radio4 = document.getElementById('kelembaban_manual_radio');
            var textSectionKelembaban = document.getElementById('textSectionKelembaban');
            var formSectionKelembaban = document.getElementById('formSectionKelembaban');

            if (radio1.checked) {
                textSectionSuhu.style.display = 'block';
            }

            radio1.addEventListener('change', function() {
                if (this.checked) {
                    textSectionSuhu.style.display = 'block';
                    formSectionSuhu.style.display = 'none';
                }
            });

            radio2.addEventListener('change', function() {
                if (this.checked) {
                    textSectionSuhu.style.display = 'none';
                    formSectionSuhu.style.display = 'block';
                }
            });

            if (radio3.checked) {
                textSectionKelembaban.style.display = 'block';
            }

            radio3.addEventListener('change', function() {
                if (this.checked) {
                    textSectionKelembaban.style.display = 'block';
                    formSectionKelembaban.style.display = 'none';
                }
            });

            radio4.addEventListener('change', function() {
                if (this.checked) {
                    textSectionKelembaban.style.display = 'none';
                    formSectionKelembaban.style.display = 'block';
                }
            });
        });
    </script>
@endsection
