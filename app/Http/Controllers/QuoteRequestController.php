<?php

namespace App\Http\Controllers;

use App\Mail\QuoteRequestReceived;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuoteRequestController extends Controller
{
    public function create()
    {
        return view('pages.request-quote');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:120'],
            'email'        => ['required', 'email', 'max:160'],
            'phone'        => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'cif'          => ['nullable', 'string', 'max:40'],
            'service_type' => ['required', 'string', 'max:80'],
            'quantity'     => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'deadline'     => ['nullable', 'string', 'max:80'],
            'budget_range' => ['nullable', 'string', 'max:80'],
            'description'  => ['required', 'string', 'max:5000'],
            'attachment'   => ['nullable', 'file', 'max:8192', 'mimes:pdf,jpg,jpeg,png,ai,eps,svg,zip'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('quote-requests', 'public');
        }

        $quoteRequest = QuoteRequest::create([
            'reference'       => QuoteRequest::generateReference(),
            'user_id'         => Auth::id(),
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'] ?? null,
            'company_name'    => $data['company_name'] ?? null,
            'cif'             => $data['cif'] ?? null,
            'service_type'    => $data['service_type'],
            'quantity'        => $data['quantity'] ?? null,
            'deadline'        => $data['deadline'] ?? null,
            'budget_range'    => $data['budget_range'] ?? null,
            'description'     => $data['description'],
            'attachment_path' => $attachmentPath,
            'status'          => 'new',
        ]);

        try {
            Mail::to(config('mail.inbox'))
                ->send(new QuoteRequestReceived($quoteRequest));
        } catch (\Throwable $e) {
            Log::error('Failed to send quote-request email', [
                'reference' => $quoteRequest->reference,
                'error'     => $e->getMessage(),
            ]);
        }

        return redirect()->route('request-quote')
            ->with('success', 'Sol·licitud rebuda. Et respondrem en menys de 24 hores laborables.');
    }
}
