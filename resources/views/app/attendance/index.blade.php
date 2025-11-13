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
                    type="button" role="tab">Registrasi Masuk</button>
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
                                                    name="cardnodevice" placeholder="Tempelkan kartu..." autofocus>

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

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nik" id="nik-in" required
                                        placeholder="NIK">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Peserta <span class="text-danger">*
                                        </span></label>
                                    <input type="text" class="form-control" name="nama_pegawai" id="nama_pegawai-in"
                                        required placeholder="Nama Peserta" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="departemen" id="departemen-in" required
                                        placeholder="Departemen" readonly>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary" id="saveRegistrasi">Simpan</button>
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
                                                    name="cardnodevice" placeholder="Tempelkan kartu..." autofocus>

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

                                <div class="attendanceResult">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">NIK <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nik" id="nik-out"
                                            required placeholder="NIK">
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nama Peserta <span class="text-danger">*
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

                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary" id="saveCheckOut">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TAB DATA --}}
            <div class="tab-pane fade" id="dataTab" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Data Peserta</h6>
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
                                        <th>Jam Pulang</th>
                                        <th>Status Doorprize</th>
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
                            console.log(res);
                            if (res.status === 'success') {
                                $('#nik-in').val(res.data.nik);
                                $('#nama_pegawai-in').val(res.data.nama);
                                $('#departemen-in').val(res.data.departemen);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Gagal',
                                    text: res.message,
                                });
                                $('#btnResetRFID').click();
                                return;
                            }
                        },
                        error: function(xhr) {
                            let response = xhr.responseJSON;

                            if (xhr.status === 403) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sudah Terdaftar',
                                    text: response.message,
                                });
                                $('#btnResetRFID').click();
                                return;
                            } else {
                                console.error(response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat scan ID Card. Silakan coba lagi.',
                                });
                                $('#btnResetRFID').click();
                                return;
                            }
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
                            if (res.status === 'success') {
                                $('#nik-out').val(res.data.nik);
                                $('#nama_pegawai-out').val(res.data.nama);
                                $('#departemen-out').val(res.data.departemen);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Gagal',
                                    text: res.message,
                                });
                                $('#btnResetRFID-out').click();
                            }
                        },
                        error: function(xhr) {
                            let response = xhr.responseJSON;
                            console.error(response);
                            if (xhr.status === 403) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sudah Terdaftar',
                                    text: response.message,
                                });
                                $('#btnResetRFID-out').click();
                                return;
                            } else if (xhr.status === 404) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Tidak Ditemukan',
                                    text: response.message,
                                });
                                $('#btnResetRFID-out').click();
                                return;
                            } else {
                                console.error(response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat scan ID Card. Silakan coba lagi.',
                                });
                                $('#btnResetRFID-out').click();
                            }
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
            $('#cardnodevice-in').focus();
        });

        $('#btnResetRFID-out').on('click', function() {
            $('#cardnodevice-out, #nik-out, #nama_pegawai-out, #departemen-out').val('');
            $('#cardnodevice-out').focus();
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
                        name: 'jam_masuk    '
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

            $('#formRegistrasi').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('attendance.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#saveRegistrasi').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#saveRegistrasi').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            console.error(response.message);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        console.error(response);

                        if (xhr.status === 403) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sudah Terdaftar',
                                text: response.message,
                            });
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                        });
                    }
                });
            });

            $('#formCheckOut').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('attendance.checkout') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#saveCheckOut').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#saveCheckOut').prop('disabled', false).text('Simpan');
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            console.error(res.message);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;

                        if (xhr.status === 403) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sudah Keluar',
                                text: response.message,
                            });
                            $('#btnResetRFID').click();
                            return;
                        } else if (xhr.status === 404) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tidak Ditemukan',
                                text: response.message,
                            });
                            $('#btnResetRFID').click();
                            return;
                        } else {
                            console.error(response);

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }

                    }
                });
            });
        });
    </script>
@endsection
