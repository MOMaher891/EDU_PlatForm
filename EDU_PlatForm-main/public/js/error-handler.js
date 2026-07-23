/**
 * Global Error Handler for Frontend
 * This script catches JavaScript errors and displays them as user-friendly alerts
 */

(function() {
    'use strict';

    // Store original error handlers
    const originalOnError = window.onerror;
    const originalOnUnhandledRejection = window.onunhandledrejection;

    /**
     * Display error message as alert
     */
    function showErrorAlert(message, title = 'خطأ') {
        // Check if we're in a Laravel app with SweetAlert or similar
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'error',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#dc3545'
            });
        } else if (typeof toastr !== 'undefined') {
            toastr.error(message, title);
        } else {
            // Fallback to browser alert
            alert(`${title}: ${message}`);
        }
    }

    /**
     * Display success message
     */
    function showSuccessAlert(message, title = 'نجح') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'success',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#28a745'
            });
        } else if (typeof toastr !== 'undefined') {
            toastr.success(message, title);
        } else {
            alert(`${title}: ${message}`);
        }
    }

    /**
     * Display info message
     */
    function showInfoAlert(message, title = 'معلومات') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'info',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#17a2b8'
            });
        } else if (typeof toastr !== 'undefined') {
            toastr.info(message, title);
        } else {
            alert(`${title}: ${message}`);
        }
    }

    /**
     * Display warning message
     */
    function showWarningAlert(message, title = 'تحذير') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#ffc107'
            });
        } else if (typeof toastr !== 'undefined') {
            toastr.warning(message, title);
        } else {
            alert(`${title}: ${message}`);
        }
    }

    /**
     * Handle JavaScript errors
     */
    function handleError(message, source, lineno, colno, error) {
        // Ignore common non-critical errors
        if (message && (
            message.includes('ResizeObserver') ||
            message.includes('ResizeObserver loop') ||
            message.includes('Script error') ||
            message.includes('Script error.') ||
            message.includes('Uncaught') && message.includes('Alpine.js')
        )) {
            // Log to console but don't show alert for these common errors
            console.warn('Non-critical JavaScript error (suppressed):', {
                message: message,
                source: source,
                lineno: lineno,
                colno: colno
            });
            return false;
        }

        // Log error to console for developers
        console.error('JavaScript Error:', {
            message: message,
            source: source,
            lineno: lineno,
            colno: colno,
            error: error
        });

        // Show user-friendly error message only for actual errors
        const userMessage = 'حدث خطأ في الصفحة. يرجى تحديث الصفحة والمحاولة مرة أخرى.';
        showErrorAlert(userMessage, 'خطأ في الصفحة');

        // Call original handler if it exists
        if (originalOnError) {
            return originalOnError(message, source, lineno, colno, error);
        }

        return false;
    }

    /**
     * Handle unhandled promise rejections
     */
    function handleUnhandledRejection(event) {
        // Log error to console for developers
        console.error('Unhandled Promise Rejection:', event.reason);

        // Show user-friendly error message
        const userMessage = 'حدث خطأ في النظام. يرجى المحاولة مرة أخرى.';
        showErrorAlert(userMessage, 'خطأ في النظام');

        // Call original handler if it exists
        if (originalOnUnhandledRejection) {
            return originalOnUnhandledRejection(event);
        }

        return false;
    }

    /**
     * Handle AJAX errors
     */
    function handleAjaxError(xhr, status, error) {
        let userMessage = 'حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.';

        // Try to get error message from response
        if (xhr.responseJSON && xhr.responseJSON.error) {
            userMessage = xhr.responseJSON.error;
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            userMessage = xhr.responseJSON.message;
        } else if (xhr.status === 404) {
            userMessage = 'الصفحة المطلوبة غير موجودة.';
        } else if (xhr.status === 403) {
            userMessage = 'ليس لديك صلاحية للوصول لهذه الصفحة.';
        } else if (xhr.status === 500) {
            userMessage = 'حدث خطأ في الخادم. يرجى المحاولة مرة أخرى لاحقاً.';
        } else if (xhr.status === 0) {
            userMessage = 'فشل في الاتصال بالخادم. يرجى التحقق من اتصال الإنترنت.';
        }

        showErrorAlert(userMessage, 'خطأ في الاتصال');
    }

    /**
     * Handle fetch errors
     */
    function handleFetchError(error) {
        console.error('Fetch Error:', error);

        let userMessage = 'حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.';

        if (error.name === 'TypeError' && error.message.includes('fetch')) {
            userMessage = 'فشل في الاتصال بالخادم. يرجى التحقق من اتصال الإنترنت.';
        }

        showErrorAlert(userMessage, 'خطأ في الاتصال');
    }

    /**
     * Initialize error handling
     */
    function init() {
        // Wait for Alpine.js to initialize if it exists
        if (typeof Alpine !== 'undefined') {
            // Wait for Alpine to be ready
            document.addEventListener('alpine:init', function() {
                setTimeout(setupErrorHandlers, 100);
            });
        } else {
            // Set up error handlers immediately if Alpine.js is not present
            setTimeout(setupErrorHandlers, 100);
        }
    }

    /**
     * Set up error handlers
     */
    function setupErrorHandlers() {
        // Set global error handlers
        window.onerror = handleError;
        window.onunhandledrejection = handleUnhandledRejection;

        // Override jQuery AJAX error handling if jQuery exists
        if (typeof $ !== 'undefined' && $.ajax) {
            $(document).ajaxError(function(event, xhr, settings, error) {
                handleAjaxError(xhr, xhr.status, error);
            });
        }

        // Override fetch to catch errors
        if (window.fetch) {
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args).catch(handleFetchError);
            };
        }

        // Handle form submission errors
        document.addEventListener('submit', function(event) {
            const form = event.target;
            if (form.classList.contains('needs-validation')) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    showErrorAlert('يرجى ملء جميع الحقول المطلوبة بشكل صحيح.', 'خطأ في النموذج');
                }
            }
        });

        // Handle image loading errors
        document.addEventListener('error', function(event) {
            if (event.target.tagName === 'IMG') {
                event.target.style.display = 'none';
                console.warn('Image failed to load:', event.target.src);
            }
        }, true);

        // Handle video loading errors
        document.addEventListener('error', function(event) {
            if (event.target.tagName === 'VIDEO') {
                console.warn('Video failed to load:', event.target.src);
                showErrorAlert('فشل في تحميل الفيديو. يرجى المحاولة مرة أخرى.', 'خطأ في الفيديو');
            }
        }, true);

        // Handle audio loading errors
        document.addEventListener('error', function(event) {
            if (event.target.tagName === 'AUDIO') {
                console.warn('Audio failed to load:', event.target.src);
                showErrorAlert('فشل في تحميل الصوت. يرجى المحاولة مرة أخرى.', 'خطأ في الصوت');
            }
        }, true);

        console.log('Error handler initialized successfully');
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose functions globally for use in other scripts
    window.ErrorHandler = {
        showError: showErrorAlert,
        showSuccess: showSuccessAlert,
        showInfo: showInfoAlert,
        showWarning: showWarningAlert,
        handleAjaxError: handleAjaxError,
        handleFetchError: handleFetchError
    };

})();
