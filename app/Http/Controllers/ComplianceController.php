<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComplianceController extends Controller
{
    /**
     * Display the Terms and Conditions page.
     */
    public function terms()
    {
        try {
            $settings = Setting::getCached();
            $content = $settings->terms_and_conditions ?? '# Terms and Conditions';
            return view('compliance.terms', compact('content'));
        } catch (\Exception $e) {
            Log::error('Error rendering terms page: ' . $e->getMessage());
            abort(500, 'Error rendering page.');
        }
    }

    /**
     * Display the Privacy Policy page.
     */
    public function privacy()
    {
        try {
            $settings = Setting::getCached();
            $content = $settings->privacy_policy ?? '# Privacy Policy';
            return view('compliance.privacy', compact('content'));
        } catch (\Exception $e) {
            Log::error('Error rendering privacy page: ' . $e->getMessage());
            abort(500, 'Error rendering page.');
        }
    }

    /**
     * Display the Refund and Cancellation Policy page.
     */
    public function refund()
    {
        try {
            $settings = Setting::getCached();
            $content = $settings->refund_and_cancellation_policy ?? '# Refund and Cancellation Policy';
            return view('compliance.refund', compact('content'));
        } catch (\Exception $e) {
            Log::error('Error rendering refund page: ' . $e->getMessage());
            abort(500, 'Error rendering page.');
        }
    }
}
