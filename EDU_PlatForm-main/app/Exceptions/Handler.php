<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log all exceptions
            Log::error('Unhandled exception: ' , [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        });

        // Handle different types of exceptions
        $this->renderable(function (Throwable $e, Request $request) {
            return $this->handleException($e, $request);
        });
    }

    /**
     * Handle all exceptions and return user-friendly responses
     */
    protected function handleException(Throwable $e, Request $request)
    {
        // Log the exception
        Log::error('Exception handled: ' , [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id()
        ]);

        // Handle AJAX requests
        if ($request->expectsJson()) {
            return $this->handleJsonException($e);
        }

        // Handle different exception types
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e, $request);
        }

        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($e, $request);
        }

        if ($e instanceof AuthorizationException) {
            return $this->handleAuthorizationException($e, $request);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($e, $request);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFoundException($e, $request);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedException($e, $request);
        }

        if ($e instanceof TokenMismatchException) {
            return $this->handleTokenMismatchException($e, $request);
        }

        if ($e instanceof QueryException) {
            return $this->handleQueryException($e, $request);
        }

        // Handle any other exceptions
        return $this->handleGenericException($e, $request);
    }

    /**
     * Handle JSON exceptions
     */
    protected function handleJsonException(Throwable $e)
    {
        $statusCode = 500;
        $message = 'حدث خطأ في النظام. يرجى المحاولة مرة أخرى.';

        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        }

        if ($e instanceof ValidationException) {
            $statusCode = 422;
            $message = 'بيانات غير صحيحة. يرجى التحقق من المدخلات.';
        }

        if ($e instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'يجب تسجيل الدخول للوصول لهذه الصفحة.';
        }

        if ($e instanceof AuthorizationException) {
            $statusCode = 403;
            $message = 'ليس لديك صلاحية للوصول لهذه الصفحة.';
        }

        if ($e instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = 'العنصر المطلوب غير موجود.';
        }

        if ($e instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'الصفحة المطلوبة غير موجودة.';
        }

        return response()->json([
            'success' => false,
            'error' => $message,
            'message' => config('app.debug') ? $e->getMessage() : 'System error occurred'
        ], $statusCode);
    }

    /**
     * Handle validation exceptions
     */
    protected function handleValidationException(ValidationException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'بيانات غير صحيحة. يرجى التحقق من المدخلات.',
                'errors' => $e->errors()
            ], 422);
        }

        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput()
            ->with('error', 'بيانات غير صحيحة. يرجى التحقق من المدخلات.');
    }

    /**
     * Handle authentication exceptions
     */
    protected function handleAuthenticationException(AuthenticationException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'يجب تسجيل الدخول للوصول لهذه الصفحة.'
            ], 401);
        }

        return redirect()->route('login')
            ->with('error', 'يجب تسجيل الدخول للوصول لهذه الصفحة.');
    }

    /**
     * Handle authorization exceptions
     */
    protected function handleAuthorizationException(AuthorizationException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'ليس لديك صلاحية للوصول لهذه الصفحة.'
            ], 403);
        }

        return redirect()->back()
            ->with('error', 'ليس لديك صلاحية للوصول لهذه الصفحة.');
    }

    /**
     * Handle model not found exceptions
     */
    protected function handleModelNotFoundException(ModelNotFoundException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'العنصر المطلوب غير موجود.'
            ], 404);
        }

        return redirect()->back()
            ->with('error', 'العنصر المطلوب غير موجود.');
    }

    /**
     * Handle not found exceptions
     */
    protected function handleNotFoundException(NotFoundHttpException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'الصفحة المطلوبة غير موجودة.'
            ], 404);
        }

        return redirect()->back()
            ->with('error', 'الصفحة المطلوبة غير موجودة.');
    }

    /**
     * Handle method not allowed exceptions
     */
    protected function handleMethodNotAllowedException(MethodNotAllowedHttpException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'طريقة الطلب غير مسموحة.'
            ], 405);
        }

        return redirect()->back()
            ->with('error', 'طريقة الطلب غير مسموحة.');
    }

    /**
     * Handle token mismatch exceptions
     */
    protected function handleTokenMismatchException(TokenMismatchException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'انتهت صلاحية الجلسة. يرجى تحديث الصفحة والمحاولة مرة أخرى.'
            ], 419);
        }

        return redirect()->back()
            ->with('error', 'انتهت صلاحية الجلسة. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
    }

    /**
     * Handle query exceptions
     */
    protected function handleQueryException(QueryException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في قاعدة البيانات. يرجى المحاولة مرة أخرى.'
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'حدث خطأ في قاعدة البيانات. يرجى المحاولة مرة أخرى.');
    }

    /**
     * Handle generic exceptions
     */
    protected function handleGenericException(Throwable $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في النظام. يرجى المحاولة مرة أخرى.'
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'حدث خطأ في النظام. يرجى المحاولة مرة أخرى.');
    }
}
