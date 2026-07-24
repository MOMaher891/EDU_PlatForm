<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <title>
        @hasSection('title')
            @yield('title') | {{ $appSettings->platform_name ?? 'منصة التعلم الإلكتروني' }}
        @else
            {{ $appSettings->platform_name ?? 'منصة التعلم الإلكتروني' }}
        @endif
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- IntlTelInput CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">

    @if($appSettings->block_devtools ?? false)
    <script src="https://cdn.jsdelivr.net/npm/disable-devtool"></script>
    <script>
        DisableDevtool({
            url: '{{ route("danger.page") }}',
            disableMenu: true,
            clearLog: true,
        });
    </script>
    @endif

    <!-- App UI Styles (always on) -->
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #f1f5f9;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-radius: 12px;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --box-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * { font-family: 'Cairo', sans-serif; }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .main-content {
            background: var(--light-color);
            min-height: calc(100vh - 80px);
            margin-top: 80px;
        }

        .navbar {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0; height: 2px; bottom: -5px; left: 50%;
            background: var(--primary-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after { width: 100%; }

        .card { border: none; border-radius: var(--border-radius); box-shadow: var(--box-shadow); transition: all 0.3s ease; overflow: hidden; }
        .card:hover { transform: translateY(-5px); box-shadow: var(--box-shadow-lg); }
        .course-card { position: relative; overflow: hidden; }
        .course-card::before { content: ''; position: absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(6,182,212,0.1)); opacity:0; transition: opacity 0.3s ease; z-index:1; }
        .course-card:hover::before { opacity: 1; }
        .course-card .card-body { position: relative; z-index: 2; }

        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 12px 28px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }
        .btn-primary:active {
            transform: translateY(0) scale(0.98);
        }
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color) !important;
            background: rgba(99, 102, 241, 0.03);
        }
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: #ffffff !important;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
        }
        .btn-outline-primary:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-outline-secondary {
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            font-weight: 600;
        }
        .btn-outline-secondary:hover {
            background: #ffffff;
            color: #1e293b !important;
            border-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .page-header .btn-outline-secondary,
        .header-actions .btn-outline-secondary,
        .header-actions .btn-secondary {
            border-color: rgba(255, 255, 255, 0.5) !important;
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.18) !important;
            backdrop-filter: blur(8px);
            font-weight: 600;
        }
        .page-header .btn-outline-secondary:hover,
        .header-actions .btn-outline-secondary:hover,
        .header-actions .btn-secondary:hover {
            background: #ffffff !important;
            color: #1e293b !important;
            border-color: #ffffff !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Secondary button inside plain white cards */
        .card-body .btn-outline-secondary,
        .modal-body .btn-outline-secondary {
            border-color: #cbd5e1 !important;
            color: #334155 !important;
            background: #f8fafc !important;
        }
        .card-body .btn-outline-secondary:hover,
        .modal-body .btn-outline-secondary:hover {
            border-color: var(--primary-color) !important;
            color: #ffffff !important;
            background: var(--primary-color) !important;
        }

        .form-control, .form-select { border-radius: var(--border-radius); border: 2px solid #e2e8f0; padding: 12px 16px; transition: all 0.3s ease; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        .badge { border-radius: 20px; padding: 6px 12px; font-weight: 500; }
        .progress { height: 8px; border-radius: 10px; background: #e2e8f0; }
        .progress-bar { border-radius: 10px; background: linear-gradient(90deg, var(--primary-color), var(--accent-color)); }
        .progress-controls-unified {
            background-color: rgba(255, 255, 255, 0.8);
            border-color: rgba(99, 102, 241, 0.1) !important;
        }

        .fade-in { animation: fadeIn 0.6s ease-in; }
        @keyframes fadeIn { from { opacity:0; transform: translateY(20px);} to { opacity:1; transform: translateY(0);} }
        .slide-up { animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity:0; transform: translateY(30px);} to { opacity:1; transform: translateY(0);} }

        .glass { background: rgba(255,255,255,0.25); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); }
        .footer { background: linear-gradient(135deg, #1e293b, #334155); color: white; }

        @media (max-width: 991.98px) {
            html, body {
                overflow-x: hidden;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .main-content { margin-top: 70px; }
            .navbar-brand { font-size: 1.2rem; }
        }

        /* Theme Toggle Button Premium Design */
        #theme-toggle {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--dark-color);
        }

        #theme-toggle:hover {
            color: var(--primary-color) !important;
            transform: scale(1.15) rotate(15deg);
        }

        /* Dynamic Dark Mode Overrides */
        [data-bs-theme="dark"] {
            color-scheme: dark;
            --bs-body-bg: #0f172a;
            --bs-body-color: #f8fafc;
            --bs-card-bg: #1e293b;
            --bs-card-color: #f8fafc;
            --bs-tertiary-bg: #1e293b;
            --bs-border-color: #334155;
            --bs-body-color-rgb: 248, 250, 252;
            --bs-body-bg-rgb: 15, 23, 42;

            /* Override app UI style variables */
            --dark-color: #f8fafc;
            --light-color: #0f172a;
            --secondary-color: #1e293b;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
            --box-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }

        /* Specific element overrides for premium dark theme */
        [data-bs-theme="dark"] body {
            background: linear-gradient(135deg, #090d16 0%, #0f172a 100%) !important;
        }

        [data-bs-theme="dark"] .main-content {
            background: #0f172a !important;
        }

        [data-bs-theme="dark"] .navbar {
            background: rgba(15, 23, 42, 0.95) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .nav-link {
            color: rgba(248, 250, 252, 0.8) !important;
        }

        [data-bs-theme="dark"] .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        [data-bs-theme="dark"] .btn-outline-primary {
            border-color: rgba(255, 255, 255, 0.25) !important;
            color: #f8fafc !important;
            background: rgba(255, 255, 255, 0.05) !important;
        }

        [data-bs-theme="dark"] .btn-outline-primary:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
            border-color: #f8fafc !important;
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.15) !important;
        }

        [data-bs-theme="dark"] .card {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        [data-bs-theme="dark"] .card-header, 
        [data-bs-theme="dark"] .card-footer {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .text-dark {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .bg-white {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3) !important;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #334155 !important;
            color: #fff !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: #0f172a !important;
            border-color: var(--primary-color) !important;
            color: #f8fafc !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
        }

        [data-bs-theme="dark"] .table {
            --bs-table-bg: #1e293b !important;
            --bs-table-color: #f8fafc !important;
            --bs-table-border-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-color-type: #f8fafc !important;
            --bs-table-bg-type: #162030 !important;
        }

        [data-bs-theme="dark"] .modal-content {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-bs-theme="dark"] .modal-header .btn-close {
            filter: invert(1) grayscale(1) brightness(2);
        }

        [data-bs-theme="dark"] .hero-section {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.95)) !important;
        }

        [data-bs-theme="dark"] .glass {
            background: rgba(15, 23, 42, 0.45) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        [data-bs-theme="dark"] .badge.bg-light.text-dark {
            background-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .alert {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .progress {
            background-color: #0f172a !important;
        }

        [data-bs-theme="dark"] .activity-item:hover {
            background: rgba(255, 255, 255, 0.02) !important;
        }

        [data-bs-theme="dark"] .activity-icon.bg-light {
            background-color: #334155 !important;
        }

        [data-bs-theme="dark"] ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        [data-bs-theme="dark"] ::-webkit-scrollbar-thumb {
            background: #334155;
        }

        [data-bs-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Compliance Pages Overrides */
        [data-bs-theme="dark"] .markdown-body {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .markdown-body h1,
        [data-bs-theme="dark"] .markdown-body h2,
        [data-bs-theme="dark"] .markdown-body h3,
        [data-bs-theme="dark"] .markdown-body strong {
            color: #f8fafc !important;
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .markdown-body blockquote,
        [data-bs-theme="dark"] .markdown-body p:has(strong:contains("Paymob")),
        [data-bs-theme="dark"] .markdown-body p:has(strong:contains("We do not store")) {
            background-color: #0f172a !important;
            color: #cbd5e1 !important;
            border-left-color: var(--primary-color) !important;
        }

        /* Settings Page Overrides */
        [data-bs-theme="dark"] .admin-settings-page {
            background-color: #0f172a !important;
        }

        [data-bs-theme="dark"] .settings-section,
        [data-bs-theme="dark"] .system-status-card,
        [data-bs-theme="dark"] .quick-actions-card,
        [data-bs-theme="dark"] .system-info-card {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .info-item {
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .info-label {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .info-value {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .form-label {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .compliance-nav-link {
            color: #cbd5e1 !important;
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        [data-bs-theme="dark"] .compliance-nav-link:hover {
            color: #ffffff !important;
            background-color: #475569 !important;
        }

        [data-bs-theme="dark"] .compliance-nav-link.active {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
            border-color: #4f46e5 !important;
        }

        /* Learning Interface Overrides */
        [data-bs-theme="dark"] .learning-interface {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-section {
            background-color: #1e293b !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4) !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-header {
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-title {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-meta span {
            background-color: #0f172a !important;
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .learning-interface .action-btn {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .learning-interface .action-btn:hover {
            background-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .action-btn.active {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .learning-interface .file-preview {
            border-color: #334155 !important;
            background-color: #0f172a !important;
        }

        [data-bs-theme="dark"] .learning-interface .file-info h4 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-text-content {
            background-color: #0f172a !important;
            border-left-color: var(--primary-color) !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-text-content h4 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-text {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-navigation {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            border-top-color: #334155 !important;
        }

        [data-bs-theme="dark"] .learning-interface .course-sidebar {
            background-color: #1e293b !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4) !important;
        }

        [data-bs-theme="dark"] .learning-interface .sidebar-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .learning-interface .course-title {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .instructor-name {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .learning-interface .total-lessons,
        [data-bs-theme="dark"] .learning-interface .progress-text,
        [data-bs-theme="dark"] .learning-interface .progress-percentage {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .learning-interface .progress-bar {
            background-color: #0f172a !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-tabs {
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .learning-interface .tab-btn {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .learning-interface .tab-btn:hover {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .tab-btn.active {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .learning-interface .section-item {
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .learning-interface .section-header {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .learning-interface .section-header:hover {
            background-color: #334155 !important;
        }

        [data-bs-theme="dark"] .learning-interface .section-title {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .section-lessons-count {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .learning-interface .mini-progress-bar {
            background-color: #0f172a !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-item {
            background-color: transparent !important;
            color: #f8fafc !important;
            border-color: transparent !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-item:hover {
            background-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-item.active {
            background-color: rgba(99, 102, 241, 0.2) !important;
            color: #f8fafc !important;
            border-left: 3px solid var(--primary-color) !important;
        }

        [data-bs-theme="dark"] .learning-interface .lesson-item.completed {
            background-color: rgba(40, 167, 69, 0.1) !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-body {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-body p {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-body h1,
        [data-bs-theme="dark"] .learning-interface .content-body h2,
        [data-bs-theme="dark"] .learning-interface .content-body h3,
        [data-bs-theme="dark"] .learning-interface .content-body h4,
        [data-bs-theme="dark"] .learning-interface .content-body h5,
        [data-bs-theme="dark"] .learning-interface .content-body h6 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-body blockquote {
            background-color: #0f172a !important;
            color: #94a3b8 !important;
            border-left: 4px solid var(--primary-color) !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-body code {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .content-body pre {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .progress-controls-unified {
            background: rgba(30, 41, 59, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .learning-interface .simple-navigation {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
        }

        [data-bs-theme="dark"] .learning-interface .simple-navigation .btn-outline-primary {
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
        }

        [data-bs-theme="dark"] .learning-interface .simple-navigation .btn-outline-primary:hover:not(:disabled) {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .learning-interface .simple-navigation .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .learning-interface .simple-navigation .btn-primary:hover:not(:disabled) {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
        }

        [data-bs-theme="dark"] .learning-interface .resource-item {
            border-color: #334155 !important;
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }

        /* Auth Pages Overrides */
        [data-bs-theme="dark"] .auth-card {
            background-color: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        [data-bs-theme="dark"] .auth-form {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .auth-form h2 {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .auth-form .form-check-label {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .demo-accounts {
            border-color: #334155 !important;
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }

        /* IntlTelInput CSS Adjustments & RTL Fixes */
        .iti {
            width: 100%;
            display: block;
        }
        .iti__country-list {
            color: #1e293b;
            z-index: 1070;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            font-family: 'Cairo', sans-serif;
            max-width: 340px;
            white-space: normal;
        }
        html[dir="rtl"] .iti__country-list,
        [dir="rtl"] .iti__country-list {
            left: 0 !important;
            right: auto !important;
            text-align: right;
        }
        [dir="rtl"] .iti__country-name {
            margin-right: 6px;
            margin-left: 6px;
        }
        [dir="rtl"] .iti__flag-box {
            margin-right: 0;
            margin-left: 6px;
        }
        [data-bs-theme="dark"] .iti__country-list {
            background-color: #1e293b;
            color: #f8fafc;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        [data-bs-theme="dark"] .iti__country-name,
        [data-bs-theme="dark"] .iti__dial-code {
            color: #f8fafc;
        }
        [data-bs-theme="dark"] .iti__country.iti__highlight {
            background-color: #334155;
        }
        /* Fix Bootstrap validation icon positioning inside intl-tel-input */
        .iti input.is-valid,
        html[dir="rtl"] .iti input.is-valid,
        [dir="rtl"] .iti input.is-valid {
            background-position: right 12px center !important;
            background-size: 18px 18px !important;
            padding-right: 36px !important;
        }
        .iti input.is-invalid,
        html[dir="rtl"] .iti input.is-invalid,
        [dir="rtl"] .iti input.is-invalid {
            background-position: right 12px center !important;
            background-size: 18px 18px !important;
            padding-right: 36px !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Modern Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                @if(!empty($appSettings->platform_logo))
                    <img src="{{ asset('storage/' . $appSettings->platform_logo) }}" alt="Logo" class="me-2" style="height: 35px; max-width: 120px; object-fit: contain;">
                @else
                    <i class="fas fa-graduation-cap me-2"></i>
                @endif
                <span>{{ $appSettings->platform_name ?? 'A+ Academy' }}</span>
            </a>

            <!-- Mobile Controls Group (visible on mobile only) -->
            <div class="d-flex align-items-center gap-2 d-lg-none">
                <!-- Theme Toggle for Mobile -->
                <button id="theme-toggle-mobile" class="nav-link p-0 d-flex align-items-center justify-content-center" type="button" style="width: 36px; height: 36px; border-radius: 50%; border: none; background: transparent;" title="تغيير المظهر">
                    <i class="fas fa-moon fs-5" id="theme-toggle-dark-icon-mobile"></i>
                    <i class="fas fa-sun fs-5 d-none" id="theme-toggle-light-icon-mobile"></i>
                </button>

                <!-- Profile Dropdown or Login Link for Mobile -->
                @guest
                    <a class="nav-link p-0 d-flex align-items-center justify-content-center text-muted" href="{{ route('login') }}" style="width: 36px; height: 36px; border-radius: 50%;" title="تسجيل الدخول">
                        <i class="far fa-user-circle fs-4"></i>
                    </a>
                @else
                    <div class="dropdown">
                        <a class="nav-link p-0 d-flex align-items-center justify-content-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 36px; height: 36px; border-radius: 50%;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff"
                                 class="rounded-circle border border-primary" width="32" height="32" alt="Avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end mt-2 shadow border-0" style="position: absolute; left: 0; right: auto; min-width: 200px;">
                            <li class="dropdown-header text-start py-2 px-3">
                                <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item py-2 text-start" href="{{ route('admin.profile') }}"><i class="fas fa-user me-2 text-primary"></i>الملف الشخصي</a></li>
                                <li><a class="dropdown-item py-2 text-start" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2 text-secondary"></i>إعدادات النظام</a></li>
                            @else
                                <li><a class="dropdown-item py-2 text-start" href="#"><i class="fas fa-user me-2 text-primary"></i>الملف الشخصي</a></li>
                            @endif
                            
                            @if(auth()->user()->isStudent())
                                <li><a class="dropdown-item py-2 text-start" href="{{ route('student.dashboard') }}"><i class="fas fa-tachometer-alt me-2 text-success"></i>لوحة التحكم</a></li>
                            @elseif(auth()->user()->isAdmin())
                                <li><a class="dropdown-item py-2 text-start" href="{{ route('admin.dashboard') }}"><i class="fas fa-cog me-2 text-info"></i>إدارة النظام</a></li>
                            @endif
                            
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item text-danger py-2 text-start" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    تسجيل الخروج
                                </a>
                            </li>
                        </ul>
                    </div>
                    <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endguest

                <!-- Mobile Menu Toggler -->
                <button class="navbar-toggler border-0 p-0 ms-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars fs-4"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.courses.index') ? 'active' : '' }}" href="{{ route('student.courses.index') }}">
                            <i class="fas fa-book me-1"></i>
                            الكورسات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                            <i class="fas fa-info-circle me-1"></i>
                            من نحن
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            <i class="fas fa-envelope me-1"></i>
                            تواصل معنا
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-contract me-1"></i>
                            الشروط والسياسات
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item text-start" href="{{ route('compliance.terms') }}">الشروط والأحكام</a></li>
                            <li><a class="dropdown-item text-start" href="{{ route('compliance.privacy') }}">سياسة الخصوصية</a></li>
                            <li><a class="dropdown-item text-start" href="{{ route('compliance.refund') }}">سياسة الاسترجاع</a></li>
                        </ul>
                    </li>
                    @auth
                        @if(auth()->user()->isStudent())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    لوحة التحكم
                                </a>
                            </li>
                        @elseif(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-cog me-1"></i>
                                    إدارة النظام
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav align-items-center d-none d-lg-flex">
                    <!-- Theme Toggle -->
                    <li class="nav-item mx-2">
                        <button id="theme-toggle" class="nav-link p-0 d-flex align-items-center justify-content-center" type="button" style="width: 40px; height: 40px; border-radius: 50%; border: none; background: transparent;" title="تغيير المظهر">
                            <i class="fas fa-moon fs-5" id="theme-toggle-dark-icon"></i>
                            <i class="fas fa-sun fs-5 d-none" id="theme-toggle-light-icon"></i>
                        </button>
                    </li>
                    @guest
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                تسجيل الدخول
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>
                                إنشاء حساب
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="avatar me-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff"
                                         class="rounded-circle" width="32" height="32" alt="Avatar">
                                </div>
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user me-2"></i>الملف الشخصي</a></li>
                                @else
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>الملف الشخصي</a></li>
                                @endif
                                @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2"></i>إعدادات النظام</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        تسجيل الخروج
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050; margin-top: 80px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer class="footer py-5">
        @php
            $supportPhone = $appSettings->support_phone ?? '+966 50 123 4567';
            $whatsappNumber = preg_replace('/[^0-9]/', '', $supportPhone);
            if (empty($whatsappNumber)) {
                $whatsappNumber = '966501234567';
            }
        @endphp
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        @if(!empty($appSettings->platform_logo))
                            <img src="{{ asset('storage/' . $appSettings->platform_logo) }}" alt="Logo" class="me-2" style="height: 35px; max-width: 120px; object-fit: contain;">
                        @else
                            <i class="fas fa-graduation-cap me-2"></i>
                        @endif
                        <span>{{ $appSettings->platform_name ?? 'A+ Academy' }}</span>
                    </h5>
                    <p class="text-light opacity-75">
                        {{ $appSettings->platform_description ?? 'منصة التعلم الإلكتروني الرائدة في المنطقة. نوفر أفضل الكورسات التعليمية مع خبراء متخصصين.' }}
                    </p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/share/1bEryWohy3/" target="_blank" class="btn btn-outline-light btn-sm me-2" title="فيسبوك">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.linkedin.com/in/mohamed-maher-5a17341b9?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" target="_blank" class="btn btn-outline-light btn-sm me-2" title="لينكد إن">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://www.instagram.com/momaher158?igsh=dG83Z3ltMDZjaHVi" target="_blank" class="btn btn-outline-light btn-sm me-2" title="إنستجرام">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="btn btn-outline-light btn-sm me-2" title="واتساب الدعم">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://x.com/Mohamed99873441" target="_blank" class="btn btn-outline-light btn-sm me-2" title="إكس (تويتر)">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                        <a href="mailto:{{ $appSettings->support_email ?? 'support@example.com' }}" class="btn btn-outline-light btn-sm" title="البريد الإلكتروني">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">روابط سريعة</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('student.courses.index') }}" class="text-light opacity-75 text-decoration-none">الكورسات</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}" class="text-light opacity-75 text-decoration-none">من نحن</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="text-light opacity-75 text-decoration-none">تواصل معنا</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">المدونة</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">الدعم والسياسات</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('compliance.terms') }}" class="text-light opacity-75 text-decoration-none">الشروط والأحكام</a></li>
                        <li class="mb-2"><a href="{{ route('compliance.privacy') }}" class="text-light opacity-75 text-decoration-none">سياسة الخصوصية</a></li>
                        <li class="mb-2"><a href="{{ route('compliance.refund') }}" class="text-light opacity-75 text-decoration-none">سياسة الاسترجاع</a></li>
                        <li class="mb-2">
                            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="text-light opacity-75 text-decoration-none d-inline-flex align-items-center gap-2" title="الدعم الفني (واتساب)">
                                <i class="fab fa-whatsapp text-success"></i>
                                <span>الدعم الفني (واتساب)</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-3">اشترك في النشرة الإخبارية</h6>
                    <p class="text-light opacity-75 mb-3">احصل على آخر الأخبار والعروض الخاصة</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="البريد الإلكتروني">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75">
                        &copy; {{ date('Y') }} {{ $appSettings->platform_name ?? 'A+ Academy' }}. جميع الحقوق محفوظة.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-light opacity-75">
                        صنع بـ <i class="fas fa-heart text-danger"></i> بواسطة م.محمد ماهر
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Error Handler Script -->
    <script src="{{ asset('js/error-handler.js') }}"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Theme Toggle Functionality
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            const themeToggleBtnMobile = document.getElementById('theme-toggle-mobile');
            const themeToggleDarkIconMobile = document.getElementById('theme-toggle-dark-icon-mobile');
            const themeToggleLightIconMobile = document.getElementById('theme-toggle-light-icon-mobile');

            function updateToggleIcons(theme) {
                if (theme === 'dark') {
                    if (themeToggleDarkIcon) themeToggleDarkIcon.classList.add('d-none');
                    if (themeToggleLightIcon) themeToggleLightIcon.classList.remove('d-none');
                    if (themeToggleDarkIconMobile) themeToggleDarkIconMobile.classList.add('d-none');
                    if (themeToggleLightIconMobile) themeToggleLightIconMobile.classList.remove('d-none');
                } else {
                    if (themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('d-none');
                    if (themeToggleLightIcon) themeToggleLightIcon.classList.add('d-none');
                    if (themeToggleDarkIconMobile) themeToggleDarkIconMobile.classList.remove('d-none');
                    if (themeToggleLightIconMobile) themeToggleLightIconMobile.classList.add('d-none');
                }
            }

            // Sync icon on load
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateToggleIcons(currentTheme);

            function handleThemeToggle() {
                const activeTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = activeTheme === 'dark' ? 'light' : 'dark';

                document.documentElement.setAttribute('data-bs-theme', newTheme);
                if (newTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                localStorage.setItem('theme', newTheme);
                updateToggleIcons(newTheme);
            }

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', handleThemeToggle);
            }
            if (themeToggleBtnMobile) {
                themeToggleBtnMobile.addEventListener('click', handleThemeToggle);
            }

            // Auto-close mobile navbar on click outside or link click
            document.addEventListener('click', (event) => {
                const navbarCollapse = document.getElementById('navbarNav');
                const toggler = document.querySelector('.navbar-toggler');
                
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const isClickInsideNavbar = navbarCollapse.contains(event.target);
                    const isClickOnToggler = toggler && toggler.contains(event.target);
                    
                    if (!isClickInsideNavbar && !isClickOnToggler) {
                        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, { toggle: false });
                        bsCollapse.hide();
                    } else if (isClickInsideNavbar) {
                        // Close if clicked on a nav-link or dropdown-item (but not a dropdown-toggle)
                        const clickedLink = event.target.closest('.nav-link:not(.dropdown-toggle), .dropdown-item');
                        if (clickedLink) {
                            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, { toggle: false });
                            bsCollapse.hide();
                        }
                    }
                }
            });
        });
    </script>
    <!-- International Telephone Input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script>
        function initIntlTelInputs(container = document) {
            if (typeof window.intlTelInput === 'undefined') return;
            const phoneInputs = container.querySelectorAll('input[type="tel"], #phone, .phone-input');
            phoneInputs.forEach(function(input) {
                if (input.dataset.itiInitialized) return;
                input.dataset.itiInitialized = "true";

                const form = input.closest('form');
                let countryCodeInput = form ? form.querySelector('input[name="country_code"]') : null;
                if (form && !countryCodeInput) {
                    countryCodeInput = document.createElement('input');
                    countryCodeInput.type = 'hidden';
                    countryCodeInput.name = 'country_code';
                    countryCodeInput.id = input.id ? (input.id + '_country_code') : 'country_code';
                    countryCodeInput.value = '+20';
                    form.appendChild(countryCodeInput);
                }

                const iti = window.intlTelInput(input, {
                    initialCountry: "eg",
                    preferredCountries: ["eg", "sa", "ae", "kw", "qa", "om", "bh", "jo", "iq", "ly", "sd", "ma", "dz", "tn", "us", "gb", "tr"],
                    separateDialCode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
                });

                input.itiInstance = iti;

                function updateCountryCode() {
                    const countryData = iti.getSelectedCountryData();
                    if (countryData && countryData.dialCode && countryCodeInput) {
                        countryCodeInput.value = '+' + countryData.dialCode;
                    } else if (countryCodeInput && !countryCodeInput.value) {
                        countryCodeInput.value = '+20';
                    }
                }

                // Initial number and country resolution
                const rawVal = input.value ? input.value.trim() : '';
                if (rawVal) {
                    if (rawVal.startsWith('+')) {
                        iti.setNumber(rawVal);
                    } else {
                        const code = (countryCodeInput && countryCodeInput.value) ? countryCodeInput.value : '+20';
                        const cleanNum = rawVal.replace(/^0+/, '');
                        iti.setNumber(code + cleanNum);
                    }
                } else if (countryCodeInput && countryCodeInput.value) {
                    const dialCode = countryCodeInput.value.replace('+', '');
                    const allCountries = window.intlTelInputGlobals ? window.intlTelInputGlobals.getCountryData() : [];
                    const found = allCountries.find(c => c.dialCode === dialCode);
                    if (found) {
                        iti.setCountry(found.iso2);
                    } else {
                        iti.setCountry('eg');
                    }
                } else {
                    iti.setCountry('eg');
                }

                updateCountryCode();

                function showPhoneErrorMessage(msg) {
                    let parent = input.closest('.mb-3') || input.parentElement;
                    let errDiv = parent.querySelector('.phone-error-feedback');
                    if (!errDiv) {
                        errDiv = document.createElement('div');
                        errDiv.className = 'invalid-feedback d-block phone-error-feedback mt-1 text-danger fw-semibold';
                        parent.appendChild(errDiv);
                    }
                    errDiv.textContent = msg;
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                }

                function removePhoneErrorMessage() {
                    let parent = input.closest('.mb-3') || input.parentElement;
                    let errDiv = parent.querySelector('.phone-error-feedback');
                    if (errDiv) {
                        errDiv.remove();
                    }
                    input.classList.remove('is-invalid');
                }

                function validatePhone() {
                    const cleanVal = input.value.replace(/[^0-9]/g, '');
                    if (!cleanVal) {
                        removePhoneErrorMessage();
                        return true;
                    }

                    if (cleanVal.length < 6 || cleanVal.length > 15 || !iti.isValidNumber()) {
                        showPhoneErrorMessage('رقم الهاتف غير صحيح للدولة المحددة');
                        return false;
                    } else {
                        removePhoneErrorMessage();
                        input.classList.add('is-valid');
                        return true;
                    }
                }

                // Sanitize input live & limit max digits to 15
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length > 15) {
                        this.value = this.value.slice(0, 15);
                    }
                    updateCountryCode();
                    if (this.value.length >= 5) {
                        validatePhone();
                    } else if (this.value.length === 0) {
                        removePhoneErrorMessage();
                    }
                });

                input.addEventListener('blur', function() {
                    if (input.value.trim().length > 0) {
                        validatePhone();
                    }
                });

                input.addEventListener('countrychange', function() {
                    updateCountryCode();
                    if (input.value.trim().length > 0) {
                        validatePhone();
                    }
                });

                if (form) {
                    form.addEventListener('submit', function(e) {
                        updateCountryCode();
                        if (input.value.trim().length > 0 && !validatePhone()) {
                            e.preventDefault();
                            e.stopPropagation();
                            input.focus();
                            return false;
                        }
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initIntlTelInputs();
        });
    </script>
    @stack('scripts')
</body>
</html>
