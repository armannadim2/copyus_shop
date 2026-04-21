<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\PrintJob;
use App\Models\PrintProductionLog;
use App\Models\PrintTemplate;
use App\Models\User;
use App\Notifications\ArtworkUploadedNotification;
use App\Services\PrintPriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PrintJobController extends Controller
{
    public function __construct(private PrintPriceCalculator $calculator) {}

    /**
     * Gallery of all active print templates.
     */
    public function index()
    {
        $templates = PrintTemplate::active()
            ->with(['options'])
            ->orderBy('sort_order')
            ->get();

        return view('shop.print.index', compact('templates'));
    }

    /**
     * Interactive configurator for one template.
     */
    public function builder(PrintTemplate $template)
    {
        abort_unless($template->is_active, 404);

        $template->load([
            'options.activeValues',
            'quantityTiers' => fn($q) => $q->where('is_active', true)->orderBy('min_quantity'),
            'artworks'      => fn($q) => $q->orderBy('sort_order'),
        ]);

        // Compute initial price with defaults
        $defaultConfig = [];
        foreach ($template->options as $option) {
            $default = $option->activeValues->firstWhere('is_default', true)
                    ?? $option->activeValues->first();
            if ($default) {
                $defaultConfig[$option->key] = $default->value_key;
            }
        }

        $initialPrice = $this->calculator->calculate($template, $defaultConfig, 100, Auth::user());

        return view('shop.print.builder', compact('template', 'defaultConfig', 'initialPrice'));
    }

    /**
     * AJAX: recalculate price live as user changes options/qty.
     */
    public function calculate(Request $request, PrintTemplate $template)
    {
        abort_unless($template->is_active, 404);

        $request->validate([
            'config'   => ['nullable', 'array'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $config   = $request->input('config', []);
        $quantity = (int) $request->input('quantity', 1);

        // Validate compatibility
        $errors = $this->calculator->validate($template, $config);

        // Enforce minimum quantity from tiers
        $minQty = $template->quantityTiers()->where('is_active', true)->min('min_quantity') ?? 1;
        if ($quantity < $minQty) {
            $errors[] = [
                'type'    => 'error',
                'message' => 'La quantitat mínima per a aquesta plantilla és ' . number_format($minQty, 0, ',', '.') . ' unitats.',
            ];
        }

        $result = $this->calculator->calculate($template, $config, max($quantity, 1), Auth::user());

        return response()->json([
            'unit_price'             => $result->unitPrice,
            'total_price'            => $result->totalPrice,
            'tier_discount_percent'  => $result->tierDiscountPercent,
            'production_days'        => $result->productionDays,
            'breakdown'              => $result->breakdown,
            'unit_price_fmt'         => number_format($result->unitPrice, 4, ',', '.'),
            'total_price_fmt'        => number_format($result->totalPrice, 2, ',', '.'),
            'total_with_vat_fmt'     => number_format($result->totalPrice * (1 + $template->vat_rate / 100), 2, ',', '.'),
            'vat_amount_fmt'         => number_format($result->totalPrice * ($template->vat_rate / 100), 2, ',', '.'),
            'compatibility_errors'   => $errors,
            'min_quantity'           => $minQty,
        ]);
    }

    /**
     * Customer artwork upload for an existing print job.
     */
    public function uploadArtwork(Request $request, PrintJob $job)
    {
        // Only the owner can upload, and only while the job is not completed/cancelled
        abort_unless($job->user_id === Auth::id(), 403);
        abort_unless(in_array($job->status, ['ordered', 'in_production']), 403);

        $request->validate([
            'artwork' => ['required', 'file', 'mimes:pdf,ai,eps,svg,png,jpg,jpeg,tiff,psd', 'max:51200'],
        ]);

        if ($job->artwork_path) {
            Storage::disk('public')->delete($job->artwork_path);
        }

        $path = $request->file('artwork')->store('print/jobs/' . $job->id . '/artwork', 'public');

        $job->update(['artwork_path' => $path]);

        PrintProductionLog::create([
            'print_job_id'    => $job->id,
            'admin_id'        => null,
            'event'           => 'artwork_uploaded',
            'previous_status' => $job->status,
            'new_status'      => $job->status,
            'note'            => 'Arxiu de disseny carregat pel client.',
        ]);

        // Notify all admins that artwork is ready for review
        User::where('role', 'admin')->each(
            fn($admin) => $admin->notify(new ArtworkUploadedNotification($job))
        );

        return back()->with('success', '✅ Arxiu de disseny carregat correctament.');
    }

    /**
     * Customer's full print job history.
     */
    public function myJobs(Request $request)
    {
        $query = PrintJob::where('user_id', Auth::id())
            ->whereNotIn('status', ['draft', 'in_cart'])
            ->with('template');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(15)->withQueryString();

        $counts = PrintJob::where('user_id', Auth::id())
            ->whereNotIn('status', ['draft', 'in_cart'])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('shop.print.my_jobs', compact('jobs', 'counts'));
    }

    /**
     * Clone a completed/cancelled print job back into the cart.
     */
    public function reorder(PrintJob $job)
    {
        abort_unless($job->user_id === Auth::id(), 403);
        abort_unless(in_array($job->status, ['completed', 'cancelled']), 403);

        $template = $job->template;
        abort_unless($template && $template->is_active, 404);

        $template->load([
            'options.activeValues',
            'quantityTiers' => fn($q) => $q->where('is_active', true)->orderBy('min_quantity'),
        ]);

        $result = $this->calculator->calculate(
            $template,
            $job->configuration ?? [],
            $job->quantity,
            Auth::user()
        );

        DB::transaction(function () use ($job, $template, $result) {
            $newJob = PrintJob::create([
                'user_id'           => Auth::id(),
                'print_template_id' => $template->id,
                'status'            => 'in_cart',
                'configuration'     => $job->configuration,
                'quantity'          => $job->quantity,
                'unit_price'        => $result->unitPrice,
                'total_price'       => $result->totalPrice,
                'production_days'   => $result->productionDays,
                'artwork_notes'     => $job->artwork_notes,
            ]);

            CartItem::create([
                'user_id'                => Auth::id(),
                'product_id'             => null,
                'print_job_id'           => $newJob->id,
                'quantity'               => $newJob->quantity,
                'type'                   => 'cart',
                'unit_price'             => $result->unitPrice,
                'configuration_snapshot' => array_merge($job->configuration ?? [], [
                    '_template'      => $template->slug,
                    '_template_name' => $template->getTranslation('name', app()->getLocale()),
                    '_breakdown'     => $result->breakdown,
                ]),
            ]);
        });

        return redirect()->route('cart.index')
            ->with('success', '🖨️ Treball reutilitzat i afegit a la cistella!');
    }

    /**
     * Customer cancels an ordered print job (before it enters production).
     */
    public function cancel(PrintJob $job)
    {
        abort_unless($job->user_id === Auth::id(), 403);
        abort_unless($job->status === 'ordered', 403);

        $job->update(['status' => 'cancelled']);

        PrintProductionLog::create([
            'print_job_id'    => $job->id,
            'admin_id'        => null,
            'event'           => 'status_change',
            'previous_status' => 'ordered',
            'new_status'      => 'cancelled',
            'note'            => 'Cancel·lat pel client.',
        ]);

        return back()->with('success', 'Treball d\'impressió cancel·lat correctament.');
    }

    /**
     * Customer updates their artwork notes for an active print job.
     */
    public function updateNotes(Request $request, PrintJob $job)
    {
        abort_unless($job->user_id === Auth::id(), 403);
        abort_unless(in_array($job->status, ['ordered', 'in_production']), 403);

        $request->validate([
            'artwork_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $job->update(['artwork_notes' => $request->artwork_notes]);

        return back()->with('success', 'Notes guardades correctament.');
    }

    /**
     * Customer confirms they have received a completed print job.
     */
    public function confirmReceived(PrintJob $job)
    {
        abort_unless($job->user_id === Auth::id(), 403);
        abort_unless($job->status === 'completed', 403);
        abort_unless(!$job->received_at, 403);

        $job->update(['received_at' => now()]);

        PrintProductionLog::create([
            'print_job_id'    => $job->id,
            'admin_id'        => null,
            'event'           => 'received_by_customer',
            'previous_status' => 'completed',
            'new_status'      => 'completed',
            'note'            => 'Recepció confirmada pel client.',
        ]);

        return back()->with('success', '✅ Recepció confirmada. Gràcies!');
    }

    /**
     * Customer deletes their uploaded artwork file.
     */
    public function deleteArtwork(PrintJob $job)
    {
        abort_unless($job->user_id === Auth::id(), 403);
        abort_unless(in_array($job->status, ['ordered', 'in_production']), 403);
        abort_unless($job->artwork_path, 404);

        Storage::disk('public')->delete($job->artwork_path);
        $job->update(['artwork_path' => null]);

        PrintProductionLog::create([
            'print_job_id'    => $job->id,
            'admin_id'        => null,
            'event'           => 'artwork_deleted',
            'previous_status' => $job->status,
            'new_status'      => $job->status,
            'note'            => 'Arxiu de disseny eliminat pel client.',
        ]);

        return back()->with('success', 'Arxiu de disseny eliminat. Ara pots pujar-ne un de nou.');
    }

    /**
     * Create PrintJob and add it to the cart.
     */
    public function addToCart(Request $request, PrintTemplate $template)
    {
        abort_unless($template->is_active, 404);

        $minQty = $template->quantityTiers()->where('is_active', true)->min('min_quantity') ?? 1;

        $request->validate([
            'config'          => ['nullable', 'array'],
            'quantity'        => ['required', 'integer', 'min:' . $minQty],
            'artwork_notes'   => ['nullable', 'string', 'max:1000'],
        ]);

        $config   = $request->input('config', []);
        $quantity = (int) $request->input('quantity');

        // Block incompatible configurations
        $errors = $this->calculator->validate($template, $config);
        $hardErrors = array_filter($errors, fn($e) => $e['type'] === 'error');
        if (!empty($hardErrors)) {
            return back()->withErrors(['config' => array_column($hardErrors, 'message')])->withInput();
        }

        $result = $this->calculator->calculate($template, $config, $quantity, Auth::user());

        DB::transaction(function () use ($request, $template, $config, $quantity, $result) {
            $job = PrintJob::create([
                'user_id'          => Auth::id(),
                'print_template_id'=> $template->id,
                'status'           => 'in_cart',
                'configuration'    => $config,
                'quantity'         => $quantity,
                'unit_price'       => $result->unitPrice,
                'total_price'      => $result->totalPrice,
                'production_days'  => $result->productionDays,
                'artwork_notes'    => $request->artwork_notes,
            ]);

            CartItem::create([
                'user_id'                => Auth::id(),
                'product_id'             => null,
                'print_job_id'           => $job->id,
                'quantity'               => $quantity,
                'type'                   => 'cart',
                'unit_price'             => $result->unitPrice,
                'configuration_snapshot' => array_merge($config, [
                    '_template' => $template->slug,
                    '_template_name' => $template->getTranslation('name', app()->getLocale()),
                    '_breakdown' => $result->breakdown,
                ]),
            ]);
        });

        return redirect()->route('cart.index')
            ->with('success', '🖨️ Treball d\'impressió afegit a la cistella!');
    }
}
