<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|max:255']);

        $email = strtolower(trim($request->input('email')));

        $existing = NewsletterSubscription::where('email', $email)->first();

        if ($existing) {
            if (! $existing->is_active) {
                $existing->update(['is_active' => true, 'unsubscribed_at' => null]);
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'duplicate']);
        }

        NewsletterSubscription::create([
            'email'      => $email,
            'ip_address' => $request->ip(),
            'is_active'  => true,
        ]);

        return response()->json(['status' => 'success']);
    }
}
