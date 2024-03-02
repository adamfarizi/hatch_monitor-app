@extends('auth/app')
@section('pagetittle')
    <div class="pagetitle">
        <h1>@yield('title', $title)</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Home</a></li>
                <li class="breadcrumb-item active">@yield('title', $title)</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection
@section('content')
    <section class="section">
        <div class="card">
            <img src="{{ asset('assets/img/local/bg.png') }}" height="150" class="card-img-top" alt="..."
                style="object-fit: cover;">
            <img src="{{ asset('assets/img/local/user.png') }}" alt="Profile" class="rounded-circle"
                style="  position: absolute; top: 45%; left: 50%; -ms-transform: translate(-50%, -50%);transform: translate(-50%, -50%);">
            <div class="card-body profile-card pt-5 d-flex flex-column align-items-center">
                <h2>{{ Auth::user()->nama }}</h2>
                <h3>Peternak</h3>
            </div>
        </div>
        <div class="card">
            <div class="card-body pt-3">
                <!-- Bordered Tabs -->
                <ul class="nav nav-tabs nav-tabs-bordered">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit
                            Profil</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Ubah
                            Password</button>
                    </li>
                </ul>
                <div class="tab-content pt-2">
                    {{-- Edit Profil --}}
                    <div class="tab-pane fade show active profile-edit pt-3" id="profile-edit">
                        <h5 class="card-title mb-0">Detail Profil</h5>
                        <p class="card-subtitle text-muted mb-4">Lengkapi form dibawah dengan benar !</p>
                        {{-- Notifikasi --}}
                        @if (session('success'))
                            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-1"></i>                            
                                <b>Sukses ! </b> {{ session('success') }}
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            @foreach ($errors->all() as $err)
                                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-octagon me-1"></i>
                                    <b>Gagal ! </b> {{$err}}
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endforeach
                        @endif
                        <!-- Profile Edit Form -->
                        <form action="{{ url('/profil/'.$peternak->id_peternak.'/edit') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label for="nama" class="col-md-4 col-lg-3 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="col-md-8 col-lg-9">
                                    <input name="nama" type="text" class="form-control" id="nama"
                                        value="{{ $peternak->nama }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-lg-3 col-form-label">Email <span class="text-danger">*</span></label>
                                <div class="col-md-8 col-lg-9">
                                    <input name="email" type="email" class="form-control" id="email"
                                        value="{{ $peternak->email }}" required>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form><!-- End Profile Edit Form -->
                    </div>
                    {{-- Ubah Password --}}
                    <div class="tab-pane fade pt-3" id="profile-change-password">
                        <!-- Change Password Form -->
                        <form action="{{ url('/profil/password') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <label for="current_password" class="col-md-4 col-lg-3 col-form-label">Password Lama <span class="text-danger">*</span></label>
                                <div class="col-md-8 col-lg-9">
                                    <input name="current_password" type="password" class="form-control" id="current_password">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="new_password" class="col-md-4 col-lg-3 col-form-label">Password Baru <span class="text-danger">*</span></label>
                                <div class="col-md-8 col-lg-9">
                                    <input name="new_password" type="password" class="form-control" id="new_password">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="new_password_confirmation" class="col-md-4 col-lg-3 col-form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <div class="col-md-8 col-lg-9">
                                    <input name="new_password_confirmation" type="password" class="form-control" id="new_password_confirmation">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Ubah Password</button>
                            </div>
                        </form><!-- End Change Password Form -->
                    </div>
                </div><!-- End Bordered Tabs -->
            </div>
        </div>
    </section>
@endsection
