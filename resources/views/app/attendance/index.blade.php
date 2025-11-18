@extends('layouts.component.main')
@section('title', 'Kehadiran')
@section('content')
    <div class="page-title-head d-flex align-items-center">
        <div class="flex-grow-1">
            <h4 class="fs-xl fw-bold m-0">Kehadiran Peserta</h4>
        </div>
    </div>

    <div class="container-fluid">
        <ul class="nav nav-tabs" id="penitipanTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="registrasi-tab" data-bs-toggle="tab" data-bs-target="#registrasiTab"
                    type="button" role="tab">Check In (Masuk)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="check-out-tab" data-bs-toggle="tab" data-bs-target="#checkOutTab"
                    type="button" role="tab">Check Out (Keluar)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#dataTab" type="button"
                    role="tab">Data Peserta</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="attendanceContent">
            {{-- TAB REGISTRASI --}}
            <div class="tab-pane fade show active" id="registrasiTab" role="tabpanel">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Form Registrasi Masuk</h6>
                    </div>
                    <div class="card-body">
                        <form id="formRegistrasi">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="row g-2 align-items-end">
                                        <div class="col">
                                            <label class="form-label fw-semibold">Scan ID Card/Masukan NIK</label>

                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="cardnodevice-in"
                                                    name="cardnodevice" placeholder="Tempelkan ID card atau masukan NIK..."
                                                    autofocus>

                                                <div id="loaderRFID"
                                                    class="d-none position-absolute top-50 end-0 translate-middle-y me-2">
                                                    <div class="spinner-border text-primary"
                                                        style="width: 1rem; height: 1rem;" role="status"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-outline-secondary" id="btnResetRFID">Reset
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <div class="checkInResult d-none">
                                    <div class="row g-3">

                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">NIK <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nik" id="nik-in"
                                                required placeholder="NIK">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nama Peserta <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_pegawai"
                                                id="nama_pegawai-in" required placeholder="Nama Peserta" readonly>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Departemen <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="departemen" id="departemen-in"
                                                required placeholder="Departemen" readonly>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TAB CHECKOUT --}}
            <div class="tab-pane fade" id="checkOutTab" role="tabpanel">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Form Checkout</h6>
                    </div>
                    <div class="card-body">
                        <form id="formCheckOut">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="row g-2 align-items-end">
                                        <div class="col">
                                            <label class="form-label fw-semibold">Scan ID Card/Masukan NIK</label>

                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="cardnodevice-out"
                                                    name="cardnodevice"
                                                    placeholder="Tempelkan ID card atau masukan NIK..." autofocus>

                                                <div id="loaderRFID-out"
                                                    class="d-none position-absolute top-50 end-0 translate-middle-y me-2">
                                                    <div class="spinner-border text-primary"
                                                        style="width: 1rem; height: 1rem;" role="status"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="btnResetRFID-out">Reset
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <div class="checkOutResult d-none">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">NIK <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nik" id="nik-out"
                                                required placeholder="NIK">
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Nama Peserta <span
                                                        class="text-danger">*
                                                    </span></label>
                                                <input type="text" class="form-control" name="nama_pegawai"
                                                    id="nama_pegawai-out" required placeholder="Nama Peserta" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Departemen <span
                                                        class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="departemen"
                                                    id="departemen-out" required placeholder="Departemen" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- TAB DATA --}}
        <div class="tab-pane fade" id="dataTab" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Data Peserta</h6>
                    <button id="btnExportExcel" class="btn btn-success btn-sm">
                        Export Excel
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="tableAttendance">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>NIK</th>
                                    <th>Nama Peserta</th>
                                    <th>Departemen</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Status Layak Doorprize</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('script')
    <script>
        $('#cardnodevice-in').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                let cardValue = $(this).val().trim();

                if (cardValue !== '') {
                    $.ajax({
                        url: '/attendance/user/' + cardValue,
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#cardnodevice-in').prop('disabled', true);
                            $('#loaderRFID').removeClass('d-none');
                        },
                        success: function(res) {
                            // store data
                            $('.checkInResult').removeClass('d-none');
                            $('#nik-in').val(res.data.nik);
                            $('#nama_pegawai-in').val(res.data.nama);
                            $('#departemen-in').val(res.data.departemen);

                            $.ajax({
                                url: "{{ route('attendance.store') }}",
                                type: "POST",
                                data: {
                                    nik: res.data.nik,
                                    nama_pegawai: res.data.nama,
                                    departemen: res.data.departemen,
                                    cardnodevice: cardValue,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    // success store
                                    Toast.fire({
                                        icon: 'success',
                                        background: "#28a745",
                                        color: '#ffffff',
                                        title: 'Berhasil!',
                                        html: `
                                            <div class="text-start">
                                                <strong>${response.data.nama_pegawai}</strong><br>
                                                <small>${response.data.nik} - ${response.data.departemen}</small><br>
                                                <small>Jam Masuk: ${response.data.jam_masuk_formatted} WIB</small>
                                            </div>
                                        `,
                                        customClass: {
                                            popup: 'text-white',
                                            title: 'text-white',
                                            htmlContainer: 'text-white'
                                        }
                                    })
                                    $('#btnResetRFID').click();
                                },
                                error: function(xhr) {
                                    // error store
                                    console.error("error store: ", xhr.responseJSON);
                                    if (xhr.status === 403) {
                                        Toast.fire({
                                            icon: 'warning',
                                            background: "#fd7e14",
                                            color: '#ffffff',
                                            title: 'Sudah Terdaftar!',
                                            text: 'Peserta sudah melakukan registrasi sebelumnya',
                                            customClass: {
                                                popup: 'text-white',
                                                title: 'text-white',
                                                htmlContainer: 'text-white'
                                            }
                                        });
                                    } else {
                                        Toast.fire({
                                            icon: 'error',
                                            background: "#dc3545",
                                            color: '#ffffff',
                                            title: 'Gagal!',
                                            text: 'Terjadi kesalahan dalam menyimpan data',
                                            customClass: {
                                                popup: 'text-white',
                                                title: 'text-white',
                                                htmlContainer: 'text-white'
                                            }
                                        })
                                    }
                                    $('#btnResetRFID').click();
                                }
                            });
                        },
                        error: function(xhr) {
                            console.error("error getUser: ", xhr.responseJSON);

                            if (xhr.status === 404) {
                                Toast.fire({
                                    icon: 'warning',
                                    background: "#fd7e14",
                                    color: '#ffffff',
                                    title: 'Tidak Ditemukan!',
                                    text: 'Karyawan tidak terdaftar dalam sistem',
                                    customClass: {
                                        popup: 'text-white',
                                        title: 'text-white',
                                        htmlContainer: 'text-white'
                                    }
                                })
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    background: "#dc3545",
                                    color: '#ffffff',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan dalam scan ID card',
                                    customClass: {
                                        popup: 'text-white',
                                        title: 'text-white',
                                        htmlContainer: 'text-white'
                                    }
                                })
                            }
                            $('#btnResetRFID').click();
                        },
                        complete: function() {
                            $('#cardnodevice-in').prop('disabled', false);
                            $('#loaderRFID').addClass('d-none');

                        }
                    });
                }
            }
        });

        $('#cardnodevice-out').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                let cardValue = $(this).val().trim();

                if (cardValue !== '') {
                    $.ajax({
                        url: '/attendance/user/' + cardValue,
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#cardnodevice-out').prop('disabled', true);
                            $('#loaderRFID-out').removeClass('d-none');
                        },
                        success: function(res) {
                            // ajax checkout
                            $('.checkOutResult').removeClass('d-none');
                            $('#nik-out').val(res.data.nik);
                            $('#nama_pegawai-out').val(res.data.nama);
                            $('#departemen-out').val(res.data.departemen);

                            $.ajax({
                                // checkout data
                                url: "{{ route('attendance.checkout') }}",
                                type: "POST",
                                data: {
                                    nik: res.data.nik,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    // success checkout
                                    Toast.fire({
                                        icon: 'success',
                                        background: "#28a745",
                                        color: '#ffffff',
                                        title: 'Berhasil Checkout!',
                                        html: `
                                                <div class="text-start">
                                                    <strong>${response.data.nama_pegawai}</strong><br>
                                                    <small>${response.data.nik} - ${response.data.departemen}</small><br>
                                                    <small>Jam Keluar: ${response.data.jam_keluar_formatted} WIB</small>
                                                </div>
                                            `,
                                        customClass: {
                                            popup: 'text-white',
                                            title: 'text-white',
                                            htmlContainer: 'text-white'
                                        }
                                    })
                                    $('#btnResetRFID-out').click();
                                },
                                error: function(xhr) {
                                    console.error("error checkout: ", xhr.responseJSON);

                                    if (xhr.status === 403) {
                                        Toast.fire({
                                            icon: 'warning',
                                            background: "#fd7e14",
                                            color: '#ffffff',
                                            title: 'Sudah Keluar!',
                                            text: 'Peserta sudah melakukan checkout sebelumnya',
                                            customClass: {
                                                popup: 'text-white',
                                                title: 'text-white',
                                                htmlContainer: 'text-white'
                                            }
                                        });
                                    } else if (xhr.status === 404) {
                                        Toast.fire({
                                            background: "#fd7e14",
                                            color: '#ffffff',
                                            icon: 'warning',
                                            title: 'Tidak Ditemukan!',
                                            text: 'Peserta belum melakukan registrasi/masuk',
                                            customClass: {
                                                popup: 'text-white',
                                                title: 'text-white',
                                                htmlContainer: 'text-white'
                                            }
                                        });
                                    } else {
                                        Toast.fire({
                                            background: "#dc3545",
                                            color: '#ffffff',
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Terjadi kesalahan saat proses checkout',
                                            customClass: {
                                                popup: 'text-white',
                                                title: 'text-white',
                                                htmlContainer: 'text-white'
                                            }
                                        });
                                    }
                                    $('#btnResetRFID-out').click();
                                }
                            });

                        },
                        error: function(xhr) {
                            // error scan id card
                            console.error("error getUser: ", xhr.responseJSON);

                            if (xhr.status === 404) {
                                Toast.fire({
                                    icon: 'warning',
                                    background: "#fd7e14",
                                    color: '#ffffff',
                                    title: 'Tidak Ditemukan!',
                                    text: 'Karyawan tidak terdaftar dalam sistem',
                                    customClass: {
                                        popup: 'text-white',
                                        title: 'text-white',
                                        htmlContainer: 'text-white'
                                    }
                                })
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    background: "#dc3545",
                                    color: '#ffffff',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan dalam scan ID card',
                                    customClass: {
                                        popup: 'text-white',
                                        title: 'text-white',
                                        htmlContainer: 'text-white'
                                    }
                                })
                            }
                            $('#btnResetRFID-out').click();
                        },
                        complete: function() {
                            $('#cardnodevice-out').prop('disabled', false);
                            $('#loaderRFID-out').addClass('d-none');
                        }
                    });
                }
            }
        });

        $('#btnResetRFID').on('click', function() {
            $('#cardnodevice-in, #nik-in, #nama_pegawai-in, #departemen-in').val('');
            $('.checkInResult').addClass('d-none');
            setTimeout(() => {
                $('#cardnodevice-in').focus();
            }, 100);
        });

        $('#btnResetRFID-out').on('click', function() {
            $('#cardnodevice-out, #nik-out, #nama_pegawai-out, #departemen-out').val('');
            $('.checkOutResult').addClass('d-none');
            setTimeout(() => {
                $('#cardnodevice-out').focus();
            }, 100);
        });

        $('button[data-bs-target="#registrasiTab"]').on('shown.bs.tab', function() {
            $('#cardnodevice-in').trigger('focus');
        });

        $('button[data-bs-target="#checkOutTab"]').on('shown.bs.tab', function() {
            $('#cardnodevice-out').trigger('focus');
        });


        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#tableAttendance').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('attendance.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'nama_pegawai',
                        name: 'nama_pegawai'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: 'jam_masuk_formatted',
                        name: 'jam_masuk'
                    },
                    {
                        data: 'jam_keluar_formatted',
                        name: 'jam_keluar'
                    },
                    {
                        data: 'status_doorprize',
                        name: 'status_doorprize',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#data-tab').on('click', function() {
                $('#tableAttendance').DataTable().ajax
                    .reload()
            });

            $('#btnExportExcel').click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Export Data',
                    text: 'File Excel sedang diproses...',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        window.location.href = "{{ route('attendance.export') }}";
                    }
                });
                setTimeout(() => Swal.close(), 1500);
            });

        });
    </script>
@endsection
