<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function submit(Request $request): JsonResponse|RedirectResponse
    {
        // Validate the five fields — nothing else
        $validated = $request->validate([
            'name'            => ['required', 'string', 'min:2', 'max:100'],
            'company'         => ['required', 'string', 'min:2', 'max:150'],
            'email'           => ['required', 'email', 'max:254'],
            'service'         => ['required', Rule::in([
                'software-development', 'data-analytics', 'software-qa',
                'business-analysis', 'healthcare-technology', 'not-sure',
            ])],
            'project_details' => ['required', 'string', 'min:20', 'max:2000'],
        ], [
            'name.required'            => 'Your name is required.',
            'name.min'                 => 'Please enter at least 2 characters.',
            'company.required'         => 'Your company name is required.',
            'email.required'           => 'A work email address is required.',
            'email.email'              => 'Please enter a valid email address.',
            'service.required'         => 'Please select a service.',
            'service.in'               => 'Please select a valid service.',
            'project_details.required' => 'Please describe your project.',
            'project_details.min'      => 'Please write at least 20 characters.',
            'project_details.max'      => 'Please keep it under 2,000 characters.',
        ]);

        // Save to database
        try {
            Inquiry::create([
                'name'            => strip_tags($validated['name']),
                'company'         => strip_tags($validated['company']),
                'email'           => $validated['email'],
                'service'         => $validated['service'],
                'project_details' => strip_tags($validated['project_details']),
                'ip_address'      => $request->ip(),
                'user_agent'      => substr((string) $request->userAgent(), 0, 500),
                'recaptcha_score' => null,
                'mail_sent'       => false,
            ]);
        } catch (\Throwable $e) {
            Log::error('Inquiry save failed: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Could not save your inquiry. Please try again.'], 500);
            }
            return back()->withInput()->withErrors(['general' => 'Something went wrong. Please try again.']);
        }

        // Success
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('home')->with('inquiry_success', true);
    }
}