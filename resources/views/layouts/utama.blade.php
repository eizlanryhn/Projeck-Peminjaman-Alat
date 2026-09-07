<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Peminjaman Alat Laboratorium')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Modern Styling -->
    <style>
        :root {
            --bs-font-sans-serif: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            --accent-gradient: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            --card-hover-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.1);
        }

        body {
            font-family: var(--bs-font-sans-serif);
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-wrapper {
            flex: 1;
        }

        /* Modern Card Styling */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            background: #ffffff;
            transition: all 0.25s ease-in-out;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-hover-shadow);
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 600;
            padding: 1.1rem 1.4rem;
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }

        /* Buttons & Forms */
        .btn {
            border-radius: 0.6rem;
            font-weight: 500;
            padding: 0.55rem 1.15rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .form-control, .form-select {
            border-radius: 0.6rem;
            border-color: #cbd5e1;
            padding: 0.6rem 0.9rem;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Modern Badges */
        .badge {
            font-weight: 600;
            letter-spacing: 0.3px;
            padding: 0.45em 0.85em;
            border-radius: 9999px;
        }

        /* Modern Navbar Styling */
        .navbar-custom {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .navbar-custom .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 1.25rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-custom .nav-link {
            color: #cbd5e1;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.5rem 0.85rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Stat Card Gradient Bars */
        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-primary::before { background: var(--primary-gradient); }
        .stat-success::before { background: var(--success-gradient); }
        .stat-warning::before { background: var(--warning-gradient); }
        .stat-danger::before { background: var(--danger-gradient); }
        .stat-accent::before { background: var(--accent-gradient); }

        .stat-icon-wrapper {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
        }

        /* Modern Tables */
        .table {
            vertical-align: middle;
        }

        .table thead th {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.9rem 1rem;
        }

        .table tbody td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Footer */
        .footer-custom {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.875rem;
            padding: 1.5rem 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')

    <div class="main-wrapper">
        <div class="container py-4">
            @if (session('sukses'))
                <div class="alert alert-success d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>{{ session('sukses') }}</div>
                </div>
            @endif

            @if (session('gagal'))
                <div class="alert alert-danger d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>{{ session('gagal') }}</div>
                </div>
            @endif

            @yield('konten')
        </div>
    </div>

    <!-- Onboarding Modal Component -->
    @auth
        <x-onboarding-modal />
    @endauth

    <footer class="footer-custom text-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start mb-2 mb-md-0">
                    <strong>{{ \App\Models\Pengaturan::ambil('nama_sekolah', 'Sistem Peminjaman Alat Laboratorium') }}</strong>
                    <div class="small text-muted">Aplikasi Pengelolaan & Inventarisasi Laboratorium</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted small">&copy; {{ date('Y') }} Hak Cipta Dilindungi Sistem.</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
