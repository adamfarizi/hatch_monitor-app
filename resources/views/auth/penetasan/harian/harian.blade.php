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
                        <a type="button" class="btn btn-primary"
                            href="{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian/create') }}"><i
                                class="bi bi-plus-lg me-1"></i> Tambah
                            Penetasan</a>
                    </div>
                </h5>
                <div class="d-flex">
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
                                    <td class="fw-semibold">Tanggal Selesai</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($penetasan->tanggal_selesai)->locale('id')->translatedFormat('l, j F Y (H:i)') }}
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
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <table class="table table-borderless">
                            <tbody>
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
                                                {{-- @if ($penetasan->id_infertil)     --}}
                                                <tr>
                                                    <td colspan="3">
                                                        <p class="fw-semibold mb-0">Infertil</p>
                                                        <ul>
                                                            <li>Nomor Telur : A1, B2, B3</li>
                                                            <li>Jumlah : 3</li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                                {{-- @endif --}}
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
                                            <div class="bg-secondary h-100 text-center text-white"
                                                style="border-radius: 25px;">
                                                <p style="padding-top: 19vh">
                                                    <i class="bi bi-exclamation-circle-fill"></i>
                                                    <span>Belum ada gambar</span>
                                                </p>
                                            </div>
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
                                {{ $harian->id_harian }}
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
@endsection
