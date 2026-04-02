@extends('layouts.app')

@section('title', 'Our Services - FRYDT Lying-in Management System')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0;
    }

    .service-card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        border-radius: 12px;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .service-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 24px;
        color: white;
    }

    .consultation-service .service-icon {
        background: linear-gradient(135deg, #4CAF50, #45a049);
    }

    .procedure-service .service-icon {
        background: linear-gradient(135deg, #FF9800, #F57C00);
    }

    .laboratory-service .service-icon {
        background: linear-gradient(135deg, #9C27B0, #7B1FA2);
    }

    .imaging-service .service-icon {
        background: linear-gradient(135deg, #2196F3, #1976D2);
    }

    .vaccination-service .service-icon {
        background: linear-gradient(135deg, #00BCD4, #0097A7);
    }

    .prenatal_care-service .service-icon {
        background: linear-gradient(135deg, #E91E63, #C2185B);
    }

    .delivery-service .service-icon {
        background: linear-gradient(135deg, #607D8B, #455A64);
    }

    .postnatal_care-service .service-icon {
        background: linear-gradient(135deg, #8BC34A, #689F38);
    }

    .other-service .service-icon {
        background: linear-gradient(135deg, #795548, #5D4037);
    }

    .service-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .service-description {
        color: #666;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .service-type-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 12px;
    }

    .badge-consultation {
        background-color: #e8f5e8;
        color: #2e7d32;
    }

    .badge-procedure {
        background-color: #fff3e0;
        color: #ef6c00;
    }

    .badge-laboratory {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }

    .badge-imaging {
        background-color: #e3f2fd;
        color: #1565c0;
    }

    .badge-vaccination {
        background-color: #e0f2f1;
        color: #00695c;
    }

    .badge-prenatal_care {
        background-color: #fce4ec;
        color: #ad1457;
    }

    .badge-delivery {
        background-color: #eceff1;
        color: #37474f;
    }

    .badge-postnatal_care {
        background-color: #e8f5e8;
        color: #2e7d32;
    }

    .badge-other {
        background-color: #efebe9;
        color: #5d4037;
    }

    .section-title {
        position: relative;
        margin-bottom: 50px;
    }

    .section-title::after {
        content: '';
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        border-radius: 2px;
    }

    .services-section {
        padding: 80px 0;
        background-color: #f8f9fa;
    }

    .cta-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 0;
    }

    .btn-appointment {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-appointment:hover {
        background: white;
        color: #667eea;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Our Medical Services</h1>
                <p class="lead mb-0">Comprehensive healthcare services for mothers and babies with professional care and modern facilities</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="section-title display-5 fw-bold">Healthcare Services</h2>
                <p class="text-muted">We provide a comprehensive range of medical services to ensure the health and well-being of mothers and babies throughout their journey.</p>
            </div>
        </div>

        @if($services->count() > 0)
            <div class="row g-4">
                @php
                    // dd($services)
                @endphp
                @foreach($services as $service)
                    <div class="col-lg-4 col-md-6">
                        <div class="card service-card {{ $service->service_type }}-service position-relative">
                            {{-- <span class="service-type-badge bg-{{ $service->service_type }}">
                                {{ ucwords(str_replace('_', ' ', $service->service_type)) }}
                            </span> --}}
                            <div class="card-body text-center p-4">
                                <div class="service-icon">
                                    <i class="{{ $this->getServiceIcon($service->name) }}"></i>
                                </div>
                                <h5 class="service-title">{{ $service->name }}</h5>
                                <p class="service-description">{{ $service->description }}</p>

                                @if($service->base_price)
                                    <div class="mt-3">
                                        <div class="mb-2">
                                            <span class="badge bg-primary text-white">₱{{ number_format($service->base_price, 2) }}</span>
                                            @if($service->philhealth_covered && $service->philhealth_price)
                                                <span class="badge bg-success text-white ms-1">PhilHealth: ₱{{ number_format($service->philhealth_price, 2) }}</span>
                                            @endif
                                        </div>
                                        @if($service->duration_minutes)
                                            <span class="badge bg-light text-dark">{{ $service->duration_minutes }} mins</span>
                                        @endif
                                        @if($service->category)
                                            <div class="mt-2">
                                                <small class="text-muted">{{ ucwords(str_replace('_', ' ', $service->category)) }}</small>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <h4>No Services Available</h4>
                        <p>We're currently updating our services. Please check back later.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h3 class="mb-4">Ready to Schedule Your Appointment?</h3>
                <p class="mb-4">Our medical professionals are here to provide you with the best care possible. Contact us today to book your appointment.</p>
                <a href="{{ route('appointment.create') ?? '#' }}" class="btn btn-appointment btn-lg">
                    <i class="fas fa-calendar-alt me-2"></i>Book Appointment
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@php
function getServiceIcon($serviceName) {
    $icons = [
        // Maternity & Delivery Services
        'Pregnancy test' => 'fas fa-vial',
        'Postpartum Checkup' => 'fas fa-user-md',
        'Normal Spontaneous Package' => 'fas fa-baby-carriage',
        'Prenatal Checkup' => 'fas fa-stethoscope',
        'Ultrasound Scan' => 'fas fa-laptop-medical',

        // Newborn Services
        'Newborn Screening Test' => 'fas fa-microscope',
        'Newborn Hearing Test' => 'fas fa-deaf',
        'Newborn Package' => 'fas fa-heartbeat',

        // Preventive Care
        'Immunization' => 'fas fa-syringe',
        'Family Planning Consultation' => 'fas fa-users',

        // Family Planning Procedures
        'DMPSA (Injectable Contraceptive)' => 'fas fa-syringe',
        'Subdermal Implant' => 'fas fa-capsules',

        // Other Services
        'Ear Piercing' => 'fas fa-gem',
    ];

    return $icons[$serviceName] ?? 'fas fa-medkit';
}
@endphp
