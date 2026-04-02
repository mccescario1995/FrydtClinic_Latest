@extends('layouts.app')

@section('title', $service->name . ' - FRYDT Lying-in Management System')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Services</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $service->name }}</li>
                    </ol>
                </nav>

                <!-- Service Details Card -->
                <div class="card shadow-lg border-0 mb-5">
                    <div class="card-header bg-gradient text-white text-center py-4"
                        style="background: linear-gradient(135deg, {{ $service->type == 'package' ? '#2196F3, #1976D2' : '#4CAF50, #45a049' }});">
                        <div class="service-icon mx-auto mb-3"
                            style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="{{ $this->getServiceIcon($service->name) }}" style="font-size: 2rem;"></i>
                        </div>
                        <h1 class="h2 mb-2">{{ $service->name }}</h1>
                        <span class="badge bg-light text-dark">{{ ucfirst($service->type) }}</span>
                    </div>

                    <div class="card-body p-5">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="mb-3">Service Description</h4>
                                <p class="lead text-muted mb-4">{{ $service->description }}</p>
                            </div>

                            <div class="col-md-4">
                                <div class="bg-light p-4 rounded">
                                    <h5 class="mb-3">Service Details</h5>

                                    @if ($service->base_price)
                                        <div class="mb-3">
                                            <strong>Price:</strong>
                                            <span class="float-end">₱{{ number_format($service->base_price, 2) }}</span>
                                        </div>
                                    @endif

                                    @if ($service->duration_minutes)
                                        <div class="mb-3">
                                            <strong>Duration:</strong>
                                            <span class="float-end">{{ $service->duration_minutes }} minutes</span>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <strong>Type:</strong>
                                        <span class="float-end">{{ ucfirst($service->type) }}</span>
                                    </div>

                                    <hr>

                                    <div class="d-grid">
                                        <a href="{{ route('appointment.create') }}?service_id={{ $service->id }}"
                                            class="btn btn-primary btn-lg">
                                            <i class="fas fa-calendar-alt me-2"></i>Book Appointment
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Services -->
                @if ($relatedServices->count() > 0)
                    <div class="mb-5">
                        <h3 class="mb-4">Related {{ ucfirst($service->type) }} Services</h3>
                        <div class="row g-3">
                            @foreach ($relatedServices as $relatedService)
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $relatedService->name }}</h6>
                                            <p class="card-text small">{{ Str::limit($relatedService->description, 80) }}
                                            </p>
                                            @if ($relatedService->price)
                                                <p class="small mb-2">
                                                    <strong>₱{{ number_format($relatedService->price, 2) }}</strong></p>
                                            @endif
                                            <a href="{{ route( 'services.show', $relatedService->id) }}"
                                                class="btn btn-outline-primary btn-sm">Learn More</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Back to Services -->
                <div class="text-center">
                    <a href="{{ route('services.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to All Services
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
        function getServiceIcon($serviceName)
        {
            $icons = [
                'Pregnancy test' => 'fas fa-vial',
                'Postpartum Checkup' => 'fas fa-user-md',
                'Normal Spontaneous Package' => 'fas fa-baby',
                'Prenatal Checkup' => 'fas fa-stethoscope',
                'Ultrasound Scan' => 'fas fa-laptop-medical',
                'Newborn Screening Test' => 'fas fa-microscope',
                'Newborn Hearing Test' => 'fas fa-deaf',
                'Newborn Package' => 'fas fa-heart',
                'Immunization' => 'fas fa-syringe',
                'Ear Piercing' => 'fas fa-gem',
                'Family Planning Consultation' => 'fas fa-users',
                'DMPSA (Injectable Contraceptive)' => 'fas fa-syringe',
                'Subdermal Implant' => 'fas fa-capsules',
            ];

            return $icons[$serviceName] ?? 'fas fa-medical-kit';
        }
    @endphp
@endsection
