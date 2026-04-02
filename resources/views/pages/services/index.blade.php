@extends('layouts.app')

@section('title', 'Our Services - FRYDT Lying-in Management System')

@section('css')
    <style>
        .hero-section {
            background: var(--theme-hover);
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

        .single-service .service-icon {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }

        .package-service .service-icon {
            background: linear-gradient(135deg, #2196F3, #1976D2);
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

        .badge-single {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .badge-package {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .section-title {
            position: relative;
            margin-bottom: 50px;
        }

        .section-title::after {
            content: '';
            width: 80px;
            height: 4px;
            background: var(--theme-focus);
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
            background: var(--theme-bg);
            color: white;
            padding: 60px 0;
        }

        .btn-appointment {
            background: var(--theme-bg);
            border: 2px solid white;
            color: var(--theme-text);
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-appointment:hover {
            background: white;
            color: #343539;
        }
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    {{-- <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">Our Services</h1>
                    <p class="lead mb-0">Comprehensive healthcare services for mothers and babies with professional care and
                        modern facilities</p>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="section-title display-5 fw-bold">Clinic Services</h2>
                    <p class="text-muted">We provide a comprehensive range of clinical services to ensure the health and
                        well-being of mothers and babies throughout their journey.</p>
                </div>
            </div>

            @if ($services->count() > 0)
                <div class="row g-4">
                    @php
                        // dd($services)
                    @endphp
                    @foreach ($services as $service)
                        <div class="col-lg-4 col-md-6">
                            <div class="card service-card {{ $service->type }}-service position-relative">
                                <span class="service-type-badge bg-{{ $service->type }}">
                                    {{ $service->type == 'package' ? 'Package' : 'Single Service' }}
                                </span>
                                <div class="card-body text-center p-4">
                                    <div class="service-icon">
                                        <i class="{{ getServiceIcon($service->name) }}"></i>
                                    </div>
                                    <h5 class="service-title">{{ $service->name }}</h5>
                                    <p class="service-description">{{ $service->description }}</p>

                                    @if ($service->base_price)
                                        <div class="mt-3">
                                            <span
                                                class="badge bg-light text-dark">₱{{ number_format($service->base_price, 2) }}</span>
                                            @if ($service->duration_minutes)
                                                <span
                                                    class="badge bg-light text-dark ms-2">{{ $service->duration_minutes }}
                                                    mins</span>
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
                    <p class="mb-4">Our medical professionals are here to provide you with the best care possible. Contact
                        us today to book your appointment.</p>
                    <a href="{{ route('appointment.create') ?? '#' }}" class="btn btn-appointment btn-lg">
                        <i class="fas fa-calendar-alt me-2"></i>Book Appointment
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

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
