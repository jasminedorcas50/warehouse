<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Healthcare Warehouse</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .hero-section {
            background: linear-gradient(135deg, #4CAF50 0%, #2196F3 100%);
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .btn-custom {
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-login {
            background-color: #4CAF50;
            border: none;
            color: white;
        }
        .btn-register {
            background-color: transparent;
            border: 2px solid white;
            color: white;
        }
        .btn-login:hover, .btn-register:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Healthcare Warehouse</a>
            <div class="ms-auto">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-custom btn-login">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-custom btn-login me-2">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-custom btn-register">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Welcome to Healthcare Warehouse</h1>
            <p class="lead mb-5">Your comprehensive solution for managing healthcare inventory and supplies efficiently</p>
            <div class="d-flex justify-content-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-custom btn-primary-custom">Get Started</a>
                    <a href="{{ route('register') }}" class="btn btn-custom btn-secondary-custom">Learn More</a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mb-5">
        <div class="row">
            <div class="col-md-4">
                <div class="feature-card">
                    <h3 class="h5 mb-3">Inventory Management</h3>
                    <p class="text-muted">Efficiently track and manage your healthcare supplies with our advanced inventory system.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <h3 class="h5 mb-3">Real-time Analytics</h3>
                    <p class="text-muted">Get instant insights into your inventory levels, usage patterns, and supply chain metrics.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <h3 class="h5 mb-3">Secure & Reliable</h3>
                    <p class="text-muted">Your data is protected with enterprise-grade security and reliable backup systems.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-4 mt-5">
        <div class="container text-center">
            <p class="text-muted mb-0">&copy; {{ date('Y') }} Healthcare Warehouse. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
