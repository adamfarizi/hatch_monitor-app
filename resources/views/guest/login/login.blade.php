@extends('guest/app')
@section('content')
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="pt-4 pb-2">
                                <div class="d-flex justify-content-center py-4">
                                    <img src="{{ asset('assets/img/local/logo2.png') }}" width="200" alt="">
                                </div>
                                <p class="text-center small">Masukkan email dan password anda</p>
                            </div>
                            {{-- Notifikasi --}}
                            @if ($errors->any())
                                @foreach ($errors->all() as $err)
                                    <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-octagon me-1"></i>
                                        <b>Gagal ! </b> {{$err}}
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endforeach
                            @endif
                            <form class="row g-3 needs-validation" action="{{ url('/login') }}" method="POST">
                                @csrf
                                <div class="col-12">
                                    <label for="email" class="form-label small">Email</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                        <input type="email" name="email" class="form-control" id="email"
                                            required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="password" class="form-label small">Password</label>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" value="true"
                                            id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">Remember me</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100" type="submit">Masuk</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
