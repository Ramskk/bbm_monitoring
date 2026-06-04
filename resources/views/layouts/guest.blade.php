<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Pengawasan BBM') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <style>
        body {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 50%, #003d7a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }

        .auth-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border: 1px solid rgba(0, 102, 204, 0.1);
        }

        .auth-header {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            padding: 2rem 1.5rem;
            text-align: center;
            color: white;
        }

        .auth-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .auth-header i {
            font-size: 2rem;
        }

        .auth-header p {
            margin: 0.75rem 0 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .auth-body {
            padding: 2rem 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            padding: 0.625rem 0.875rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .btn-auth {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary-auth {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            color: white;
        }

        .btn-primary-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 102, 204, 0.2);
            color: white;
            text-decoration: none;
        }

        .auth-footer {
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: #0066cc;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            padding: 1rem;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-info {
            background-color: rgba(23, 162, 184, 0.1);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        .alert i {
            margin-right: 0.5rem;
        }

        .form-check-input {
            width: 1.25em;
            height: 1.25em;
            border: 1px solid #dee2e6;
        }

        .form-check-input:checked {
            background-color: #0066cc;
            border-color: #0066cc;
        }

        .form-check-input:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #495057;
            margin-left: 0.5rem;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: block;
        }

        @media (max-width: 576px) {
            .auth-card {
                border-radius: 0.5rem;
            }

            .auth-header {
                padding: 1.5rem 1rem;
            }

            .auth-header h1 {
                font-size: 1.5rem;
            }

            .auth-body {
                padding: 1.5rem 1rem;
            }

            .auth-footer {
                padding: 1rem;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-container">
        {{ $slot }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
