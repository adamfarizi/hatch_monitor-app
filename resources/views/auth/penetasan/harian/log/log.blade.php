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
                <h5 class="card-title row">
                    <div class="col">Log Harian Penetasan</div>
                </h5>
                <div class="datatable-wrapper datatable-loading sortable searchable fixed-columns p-3">
                    <table id="tableLog" class="table" role="grid" style="min-height: 500px;">
                        <thead>
                            <tr>
                                <th class="col">#</th>
                                <th class="col">Waktu Log</th>
                                <th class="col">Infertil</th>
                                <th class="col">Fertil</th>
                                <th class="col">Unknown</th>
                                <th class="col">Bukti Log</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Bukti Scan --}}
    @foreach ($logs as $log)
        <div class="modal fade" id="buktiScan{{ $log->id_log }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" style="color: #012970;">Bukti Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center">
                        <div class="bg-dark text-center text-white"
                            style="border-radius: 25px; position: relative; overflow: hidden; max-height: 80vh; max-width: 100%;">
                            <img src="{{ asset('images/log/' . $log->bukti_log) }}" class="img-fluid" alt="Bukti Log Harian"
                                style="max-height: 80vh; object-fit: cover; border-radius: 25px;">
                        </div>
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
    {{-- Datatables --}}
    <script>
        $(document).ready(function() {
            var table = $('#tableLog').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ url('/penetasan/' . $penetasan->id_penetasan . '/harian/log') }}",
                    data: function(d) {
                        var filterBulan = $('#filterBulan').val();
                        d.filterBulan = filterBulan;
                    }
                },
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'waktu_log',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            var waktu_log = moment(full.waktu_log);
                            var formattedDate = waktu_log.format('dddd, D MMMM YYYY');
                            var formattedTime = waktu_log.format('HH:mm');
                            return '<p class="mb-0">' +
                                formattedDate +
                                '</p>' +
                                '<p class="mb-0">Jam: ' +
                                formattedTime +
                                '</p>';
                        }
                    },
                    {
                        data: null,
                        searchable: true,
                        render: function(data, type, full, meta) {
                            return '<ul>' +
                                '<li>Rendah: ' + full.infertil_rendah + '</li>' +
                                '<li>Sedang: ' + full.infertil_sedang + '</li>' +
                                '<li>Tinggi: ' + full.infertil_tinggi + '</li>' +
                                '</ul>';
                        }
                    },
                    {
                        data: null,
                        searchable: true,
                        render: function(data, type, full, meta) {
                            return '<ul>' +
                                '<li>Rendah: ' + full.fertil_rendah + '</li>' +
                                '<li>Sedang: ' + full.fertil_sedang + '</li>' +
                                '<li>Tinggi: ' + full.fertil_tinggi + '</li>' +
                                '</ul>';
                        }
                    },
                    {
                        data: 'unknown',
                        searchable: true,
                    }, {
                        data: 'bukti_log',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            var imagePath = '/images/log/' + data;
                            return '<img src="' + imagePath +
                                '" style="max-width: 100px; border-radius: 5px; cursor: pointer;" ' +
                                'data-bs-toggle="modal" data-bs-target="#buktiScan' + full.id_log +
                                '" />';
                        }
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1], // Jumlah entries per halaman, -1 untuk Tampilkan Semua Data
                    ['10', '25', '50', '100', 'Semua']
                ]
            });

            $(
                    '<span class="ms-2"><label>Filter: <input type="month" id="filterBulan" class="form-control"></label></span>'
                )
                .appendTo('.dataTables_wrapper .dataTables_filter');

            $('#filterBulan').on('change', function() {
                table.ajax.reload();
            });

        });
    </script>
@endsection
