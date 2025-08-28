<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحذير أمني - منصة التعلم</title>
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Security Headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 50%, #a71e2a 100%);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 20px;
            animation: dangerPulse 3s infinite;
            overflow-y: auto;
            overflow-x: hidden;
        }

        @keyframes dangerPulse {
            0%, 100% {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 50%, #a71e2a 100%);
            }
            50% {
                background: linear-gradient(135deg, #c82333 0%, #a71e2a 50%, #dc3545 100%);
            }
        }

        .danger-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(220, 53, 69, 0.4);
            border: 3px solid #dc3545;
            animation: dangerShake 0.5s ease-in-out;
            position: relative;
            margin: 20px 0;
            min-height: auto;
        }

        @keyframes dangerShake {
            0%, 100% { transform: translateX(0) rotate(0deg); }
            25% { transform: translateX(-5px) rotate(-1deg); }
            75% { transform: translateX(5px) rotate(1deg); }
        }

        .danger-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(220, 53, 69, 0.1) 10px,
                rgba(220, 53, 69, 0.1) 20px
            );
            animation: dangerStripes 2s linear infinite;
            z-index: 0;
        }

        @keyframes dangerStripes {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(20px) translateY(20px); }
        }

        .danger-content {
            position: relative;
            z-index: 1;
        }

        .danger-header {
            margin-bottom: 25px;
        }

        .danger-icon {
            font-size: 3.5rem;
            color: #dc3545;
            margin-bottom: 20px;
            animation: dangerRotate 3s infinite linear;
            display: block;
        }

        @keyframes dangerRotate {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.1); }
            100% { transform: rotate(360deg) scale(1); }
        }

        .danger-title {
            color: #dc3545;
            font-size: 2.2rem;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            animation: dangerGlow 2s ease-in-out infinite alternate;
        }

        @keyframes dangerGlow {
            0% { text-shadow: 2px 2px 4px rgba(0,0,0,0.2); }
            100% { text-shadow: 2px 2px 15px rgba(220, 53, 69, 0.6); }
        }

        .danger-message h2 {
            color: #dc3545;
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .danger-message p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .danger-details,
        .danger-consequences {
            text-align: right;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border-right: 4px solid #dc3545;
            position: relative;
        }

        .danger-details::before,
        .danger-consequences::before {
            content: '🚨';
            position: absolute;
            top: -12px;
            right: 15px;
            font-size: 1.5rem;
            background: white;
            padding: 4px;
            border-radius: 50%;
            border: 2px solid #dc3545;
        }

        .danger-details h3,
        .danger-consequences h3 {
            color: #dc3545;
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .danger-details ul,
        .danger-consequences ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .danger-details li,
        .danger-consequences li {
            color: #495057;
            font-size: 1rem;
            margin-bottom: 10px;
            padding-right: 25px;
            position: relative;
        }

        .danger-details li:after,
        .danger-consequences li:after {
            content: '⚠️';
            position: absolute;
            right: 0;
            top: 0;
        }

        .danger-actions {
            margin: 30px 0;
        }

        .danger-actions .btn {
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.5);
        }

        .danger-footer {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
        }

        .danger-footer p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0 0 5px 0;
        }

        .security-log {
            background: #343a40;
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            text-align: left;
            max-height: 150px;
            overflow-y: auto;
        }

        .security-log h4 {
            color: #ffc107;
            margin-bottom: 12px;
            font-size: 1rem;
        }

        .log-entry {
            color: #28a745;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .log-timestamp {
            color: #6c757d;
            font-size: 0.75rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
                align-items: flex-start;
            }

            .danger-container {
                padding: 20px 15px;
                margin: 10px 0;
                border-radius: 15px;
            }

            .danger-title {
                font-size: 1.8rem;
            }

            .danger-message h2 {
                font-size: 1.5rem;
            }

            .danger-icon {
                font-size: 2.8rem;
            }

            .danger-details,
            .danger-consequences {
                padding: 15px;
                margin: 15px 0;
            }

            .danger-actions .btn {
                padding: 12px 25px;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .danger-container {
                padding: 15px 10px;
            }

            .danger-title {
                font-size: 1.6rem;
            }

            .danger-message h2 {
                font-size: 1.3rem;
            }

            .danger-icon {
                font-size: 2.5rem;
            }
        }

        /* Prevent selection and context menu */
        .danger-container {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Disable right-click */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Ensure proper scrolling */
        html, body {
            height: auto;
            min-height: 100vh;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body oncontextmenu="return false;" onselectstart="return false;" ondragstart="return false;">
    <div class="danger-container">
        <div class="danger-content">
            <div class="danger-header">
                <span class="danger-icon">🚨</span>
                <h1 class="danger-title">⚠️ تحذير أمني خطير ⚠️</h1>
            </div>

            <div class="danger-message">
                <h2>تم اكتشاف انتهاك أمني!</h2>
                <p>تم اكتشاف محاولة فتح أدوات المطور (Developer Tools) أو فحص الكود المصدري للصفحة.</p>

                <div class="danger-details">
                    <h3>تفاصيل الانتهاك:</h3>
                    <ul>
                        <li>تم فتح أدوات المطور (Inspect Element)</li>
                        <li>تم اكتشاف محاولة فحص الكود المصدري</li>
                        <li>تم تسجيل هذا الانتهاك في النظام</li>
                        <li>تم تسجيل عنوان IP والبيانات الشخصية</li>
                    </ul>
                </div>

                <div class="danger-consequences">
                    <h3>العواقب والتدابير:</h3>
                    <ul>
                        <li>تم إيقاف جميع المحتوى مؤقتاً</li>
                        <li>سيتم تسجيل هذا الانتهاك في قاعدة البيانات</li>
                        <li>قد يؤدي إلى تعليق الحساب نهائياً</li>
                        <li>سيتم إبلاغ المسؤولين بالانتهاك</li>
                    </ul>
                </div>

                <div class="security-log">
                    <h4>📊 سجل الأمان:</h4>
                    <div class="log-entry">
                        <span class="log-timestamp">{{ now()->format('Y-m-d H:i:s') }}</span>
                        <br>🚨 تم اكتشاف انتهاك أمني
                        <br>👤 المستخدم: {{ $user->name ?? 'غير محدد' }}
                        <br>📧 البريد الإلكتروني: {{ $user->email ?? 'غير محدد' }}
                        <br>🌐 عنوان IP: {{ request()->ip() }}
                        <br>🔍 نوع الانتهاك: فتح أدوات المطور
                    </div>
                </div>

                <div class="danger-actions">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-danger">
                        <span>✅ فهمت - العودة إلى لوحة التحكم</span>
                    </a>
                </div>
            </div>

            <div class="danger-footer">
                <p><strong>⚠️ تحذير:</strong> أي محاولة أخرى لفتح أدوات المطور ستؤدي إلى تعليق الحساب نهائياً</p>
                <p><small>إذا كنت بحاجة إلى مساعدة، يرجى التواصل مع الدعم الفني</small></p>
            </div>
        </div>
    </div>

    <script>
        // Additional security measures
        document.addEventListener('keydown', function(e) {
            // Block common developer shortcuts
            const blockedKeys = [
                'F12', 'Ctrl+Shift+I', 'Ctrl+Shift+J', 'Ctrl+U', 'Ctrl+S',
                'Ctrl+Shift+C', 'F5', 'Ctrl+R', 'Ctrl+Shift+R'
            ];

            const keyCombo = getKeyCombo(e);
            if (blockedKeys.includes(keyCombo)) {
                e.preventDefault();
                alert('تم حظر هذه العملية لأسباب أمنية');
                return false;
            }
        });

        function getKeyCombo(e) {
            let combo = '';
            if (e.ctrlKey) combo += 'Ctrl+';
            if (e.shiftKey) combo += 'Shift+';
            if (e.altKey) combo += 'Alt+';
            combo += e.key;
            return combo;
        }

        // Prevent leaving the page
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = 'هل أنت متأكد من أنك تريد مغادرة صفحة التحذير الأمني؟';
        });

        // Log any additional security violations
        console.log('%cSTOP!', 'color: red; font-size: 50px; font-weight: bold;');
        console.log('%cThis is a protected page. Any attempt to inspect or modify this page will be logged and reported.', 'color: red; font-size: 20px;');

        // Monitor for developer tools
        setInterval(function() {
            if (window.outerHeight - window.innerHeight > 200 || window.outerWidth - window.innerWidth > 200) {
                alert('تم اكتشاف أدوات المطور مرة أخرى!');
                location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
