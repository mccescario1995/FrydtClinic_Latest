<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Account Created - FRYDT Patient Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirmation-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.1);
            max-width: 600px;
            width: 100%;
            padding: 3rem;
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            color: var(--success-color);
            margin-bottom: 2rem;
        }

        .welcome-title {
            color: var(--primary-dark);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .account-type {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #16a34a 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .confirmation-text {
            color: var(--text-dark);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .next-steps {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .next-steps h4 {
            color: var(--primary-dark);
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
            color: var(--success-color);
            margin-right: 0.5rem;
            width: 20px;
        }

        .btn-dashboard {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #16a34a 100%);
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
            color: white;
            text-decoration: none;
        }

        .contact-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .contact-info i {
            color: var(--primary-dark);
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
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1 class="welcome-title">Welcome to FRYDT!</h1>

        <div class="account-type">
            <i class="fas fa-user-injured me-2"></i>Patient Account Created Successfully
        </div>

        <div class="confirmation-text">
            <p>Thank you for registering with FRYDT Clinic Management System. Your patient account has been created successfully and you can now access all patient portal features.</p>
        </div>

        <div class="next-steps">
            <h4><i class="fas fa-list-check me-2"></i>What you can do next:</h4>
            <ul>
                <li><i class="fas fa-calendar-plus"></i> <strong>Book Appointments:</strong> Schedule appointments with our healthcare providers</li>
                <li><i class="fas fa-file-medical"></i> <strong>Access Medical Records:</strong> View your medical history and test results</li>
                <li><i class="fas fa-credit-card"></i> <strong>Manage Payments:</strong> View and pay for your medical services</li>
                <li><i class="fas fa-user-edit"></i> <strong>Update Profile:</strong> Complete your patient profile information</li>
                <li><i class="fas fa-comments"></i> <strong>Get Support:</strong> Contact our support team if you need assistance</li>
            </ul>
        </div>

        <a href="{{ route('patient.dashboard') }}" class="btn-dashboard">
            <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
        </a>

        <div class="contact-info">
            <p><i class="fas fa-envelope"></i> Need help? Contact our support team</p>
            <p><i class="fas fa-phone"></i> Emergency: Call our clinic directly</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
