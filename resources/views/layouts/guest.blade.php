<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Global Security Measures -->
        <script>
            // Global Security System for Guest Users
            (function() {
                'use strict';

                // Prevent developer tools access
                function detectDevTools() {
                    const threshold = 160;
                    const widthThreshold = window.outerWidth - window.innerWidth > threshold;
                    const heightThreshold = window.outerHeight - window.innerHeight > threshold;

                    if (widthThreshold || heightThreshold) {
                        // Developer tools detected
                        document.body.innerHTML = `
                            <div style="
                                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                                background: #000; color: #fff; display: flex;
                                align-items: center; justify-content: center;
                                font-family: Arial, sans-serif; text-align: center; z-index: 999999;
                            ">
                                <div>
                                    <h1 style="color: #ff0000; font-size: 2em;">⚠️ Security Warning ⚠️</h1>
                                    <p style="font-size: 1.2em; margin: 20px 0;">
                                        Developer tools detected!<br>
                                        Please close developer tools and press F5 to refresh the page.
                                    </p>
                                    <button onclick="location.reload()" style="
                                        background: #ff0000; color: #fff; border: none;
                                        padding: 15px 30px; font-size: 1.1em;
                                        border-radius: 8px; cursor: pointer;
                                    ">
                                        Refresh Page
                                    </button>
                                </div>
                            </div>
                        `;
                    }
                }

                // Check every 100ms
                setInterval(detectDevTools, 100);

                // Block common developer tools shortcuts
                document.addEventListener('keydown', function(e) {
                    // F12
                    if (e.key === 'F12') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Ctrl+Shift+I (Chrome DevTools)
                    if (e.ctrlKey && e.shiftKey && e.key === 'I') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Ctrl+Shift+J (Chrome Console)
                    if (e.ctrlKey && e.shiftKey && e.key === 'J') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Ctrl+Shift+C (Chrome Element Inspector)
                    if (e.ctrlKey && e.shiftKey && e.key === 'C') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Ctrl+U (View Source)
                    if (e.ctrlKey && e.key === 'u') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Ctrl+S (Save Page)
                    if (e.ctrlKey && e.key === 's') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Ctrl+P (Print)
                    if (e.ctrlKey && e.key === 'p') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });

                // Disable right-click context menu
                document.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });

                // Disable text selection
                document.addEventListener('selectstart', function(e) {
                    e.preventDefault();
                    return false;
                });

                // Disable copy/paste
                document.addEventListener('copy', function(e) {
                    e.preventDefault();
                    return false;
                });

                document.addEventListener('cut', function(e) {
                    e.preventDefault();
                    return false;
                });

                document.addEventListener('paste', function(e) {
                    e.preventDefault();
                    return false;
                });

                // Disable drag and drop
                document.addEventListener('dragstart', function(e) {
                    e.preventDefault();
                    return false;
                });

                document.addEventListener('drop', function(e) {
                    e.preventDefault();
                    return false;
                });

                // Prevent iframe embedding
                if (window.top !== window.self) {
                    window.top.location.href = window.location.href;
                }

                // Console warning
                console.log('%c⚠️ Security Warning ⚠️', 'color: #ff0000; font-size: 20px; font-weight: bold;');
                console.log('%cThis website is protected from tampering attempts. Please do not use developer tools.', 'color: #ff0000; font-size: 14px;');

            })();
        </script>

        <style>
            /* Global Security CSS */
            * {
                -webkit-user-select: none !important;
                -moz-user-select: none !important;
                -ms-user-select: none !important;
                user-select: none !important;
                -webkit-touch-callout: none !important;
                -webkit-user-drag: none !important;
            }

            /* Allow text selection for form inputs and content */
            input, textarea, select, .content-area {
                -webkit-user-select: text !important;
                -moz-user-select: text !important;
                -ms-user-select: text !important;
                user-select: text !important;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
