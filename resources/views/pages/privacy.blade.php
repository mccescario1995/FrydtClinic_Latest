@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Privacy Policy</h1>

            <div class="card">
                <div class="card-body">
                    <h2>1. Introduction</h2>
                    <p>At Frydt Lying-in Clinic, we are committed to protecting your privacy and ensuring the security of your personal information. This privacy policy explains how we collect, use, and protect your data.</p>

                    <h2>2. Information We Collect</h2>
                    <p>We collect information you provide directly to us, such as when you register an account, book appointments, or contact us. This may include:</p>
                    <ul>
                        <li>Personal information (name, email, phone number, address)</li>
                        <li>Medical information (health history, appointment details)</li>
                        <li>Payment information</li>
                        <li>Communication records</li>
                    </ul>

                    <h2>3. How We Use Your Information</h2>
                    <p>We use your information to:</p>
                    <ul>
                        <li>Provide healthcare services</li>
                        <li>Schedule and manage appointments</li>
                        <li>Process payments</li>
                        <li>Communicate with you about your care</li>
                        <li>Improve our services</li>
                        <li>Comply with legal obligations</li>
                    </ul>

                    <h2>4. Information Sharing</h2>
                    <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy or required by law.</p>

                    <h2>5. Data Security</h2>
                    <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

                    <h2>6. Your Rights</h2>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access your personal information</li>
                        <li>Correct inaccurate information</li>
                        <li>Request deletion of your information</li>
                        <li>Withdraw consent for processing</li>
                        <li>Lodge a complaint with supervisory authorities</li>
                    </ul>

                    <h2>7. Cookies and Tracking</h2>
                    <p>Our website may use cookies to enhance your experience. You can control cookie settings through your browser preferences.</p>

                    <h2>8. Changes to This Policy</h2>
                    <p>We may update this privacy policy from time to time. Changes will be posted on this page with an updated effective date.</p>

                    <h2>9. Contact Us</h2>
                    <p>If you have questions about this privacy policy or our data practices, please contact us through our website or visit our clinic.</p>

                    <p class="mt-4"><em>Last updated: {{ date('F j, Y') }}</em></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
