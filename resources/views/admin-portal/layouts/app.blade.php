<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="max-height: 100%">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Portal</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        /* Admin Portal Theme */
        :root {
            --admin-primary: #4ade80;
            --admin-secondary: #16a34a;
            --admin-success: #22c55e;
            --admin-danger: #dc3545;
            --admin-warning: #ffc107;
            --admin-info: #17a2b8;
            --admin-light: #f0fdf4;
            --admin-dark: #1f2937;
        }

        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            font-family: 'Nunito', sans-serif;
        }

        /* Admin Navigation */
        .admin-nav {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .admin-nav .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .admin-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .admin-nav .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .admin-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        /* Main content */
        .admin-content {
            min-height: calc(100vh - 76px);
            padding: 2rem 1rem;
        }

        /* Page headers */
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .page-title {
            color: var(--admin-primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        /* Content sections */
        .content-section {
            padding: 1.5rem;
            padding-bottom: 0px;
        }

        .section-header {
            margin-bottom: 1.5rem;
        }

        .section-title {
            color: var(--admin-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
        }

        .section-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        /* Cards */
        .admin-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        /* Stats cards */
        .stats-card {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-card .card-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }

        .stats-card .card-title {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .stats-card .card-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }

        /* Tables */
        .admin-table {
            margin-bottom: 0;
        }

        .admin-table th {
            background-color: var(--admin-primary);
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-table td {
            border-color: #dee2e6;
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }

        .admin-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Table containers */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        /* Card headers */
        .admin-card .card-header {
            background-color: var(--admin-primary);
            color: white;
            border: none;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .admin-card .card-body {
            padding: 1.5rem;
        }

        /* Form elements */
        .form-label {
            font-weight: 600;
            color: var(--admin-primary);
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-admin-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
            color: white;
        }

        .btn-admin-primary:hover {
            background-color: var(--admin-secondary);
            border-color: var(--admin-secondary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Filter and search sections */
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-section .form-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Empty states */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-state .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-state .empty-text {
            font-size: 1rem;
            margin-bottom: 0;
        }

        /* Status badges */
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: white;
        }

        /* Sidebar */
        .admin-sidebar {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .admin-sidebar .nav-link {
            color: var(--admin-primary) !important;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            margin-bottom: 0.25rem;
        }

        .admin-sidebar .nav-link:hover {
            background-color: var(--admin-light);
        }

        .admin-sidebar .nav-link.active {
            background-color: var(--admin-primary);
            color: white !important;
        }

        /* Buttons */
        .btn-admin-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
            color: white;
        }

        .btn-admin-primary:hover {
            background-color: var(--admin-secondary);
            border-color: var(--admin-secondary);
            color: white;
        }
    </style>
</head>

<body>
    <div id="app">
        <!-- Admin Navigation -->
        <nav class="navbar navbar-expand-lg admin-nav">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('admin-portal.dashboard') }}">
                    <i class="fas fa-cog me-2"></i>Admin Portal
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.dashboard') ? 'active' : '' }}"
                               href="{{ route('admin-portal.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.users*') ? 'active' : '' }}"
                               href="{{ route('admin-portal.users') }}">
                                <i class="fas fa-users me-1"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.services*') ? 'active' : '' }}"
                               href="{{ route('admin-portal.services') }}">
                                <i class="fas fa-stethoscope me-1"></i>Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.patients*') ? 'active' : '' }}"
                               href="{{ route('admin-portal.patients') }}">
                                <i class="fas fa-user-injured me-1"></i>Patients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.payroll*') ? 'active' : '' }}"
                               href="{{ route('admin-portal.payroll') }}">
                                <i class="fas fa-money-check me-1"></i>Payroll
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.appointments*') ? 'active' : '' }}"
                               href="{{ route('admin-portal.appointments') }}">
                                <i class="fas fa-calendar-check me-1"></i>Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.attendance') ? 'active' : '' }}"
                               href="{{ route('admin-portal.attendance') }}">
                                <i class="fas fa-clock me-1"></i>Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.clock-in-out') ? 'active' : '' }}"
                               href="{{ route('admin-portal.clock-in-out') }}">
                                <i class="fas fa-fingerprint me-1"></i>Time In/Out
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.documents*') ? 'active' : '' }}"
                               href="{{ route('admin-portal.documents') }}">
                                <i class="fas fa-file-alt me-1"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.medical-records') ? 'active' : '' }}"
                               href="{{ route('admin-portal.medical-records') }}">
                                <i class="fas fa-file-medical me-1"></i>Medical Records
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.reports') ? 'active' : '' }}"
                               href="{{ route('admin-portal.reports') }}">
                                <i class="fas fa-chart-bar me-1"></i>Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.sms-logs') ? 'active' : '' }}"
                               href="{{ route('admin-portal.sms-logs') }}">
                                <i class="fas fa-sms me-1"></i>SMS Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.settings') ? 'active' : '' }}"
                               href="{{ route('admin-portal.settings') }}">
                                <i class="fas fa-cogs me-1"></i>Settings
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <form method="POST" action="{{ route('backpack.auth.logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light nav-link">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div> --}}
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav me-auto">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-portal.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin-portal.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>

                        <!-- Management Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin-portal.users*') || request()->routeIs('admin-portal.services*') || request()->routeIs('admin-portal.inventory*') || request()->routeIs('admin-portal.schedules*') || request()->routeIs('admin-portal.patients*') || request()->routeIs('admin-portal.payroll*') ? 'active' : '' }}"
                                href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-briefcase me-1"></i>Management
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="managementDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin-portal.users') }}"><i
                                            class="fas fa-users me-1"></i>Users</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.services') }}"><i
                                            class="fas fa-stethoscope me-1"></i>Services</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.inventory') }}"><i
                                            class="fas fa-boxes me-1"></i>Inventory</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.schedules') }}"><i
                                            class="fas fa-calendar-alt me-1"></i>Employee Schedule</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.employee-deductions') }}"><i
                                            class="fas fa-calendar-alt me-1"></i>Employee Deductions</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.mandatory-deductions') }}"><i
                                            class="fas fa-calendar-alt me-1"></i>Mandatory Deductions</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.patients') }}"><i
                                            class="fas fa-user-injured me-1"></i>Patients</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.payroll') }}"><i
                                            class="fas fa-money-check me-1"></i>Payroll</a></li>
                            </ul>
                        </li>

                        <!-- Operations Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin-portal.appointments*') || request()->routeIs('admin-portal.attendance') || request()->routeIs('admin-portal.clock-in-out') || request()->routeIs('admin-portal.payments*') ? 'active' : '' }}"
                                href="#" id="operationsDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-tasks me-1"></i>Operations
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="operationsDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin-portal.appointments') }}"><i
                                            class="fas fa-calendar-check me-1"></i>Appointments</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.payments') }}"><i
                                            class="fas fa-credit-card me-1"></i>Payments</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.attendance') }}"><i
                                            class="fas fa-clock me-1"></i>Attendance</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.clock-in-out') }}"><i
                                            class="fas fa-fingerprint me-1"></i>Time In/Out</a></li>
                            </ul>
                        </li>

                        <!-- Records Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin-portal.documents*') || request()->routeIs('admin-portal.medical-records') ? 'active' : '' }}"
                                href="#" id="recordsDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-folder-open me-1"></i>Records
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="recordsDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin-portal.documents') }}"><i
                                            class="fas fa-file-alt me-1"></i>Documents</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.medical-records') }}"><i
                                            class="fas fa-file-medical me-1"></i>Medical Records</a></li>
                            </ul>
                        </li>

                        <!-- System Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin-portal.reports') || request()->routeIs('admin-portal.sms-logs') || request()->routeIs('admin-portal.settings') ? 'active' : '' }}"
                                href="#" id="systemDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-cogs me-1"></i>System
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="systemDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin-portal.reports') }}"><i
                                            class="fas fa-chart-bar me-1"></i>Reports</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.sms-logs') }}"><i
                                            class="fas fa-sms me-1"></i>SMS Logs</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin-portal.settings') }}"><i
                                            class="fas fa-cogs me-1"></i>Settings</a></li>
                            </ul>
                        </li>
                    </ul>

                    <!-- Logout -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <button type="button" class="btn btn-outline-light nav-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </button>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="container-fluid">
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

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

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

        // Reusable functions for modals and toasts
        window.AdminUtils = {
            // Show confirmation modal
            confirmDelete: function(title, message, confirmCallback) {
                const modalHtml = `
                    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteConfirmModalLabel">${title}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ${message}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if present
                const existingModal = document.getElementById('deleteConfirmModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Add modal to body
                document.body.insertAdjacentHTML('beforeend', modalHtml);

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                modal.show();

                // Handle confirm button
                document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                    modal.hide();
                    if (confirmCallback && typeof confirmCallback === 'function') {
                        confirmCallback();
                    }
                });

                // Clean up modal after hiding
                document.getElementById('deleteConfirmModal').addEventListener('hidden.bs.modal', function() {
                    this.remove();
                });
            },

            // Toast notifications
            showSuccess: function(message) {
                toastr.success(message);
            },

            showError: function(message) {
                toastr.error(message);
            },

            showWarning: function(message) {
                toastr.warning(message);
            },

            showInfo: function(message) {
                toastr.info(message);
            }
        };

        // Show flash messages as toasts
        @if(session('success'))
            AdminUtils.showSuccess("{{ session('success') }}");
        @endif

        @if(session('error'))
            AdminUtils.showError("{{ session('error') }}");
        @endif

        @if(session('warning'))
            AdminUtils.showWarning("{{ session('warning') }}");
        @endif

        @if(session('info'))
            AdminUtils.showInfo("{{ session('info') }}");
        @endif
    </script>

    @yield('scripts')
    @yield('script')
</body>

</html>
