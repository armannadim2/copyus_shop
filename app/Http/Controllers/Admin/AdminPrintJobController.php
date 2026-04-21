<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintJob;
use App\Models\PrintProductionLog;
use App\Notifications\PrintJobStatusUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminPrintJobController extends Controller
{
    private const VALID_TRANSITIONS = [
        'ordered'       => ['in_production', 'cancelled'],
        'in_production' => ['completed',     'cancelled'],
        'completed'     => [],
        'cancelled'     => [],
    ];

    public function index(Request $request)
    {
        $query = PrintJob::with(['user', 'template'])
            ->whereNotIn('status', ['draft', 'in_cart']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        $jobs = $query->orderByRaw("FIELD(status, 'ordered', 'in_production', 'completed', 'cancelled')")
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $counts = PrintJob::whereNotIn('status', ['draft', 'in_cart'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.print.jobs.index', compact('jobs', 'counts'));
    }

    public function show(PrintJob $job)
    {
        $job->load([
            'user',
            'template.options.values',
            'productionLog.admin',
        ]);

        // Auto-mark related artwork_uploaded notifications as read
        Auth::user()->unreadNotifications()
            ->whereJsonContains('data->type', 'artwork_uploaded')
            ->whereJsonContains('data->print_job_id', $job->id)
            ->update(['read_at' => now()]);

        $allowedTransitions = self::VALID_TRANSITIONS[$job->status] ?? [];

        return view('admin.print.jobs.show', compact('job', 'allowedTransitions'));
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'job_ids'    => ['required', 'array', 'min:1'],
            'job_ids.*'  => ['integer', 'exists:print_jobs,id'],
            'status'     => ['required', 'in:in_production,completed,cancelled'],
        ]);

        $jobs = PrintJob::whereIn('id', $request->job_ids)
            ->whereNotIn('status', ['draft', 'in_cart'])
            ->get();

        $moved = 0;

        foreach ($jobs as $job) {
            $allowed = self::VALID_TRANSITIONS[$job->status] ?? [];
            if (!in_array($request->status, $allowed)) {
                continue;
            }

            $previousStatus = $job->status;

            $job->update([
                'status'      => $request->status,
                'produced_at' => $request->status === 'completed' ? now() : $job->produced_at,
            ]);

            PrintProductionLog::create([
                'print_job_id'    => $job->id,
                'admin_id'        => Auth::id(),
                'event'           => 'status_change',
                'previous_status' => $previousStatus,
                'new_status'      => $request->status,
                'note'            => 'Canvi massiu d\'estat.',
            ]);

            if (in_array($request->status, ['in_production', 'completed', 'cancelled'])) {
                $job->user?->notify(new PrintJobStatusUpdatedNotification($job, $previousStatus));
            }

            $moved++;
        }

        return back()->with('success', $moved . ' treball(s) actualitzat(s) a "' . $request->status . '".');
    }

    public function updateStatus(Request $request, PrintJob $job)
    {
        $request->validate([
            'status' => ['required', 'in:in_production,completed,cancelled'],
            'note'   => ['nullable', 'string', 'max:1000'],
        ]);

        $allowed = self::VALID_TRANSITIONS[$job->status] ?? [];
        if (!in_array($request->status, $allowed)) {
            return back()->with('error', 'Transició d\'estat no permesa.');
        }

        $previousStatus = $job->status;

        $job->update([
            'status'      => $request->status,
            'produced_at' => $request->status === 'completed' ? now() : $job->produced_at,
        ]);

        PrintProductionLog::create([
            'print_job_id'    => $job->id,
            'admin_id'        => Auth::id(),
            'event'           => 'status_change',
            'previous_status' => $previousStatus,
            'new_status'      => $request->status,
            'note'            => $request->note,
        ]);

        // Notify customer on meaningful transitions
        if (in_array($request->status, ['in_production', 'completed', 'cancelled'])) {
            $job->user?->notify(new PrintJobStatusUpdatedNotification($job, $previousStatus));
        }

        return back()->with('success', 'Estat actualitzat a "' . $request->status . '".');
    }

    public function setDelivery(Request $request, PrintJob $job)
    {
        $request->validate([
            'expected_delivery_at' => ['required', 'date', 'after_or_equal:today'],
            'admin_notes'          => ['nullable', 'string', 'max:2000'],
        ]);

        $job->update([
            'expected_delivery_at' => $request->expected_delivery_at,
            'admin_notes'          => $request->admin_notes,
        ]);

        return back()->with('success', 'Data de lliurament guardada.');
    }

    public function uploadArtwork(Request $request, PrintJob $job)
    {
        $request->validate([
            'artwork' => ['required', 'file', 'mimes:pdf,ai,eps,svg,png,jpg,jpeg,tiff,psd', 'max:51200'],
        ]);

        // Delete old artwork file if exists
        if ($job->artwork_path) {
            Storage::disk('public')->delete($job->artwork_path);
        }

        $path = $request->file('artwork')->store('print/jobs/' . $job->id . '/artwork', 'public');

        $job->update(['artwork_path' => $path]);

        PrintProductionLog::create([
            'print_job_id'    => $job->id,
            'admin_id'        => Auth::id(),
            'event'           => 'artwork_uploaded',
            'previous_status' => $job->status,
            'new_status'      => $job->status,
            'note'            => 'Arxiu de disseny actualitzat per l\'administrador.',
        ]);

        return back()->with('success', 'Arxiu de disseny carregat correctament.');
    }
}
