@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section
  class="hero-section d-flex align-items-center justify-content-center text-center"
  style="
    background: linear-gradient(
        to right,
        rgba(255, 255, 255, 0.8),
        rgba(255, 255, 255, 0.8)
      ),
      url('{{ asset('images/homepage.png') }}') no-repeat center center;
    background-size: cover;
    min-height: 80vh;
  "
>
  <div class="container mb-3">
    <div class="col-12 col-md-8 mx-auto px-3 px-md-auto">
      <h1 class="display-6 fw-bold text-dark mb-4 text-nowrap text-center">
        Welcome to Frydt Lying-in Clinic
      </h1>
      <p class="lead text-dark mb-4 fs-5">
        Your trusted partner in maternal and child healthcare. We provide
        compassionate care throughout your journey to motherhood with expertise
        and dedication.
      </p>
      <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
        <a
          href="{{ route('backpack.auth.login') }}"
          class="btn btn-theme px-4 py-3 rounded-pill shadow text-uppercase fw-bold"
        >
          Login Portal
        </a>
      </div>
    </div>
  </div>
</section>


    <!-- Services Section -->
    <section class="services-section py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-theme">Our Services</h2>
                    <p class="lead text-muted">Comprehensive healthcare services tailored for mothers and children</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-baby text-primary fs-1 mb-3"></i>
                            <h5 class="card-title fw-bold">Prenatal Care</h5>
                            <p class="card-text text-muted">Comprehensive prenatal check-ups and monitoring for a healthy pregnancy journey.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-heartbeat text-danger fs-1 mb-3"></i>
                            <h5 class="card-title fw-bold">Delivery Services</h5>
                            <p class="card-text text-muted">Safe and professional delivery services with experienced medical staff.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-stethoscope text-success fs-1 mb-3"></i>
                            <h5 class="card-title fw-bold">Postnatal Care</h5>
                            <p class="card-text text-muted">Supportive care for mothers and newborns after delivery.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('services.index') }}" class="btn btn-theme btn-lg px-4 py-3 rounded-pill">
                    <i class="fas fa-list me-2"></i>View All Services
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section py-5 border-top border-1">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold text-theme mb-4">Why Choose Frydt Clinic?</h2>
                    <p class="lead text-muted mb-4">
                        With years of experience in maternal healthcare, we prioritize your comfort, safety, and well-being. Our dedicated team of healthcare professionals ensures personalized care for every patient.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-3"></i>24/7 Emergency Care</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-3"></i>Experienced Medical Staff</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-3"></i>Facilities</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-3"></i>Compassionate Care</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Frydt Clinic Logo" class="img-fluid rounded d-flex justify-content-center m-auto" style="max-width: 400px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-theme">Get In Touch</h2>
                    <p class="lead text-muted">Ready to start your healthcare journey? Contact us today.</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0">
                        <div class="card-body p-4">
                            <div class="row text-center">
                                <div class="col-md-4 mb-4">
                                    <i class="fas fa-phone text-primary fs-2 mb-3"></i>
                                    <h6 class="fw-bold">Call Us</h6>
                                    <p class="text-muted mb-0">450-65-84</p>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <i class="fas fa-envelope text-primary fs-2 mb-3"></i>
                                    <h6 class="fw-bold">Email Us</h6>
                                    <p class="text-muted mb-0">frydtlyingin@gmail.com</p>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <i class="fas fa-map-marker-alt text-primary fs-2 mb-3"></i>
                                    <h6 class="fw-bold">Visit Us</h6>
                                    <p class="text-muted mb-0">South Centro, Sipocot, Camarines Sur</p>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <a href="{{ url('/contact-us') }}" class="btn btn-theme btn-lg px-4 py-3 rounded-pill">
                                    Contact Us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
.hero-section {
    position: relative;
}
.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    z-index: 1;
}
.hero-section .container-fluid {
    position: relative;
    z-index: 2;
}
.services-section .card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}
</style>
@endpush
