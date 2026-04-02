<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="max-height: 100%">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Employee Portal</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <link rel="stylesheet" href="/css/colors.css">

    <!-- Scripts -->
    @yield('css')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Employee Portal Theme */
        :root {
            --employee-primary: #4ade80;
            --employee-secondary: #6c757d;
            --employee-success: #22c55e;
            --employee-danger: #dc3545;
            --employee-warning: #ffc107;
            --employee-info: #17a2b8;
            --employee-light: #f8f9fa;
            --employee-dark: #1f2937;
        }

        body {
            background-color: #f5f5f5;
        }

        /* Employee Navigation */
        .employee-nav {
            background: linear-gradient(135deg, var(--employee-primary) 0%, #16a34a 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .employee-nav .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.25rem;
        }

        .employee-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .employee-nav .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }

        .employee-nav .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            border-radius: 5px;
        }

        /* Logout button */
        .employee-nav .logout-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            color: white !important;
        }

        .employee-nav .logout-btn:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            color: white !important;
        }

        /* Main content */
        .employee-content {
            min-height: calc(100vh - 76px);
            padding: 2rem 0;
        }

        /* Cards */
        .employee-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .employee-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* Buttons */
        .btn-employee-primary {
            background-color: var(--employee-primary);
            border-color: var(--employee-primary);
            color: white;
        }

        .btn-employee-primary:hover {
            background-color: #16a34a;
            border-color: #16a34a;
            color: white;
        }

        /* Tables */
        .employee-table th {
            background-color: var(--employee-primary);
            color: white;
            border: none;
            font-weight: 600;
        }

        .employee-table td {
            border-color: #dee2e6;
            vertical-align: middle;
        }

        /* Status badges */
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Quick actions */
        .quick-actions {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
        }

        /* Welcome section */
        .welcome-section {
            background: linear-gradient(135deg, var(--employee-primary) 0%, #22c55e 50%, #16a34a 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .welcome-section h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }

        .welcome-section p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
    </style>
</head>

<body class="mh-100">
    <div id="app" class="mh-100">

        <!-- Employee Navigation -->
        <nav class="navbar navbar-expand-lg employee-nav sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ route('employee.dashboard') }}">
                    🏥 {{ config('app.name', 'Clinic') }} - Employee Portal
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#employeeNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="employeeNavbar" style="visibility: visible !important;">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}"
                               href="{{ route('employee.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.patients*') ? 'active' : '' }}"
                               href="{{ route('employee.patients') }}">
                                <i class="fas fa-users me-1"></i>Patients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.schedule') ? 'active' : '' }}"
                               href="{{ route('employee.schedule') }}">
                                <i class="fas fa-calendar me-1"></i>Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.appointments') ? 'active' : '' }}"
                               href="{{ route('employee.appointments') }}">
                                <i class="fas fa-calendar-check me-1"></i>Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.attendance') ? 'active' : '' }}"
                               href="{{ route('employee.attendance') }}">
                                <i class="fas fa-clock me-1"></i>Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.payments*') ? 'active' : '' }}"
                               href="{{ route('employee.payments') }}">
                                <i class="fas fa-credit-card me-1"></i>Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.payroll') ? 'active' : '' }}"
                               href="{{ route('employee.payroll') }}">
                                <i class="fas fa-money-check me-1"></i>Payroll
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.profile') ? 'active' : '' }}"
                               href="{{ route('employee.profile') }}">
                                <i class="fas fa-user me-1"></i>Profile
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="employeeDropdown" role="button"
                                data-bs-toggle="dropdown">
                                @if(backpack_user()->employeeProfile && backpack_user()->employeeProfile->image_path)
                                    <img src="{{ asset('storage/app/public/' . backpack_user()->employeeProfile->image_path) }}"
                                         alt="Profile" class="rounded-circle me-2"
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle me-2"></i>
                                @endif
                                {{ backpack_user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('employee.profile') }}">
                                        <i class="fas fa-user me-2"></i>Profile
                                    </a></li>
                                <li><hr class="dropdown-divider"></li>
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

        <!-- Main Content -->
        <main class="employee-content">
            <div class="container">
                @yield('content')
            </div>
        </main>

        <!-- Logout Modal -->
        @include('partials.logout-modal')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
    </script>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

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

    @yield('scripts')
</body>
</html>
