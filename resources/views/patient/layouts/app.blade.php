<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FRYDT Patient Portal')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        :root {
            --primary-color: #dcffdc;
            --primary-dark: #4ade80;
            --secondary-color: #6c757d;
            --success-color: #22c55e;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --text-dark: #1f2937;
            --text-light: #6b7280;
        }

        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .upsidebar{
            background: linear-gradient(180deg, var(--primary-dark) 0%, #16a34a 100%);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            position: sticky;
            top: 76px;
            /*min-height: 100vh;*/

            z-index: 100;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: 1px solid rgba(34, 197, 94, 0.1);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(34, 197, 94, 0.08);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #16a34a 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 15px 20px;
        }

        .stat-card {
            text-align: center;
            padding: 20px;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #16a34a 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(34, 197, 94, 0.3);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--primary-dark);
            color: white;
            border: none;
            font-weight: 500;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #22c55e 50%, #16a34a 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.15);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        /* Mobile Navigation Styles */
        .offcanvas .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .offcanvas .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .offcanvas .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                position: static;
            }

            .main-content {
                padding: 15px;
                margin-top: 20px;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .card {
                margin-bottom: 20px;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .table-responsive {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 10px;
            }

            .welcome-section {
                padding: 20px;
                margin-bottom: 20px;
            }

            .card-header {
                padding: 12px 15px;
            }

            .card-body {
                padding: 15px;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-card .stat-number {
                font-size: 1.5rem;
            }
        }

        /* Ensure proper scrolling */
        .main-content {
            min-height: calc(100vh - 76px);
        }

        /* Fix for mobile sidebar */
        @media (max-width: 767.98px) {
            .offcanvas {
                width: 280px !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top"
        style="background: linear-gradient(135deg, var(--primary-dark) 0%, #16a34a 100%);">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('patient.dashboard') }}">
                <i class="fas fa-heartbeat me-2"></i>FRYDT Patient Portal
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropstart">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            @if(auth()->user()->patientProfile && auth()->user()->patientProfile->image_path)
                                <img src="{{ asset('storage/app/public/' . auth()->user()->patientProfile->image_path) }}"
                                     alt="Profile" class="rounded-circle me-2"
                                     style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <i class="fas fa-user-circle me-2"></i>
                            @endif
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('patient.profile') }}">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 d-none d-md-block upsidebar">
                <div class="sidebar">
                    <nav class="nav flex-column pt-4">
                        <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}"
                            href="{{ route('patient.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.appointments*') ? 'active' : '' }}"
                            href="{{ route('patient.appointments') }}">
                            <i class="fas fa-calendar-check me-2"></i>My Appointments
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.book-appointment') ? 'active' : '' }}"
                            href="{{ route('patient.book-appointment') }}">
                            <i class="fas fa-plus-circle me-2"></i>Book Appointment
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.payments*') ? 'active' : '' }}"
                            href="{{ route('patient.payments.index') }}">
                            <i class="fas fa-receipt me-2"></i>Payments
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.billing*') ? 'active' : '' }}"
                            href="{{ route('patient.billing') }}">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Billing
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.medical-records') ? 'active' : '' }}"
                            href="{{ route('patient.medical-records') }}">
                            <i class="fas fa-notes-medical me-2"></i>Medical Records
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.laboratory-results*') ? 'active' : '' }}"
                            href="{{ route('patient.laboratory-results') }}">
                            <i class="fas fa-flask me-2"></i>Lab Results
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.documents') ? 'active' : '' }}"
                            href="{{ route('patient.documents') }}">
                            <i class="fas fa-file-alt me-2"></i>Documents
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.profile') ? 'active' : '' }}"
                            href="{{ route('patient.profile') }}">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Mobile Sidebar Toggle -->
            <div class="d-md-none">
                <button class="btn position-fixed" style="top: 80px; left: 10px; z-index: 1050; background: linear-gradient(135deg, var(--primary-dark) 0%, #16a34a 100%); color: white; border: none; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Sidebar -->
            <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar">
                <div class="offcanvas-header" style="background: linear-gradient(180deg, var(--primary-dark) 0%, #16a34a 100%); color: white;">
                    <h5 class="offcanvas-title">Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body" style="background: linear-gradient(180deg, var(--primary-dark) 0%, #16a34a 100%);">
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}"
                            href="{{ route('patient.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.appointments*') ? 'active' : '' }}"
                            href="{{ route('patient.appointments') }}">
                            <i class="fas fa-calendar-check me-2"></i>My Appointments
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.book-appointment') ? 'active' : '' }}"
                            href="{{ route('patient.book-appointment') }}">
                            <i class="fas fa-plus-circle me-2"></i>Book Appointment
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.payments*') ? 'active' : '' }}"
                            href="{{ route('patient.payments.index') }}">
                            <i class="fas fa-receipt me-2"></i>Payments
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.profile') ? 'active' : '' }}"
                            href="{{ route('patient.profile') }}">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9 col-12">
                <div class="main-content">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Logout Modal -->
        @include('partials.logout-modal')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Show flash messages as toasts
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>

    @stack('scripts')
</body>

</html>
