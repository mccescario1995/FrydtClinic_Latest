<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="max-height: 100%">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Account Created - Admin Portal</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirmation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            width: 100%;
            padding: 3rem;
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            color: var(--admin-success);
            margin-bottom: 2rem;
        }

        .welcome-title {
            color: var(--admin-primary);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .account-type {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .confirmation-text {
            color: var(--admin-dark);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .account-details {
            background: var(--admin-light);
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .account-details h4 {
            color: var(--admin-primary);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--admin-dark);
        }

        .detail-value {
            color: var(--admin-secondary);
            font-weight: 500;
        }

        .next-steps {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .next-steps h4 {
            color: var(--admin-primary);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .next-steps ul {
            list-style: none;
            padding: 0;
        }

        .next-steps li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .next-steps li:last-child {
            border-bottom: none;
        }

        .next-steps li i {
            color: var(--admin-success);
            margin-right: 0.5rem;
            width: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-admin-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
            color: white;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-admin-primary:hover {
            background-color: var(--admin-secondary);
            border-color: var(--admin-secondary);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-outline-secondary {
            border: 2px solid var(--admin-secondary);
            color: var(--admin-secondary);
            background: transparent;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background-color: var(--admin-secondary);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .contact-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .contact-info i {
            color: var(--admin-primary);
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            .confirmation-card {
                margin: 1rem;
                padding: 2rem;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .success-icon {
                font-size: 4rem;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-admin-primary,
            .btn-outline-secondary {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="confirmation-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1 class="welcome-title">Account Created Successfully!</h1>

        <div class="account-type">
            @if($user->user_type === 'admin')
                <i class="fas fa-cog me-2"></i>Administrator Account
            @elseif ($user->user_type === 'employee')
                <i class="fas fa-user-md me-2"></i>Employee Account
            @elseif ($user->user_type === 'patient')
                <i class="fas fa-user-md me-2"></i>Patient Account
            @endif
        </div>

        <div class="confirmation-text">
            <p>The {{ $user->user_type }} account for <strong>{{ $user->name }}</strong> has been created successfully. The account is now active and ready to use.</p>
        </div>

        <div class="account-details">
            <h4><i class="fas fa-user-check me-2"></i>Account Details</h4>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $user->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $user->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">User Type:</span>
                <span class="detail-value">{{ ucfirst($user->user_type) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="badge bg-success">Active</span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Created:</span>
                <span class="detail-value">{{ $user->created_at->format('M d, Y H:i') }}</span>
            </div>
        </div>

        <div class="next-steps">
            <h4><i class="fas fa-list-check me-2"></i>Next Steps</h4>
            <ul>
                @if($user->user_type === 'employee')
                    <li><i class="fas fa-key"></i> <strong>Set PIN:</strong> The employee should set up their PIN for attendance tracking</li>
                    <li><i class="fas fa-calendar-alt"></i> <strong>Schedule Setup:</strong> Configure work schedules for the employee</li>
                    <li><i class="fas fa-clock"></i> <strong>Attendance:</strong> Employee can now time in/out using their PIN</li>
                @endif
                <li><i class="fas fa-sign-in-alt"></i> <strong>Login:</strong> Account can login using email and password</li>
                <li><i class="fas fa-user-edit"></i> <strong>Profile Setup:</strong> Complete profile information and preferences</li>
                <li><i class="fas fa-envelope"></i> <strong>Email Verification:</strong> Check email for any verification instructions</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="{{ route('admin-portal.users.show', $user->id) }}" class="btn-admin-primary">
                <i class="fas fa-eye me-2"></i>View Account
            </a>
            <a href="{{ route('admin-portal.users') }}" class="btn-outline-secondary">
                <i class="fas fa-users me-2"></i>Back to Users
            </a>
        </div>

        <div class="contact-info">
            <p><i class="fas fa-info-circle"></i> If you need to modify account settings or permissions, visit the user management section.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
    </script>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>
