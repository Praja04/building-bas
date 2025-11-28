@extends('layouts.component.main')
@section('title', 'Data Peserta')
@section('content')

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-0 pb-0">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Master Data Peserta</h4>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-start gap-2 mt-3" role="alert">
            <i class="ti ti-info-circle fs-4"></i>
            <div>
                <strong>Informasi:</strong>
                Data peserta yang telah mengisi <b>kehadiran</b>, <b>pengambilan makan</b>, atau <b>pengambilan kaos</b>
                <span class="text-danger fw-semibold">tidak dapat dihapus</span> karena sedang digunakan oleh sistem.
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-semibold">Daftar Peserta</h4>

                <div class="d-flex justify-content-end gap-2 flex-wrap flex-md-nowrap">
                    <button class="btn btn-warning btn-sm d-none w-100 w-sm-auto" id="btnDeleteSelected">Hapus (0)</button>
                    <button class="btn btn-danger btn-sm w-100 w-sm-auto" id="btnDeleteAll">Hapus Semua</button>
                    <button class="btn btn-info btn-sm w-100 w-sm-auto" id="btnExport">Export Excel</button>
                    <button class="btn btn-success btn-sm w-100 w-sm-auto" id="btnImportOpen">Import Excel</button>
                    <button class="btn btn-primary btn-sm w-100 w-sm-auto" id="btnAddPeserta">+ Tambah Peserta</button>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablePeserta">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>No.</th>
                                <th>NIK</th>
                                <th>NIK 2</th>
                                <th>Card No. Device 1</th>
                                <th>Card No. Device 2</th>
                                <th>Nama Peserta</th>
                                <th>Departemen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    {{-- MODAL ADD / EDIT EVENT --}}
    <div class="modal fade" id="modalPeserta" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitlePeserta"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="formPeserta">

                        <input type="hidden" id="peserta_id">

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" id="nik" class="form-control" required placeholder="NIK">
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">NIK 2</label>
                                <input type="text" id="nik2" class="form-control" placeholder="NIK 2">
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Card No. Device 1 <span class="text-danger">*</span></label>
                                <input type="text" id="cardnodevice1" class="form-control" required
                                    placeholder="Card No. Device 1">
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Card No. Device 2</label>
                                <input type="text" id="cardnodevice2" class="form-control"
                                    placeholder="Card No. Device 2">
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Nama Peserta <span class="text-danger">*</span></label>
                                <input type="text" id="nama" class="form-control" required
                                    placeholder="Nama Peserta">
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Departemen <span class="text-danger">*</span></label>
                                <input type="text" id="departemen" class="form-control" required
                                    placeholder="Departemen">
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-light" data-bs-dismiss="modal" type="button">Batal</button>
                                <button class="btn btn-primary" id="btnSavePeserta" type="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div class="modal fade" id="modalImport" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Import Data Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="formImport">

                        <div class="mb-3">
                            <div class="p-3 border rounded bg-light">
                                <p class="mb-2">
                                    Gunakan template berikut agar format import sesuai:
                                </p>
                                <a href="{{ asset('assets/templates/template-peserta.xlsx') }}"
                                    class="btn btn-success btn-sm" download>
                                    Download Template
                                </a>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Import <span class="text-danger">*</span></label>
                            <input type="file" id="fileImport" class="form-control" accept=".xls,.xlsx,.csv"
                                required>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button class="btn btn-primary" id="btnImportSubmit" type="submit">Simpan</button>
                            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let modalPeserta = new bootstrap.Modal(document.getElementById('modalPeserta'));

        let modalImport = new bootstrap.Modal(document.getElementById('modalImport'));

        $(document).ready(function() {
            // DATATABLE
            let table = $('#tablePeserta').DataTable({
                ajax: "{{ route('participants.index') }}",
                processing: true,
                serverSide: true,
                columns: [{
                        // data: 'DT_RowIndex',
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(id) {
                            return `<input type="checkbox" class="rowCheck" value="${id}">`;
                        }
                    },
                    {
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nik',
                        name: 'nik',
                    },
                    {
                        data: 'nik2',
                        name: 'nik2',
                    },
                    {
                        data: 'cardnodevice1',
                        name: 'cardnodevice1'
                    },
                    {
                        data: 'cardnodevice2',
                        name: 'cardnodevice2'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-primary btnEdit" data-id="${data.id}">Edit</button>
                                <button class="btn btn-sm btn-danger btnDelete" data-id="${data.id}">Hapus</button>
                            </div>                        
                            `;
                        }
                    }
                ]
            });

            // OPEN ADD MODAL
            $('#btnAddPeserta').click(function() {
                $('#modalTitlePeserta').text('Tambah Peserta');
                $('#peserta_id').val('');
                $('#formPeserta')[0].reset();
                $('#btnSavePeserta').prop('disabled', false).text('Simpan');
                modalPeserta.show();
            });

            $('#modalPeserta').on('shown.bs.modal', function() {
                $('#nik').trigger('focus');
            });

            // SAVE EVENT
            $('#formPeserta').submit(function(e) {
                e.preventDefault();

                $('#btnSavePeserta')
                    .prop('disabled', true)
                    .html('Processing...');

                let id = $('#peserta_id').val();
                let url = id ? "/master/participants/" + id : "/master/participants";
                let method = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        nama: $('#nama').val(),
                        nik: $('#nik').val(),
                        departemen: $('#departemen').val(),
                        nik2: $('#nik2').val(),
                        cardnodevice1: $('#cardnodevice1').val(),
                        cardnodevice2: $('#cardnodevice2').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {
                        modalPeserta.hide();
                        table.ajax.reload();

                        let msg = id ?
                            "Peserta berhasil diperbarui" :
                            "Peserta berhasil ditambahkan";
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: msg,
                        });
                    },
                    error: function(xhr) {
                        let msg = "Terjadi kesalahan saat menyimpan peserta";

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            msg = Object.values(errors)[0][0];
                        } else if (xhr.status === 404) {
                            msg = 'Peserta tidak ditemukan atau sudah dihapus';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg,
                        });
                    },
                    complete: function() {
                        $('#btnSavePeserta')
                            .prop('disabled', false)
                            .text('Simpan');
                    }
                });
            });

            // EDIT EVENT
            $(document).on('click', '.btnEdit', function() {
                let id = $(this).data('id');
                $('.btnEdit').prop('disabled', true);

                $.ajax({
                    url: "/master/participants/" + id,
                    type: "GET",
                    success: function(res) {
                        $('#modalTitlePeserta').text('Edit Peserta');
                        $('#peserta_id').val(res.id);
                        $('#nama').val(res.nama);
                        $('#nik').val(res.nik);
                        $('#departemen').val(res.departemen);
                        $('#nik2').val(res.nik2);
                        $('#cardnodevice1').val(res.cardnodevice1);
                        $('#cardnodevice2').val(res.cardnodevice2);
                        modalPeserta.show();
                    },
                    error: function(xhr) {
                        let msg = "Terjadi kesalahan saat mengambil data peserta";

                        if (xhr.status === 404) {
                            msg = 'Peserta tidak ditemukan atau sudah dihapus';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg,
                        });
                    },
                    complete: function() {
                        $('.btnEdit').prop('disabled', false);
                    }
                });
            });

            // DELETE EVENT
            $(document).on('click', '.btnDelete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: "Hapus Peserta?",
                    text: "Peserta akan dihapus secara permanen.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Hapus",
                    cancelButtonText: "Batal"
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/master/participants/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Peserta berhasil dihapus"
                                });
                            },
                            error: function(xhr) {
                                let msg = "Terjadi kesalahan saat menghapus peserta";

                                if (xhr.status === 404) {
                                    msg = xhr.responseJSON?.message ||
                                        "Peserta tidak ditemukan atau sudah dihapus";
                                } else if (xhr.status === 409) {
                                    msg = xhr.responseJSON?.message ||
                                        "Peserta tidak dapat dihapus karena sedang digunakan";
                                }

                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    html: msg,
                                });
                            }
                        });
                    }
                });
            });


            // OPEN IMPORT MODAL
            $('#btnImportOpen').click(function() {
                $('#formImport')[0].reset();
                $('#btnImportSubmit').prop('disabled', false).text('Simpan');
                modalImport.show();
            });

            // SUBMIT IMPORT
            $('#formImport').submit(function(e) {
                e.preventDefault();

                let file = $('#fileImport')[0].files[0];

                if (!file) {
                    Swal.fire({
                        icon: "warning",
                        title: "File belum dipilih",
                        text: "Silakan pilih file Excel terlebih dahulu"
                    });
                    return;
                }

                $('#btnImportSubmit')
                    .prop('disabled', true)
                    .text('Processing...');

                let formData = new FormData();
                formData.append('file', file);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: "Loading...",
                    text: "Import sedang diproses",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });

                $.ajax({
                    url: "{{ route('participants.import') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        Swal.close();
                        modalImport.hide();
                        table.ajax.reload();

                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: res.message
                        });
                    },
                    error: function(xhr) {
                        Swal.close();

                        let res = xhr.responseJSON;

                        if (res.error === "DuplicateData") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                html: `
                                    ${res.message}<br><br>
                                    <b>NIK:</b> ${res.data.nik}<br>
                                    <b>Nama:</b> ${res.data.nama}<br>
                                    <b>Departemen:</b> ${res.data.departemen}
                                `
                            });
                        } else if (res.error === "MissingData") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                html: `
                                    ${res.message}<br><br>
                                    <b>NIK:</b> ${res.data.nik || "(BELUM DIISI)"}<br>
                                    <b>Nama:</b> ${res.data.nama || "(BELUM DIISI)"}<br>
                                    <b>Departemen:</b> ${res.data.departemen || "(BELUM DIISI)"}
                                `
                            });
                        } else if (res.error === "InvalidTemplate") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: "Terjadi kesalahan saat import."
                            });
                        }
                    },
                    complete: function() {
                        $('#btnImportSubmit')
                            .prop('disabled', false)
                            .text('Simpan');
                    }
                });
            });

            // CHECK ALL
            $(document).on('click', '#checkAll', function() {
                $('.rowCheck').prop('checked', this.checked);
            });

            // UPDATE BUTTON STATE
            function updateBulkDeleteButton() {
                let count = $('.rowCheck:checked').length;

                if (count > 0) {
                    $('#btnDeleteSelected')
                        .removeClass('d-none')
                        .text(`Hapus (${count})`);
                } else {
                    $('#btnDeleteSelected').addClass('d-none');
                }
            }

            // CHECK/UNCHECK DATA
            $(document).on('change', '.rowCheck', function() {
                updateBulkDeleteButton();
            });

            // CHECK ALL
            $(document).on('change', '#checkAll', function() {
                $('.rowCheck').prop('checked', this.checked);
                updateBulkDeleteButton();
            });

            table.on('draw', function() {
                updateBulkDeleteButton();
            });

            // BULK DELETE
            $('#btnDeleteSelected').click(function() {

                let ids = [];
                $('.rowCheck:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length === 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "Tidak ada data",
                        text: "Pilih minimal satu data yang ingin dihapus."
                    });
                    return;
                }

                Swal.fire({
                    title: "Hapus Peserta Terpilih?",
                    html: `Akan menghapus <b>${ids.length}</b> peserta.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Hapus",
                    cancelButtonText: "Batal",
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('participants.bulkDelete') }}",
                            type: "POST",
                            data: {
                                ids: ids,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                $('#checkAll').prop('checked', false);
                                table.ajax.reload();

                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Peserta berhasil dihapus."
                                });
                            },
                            error: function(xhr) {
                                let msg = "Terjadi kesalahan saat menghapus peserta";

                                if (xhr.status === 404) {
                                    msg = xhr.responseJSON?.message ||
                                        "Peserta tidak ditemukan atau sudah dihapus";
                                } else if (xhr.status === 409) {
                                    msg = xhr.responseJSON?.message ||
                                        "Peserta tidak dapat dihapus karena sedang digunakan";
                                }

                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    html: msg,
                                });
                            }
                        });
                    }
                });

            });

            // DELETE ALL
            $('#btnDeleteAll').click(function() {
                Swal.fire({
                    title: "Hapus Semua Peserta?",
                    html: "Semua data peserta akan dihapus secara permanen.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Hapus Semua",
                    cancelButtonText: "Batal"
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('participants.destroyAll') }}",
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Semua peserta berhasil dihapus."
                                });
                            },
                            error: function(xhr) {
                                let msg =
                                    "Terjadi kesalahan saat menghapus semua peserta";

                                if (xhr.status === 409) {
                                    msg = xhr.responseJSON?.message ||
                                        "Peserta tidak dapat dihapus karena sedang digunakan";
                                }

                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    html: msg,
                                });
                            }
                        });
                    }
                });
            });

            $('#btnExport').click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Export Data',
                    text: 'Sedang memproses export...',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ route('participants.export') }}",
                    type: "GET",
                    success: function() {
                        Swal.close();
                        window.location.href = "{{ route('participants.export') }}";
                    },
                    error: function(xhr) {
                        Swal.close();

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: "Gagal export data peserta. Silakan coba lagi."
                        });
                    }
                });
            });
        });
    </script>
@endsection
