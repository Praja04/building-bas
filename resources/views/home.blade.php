@extends('layouts.component.main')
@section('title', 'Home')

@section('content')
    <div class="page-title-head d-flex align-items-center">
        <div class="flex-grow-1">
            <h4 class="fs-xl fw-bold m-0">Home</h4>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Kehadiran -->
            <div class="col-md-4 mb-3">
                <a href="/attendance" class="text-decoration-none">
                    <div class="card shadow-sm hover-card">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-person-check fs-1 mb-2"></i>
                            <h5 class="fw-bold">Kehadiran</h5>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Pengambilan Makan -->
            <div class="col-md-4 mb-3">
                <a href="/pengambilan-makan" class="text-decoration-none">
                    <div class="card shadow-sm hover-card">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-egg-fried fs-1 mb-2"></i>
                            <h5 class="fw-bold">Pengambilan Makan</h5>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Pengambilan Kaos -->
            <div class="col-md-4 mb-3">
                <a href="/pengambilan-kaos" class="text-decoration-none">
                    <div class="card shadow-sm hover-card">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-bag-check fs-1 mb-2"></i>
                            <h5 class="fw-bold">Pengambilan Kaos</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>

@endsection

@push('style')
    <style>
        .hover-card {
            transition: 0.2s ease;
            border-radius: 12px;
        }

        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
    </style>
@endpush
