<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\PrintJob;
use App\Models\SavedPrintConfig;
use App\Services\PrintPriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SavedPrintConfigController extends Controller
{
    public function __construct(private PrintPriceCalculator $calculator) {}

    /**
     * List all saved configurations for the current user.
     */
    public function index()
    {
        $configs = SavedPrintConfig::where('user_id', Auth::id())
            ->with('template')
            ->latest()
            ->paginate(15);

        return view('shop.print.saved_configs', compact('configs'));
    }

    /**
     * Save current builder state as a named configuration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'print_template_id' => ['required', 'integer', 'exists:print_templates,id'],
            'name'              => ['required', 'string', 'max:150'],
            'configuration'     => ['nullable', 'array'],
            'quantity'          => ['required', 'integer', 'min:1'],
            'artwork_notes'     => ['nullable', 'string', 'max:1000'],
        ]);

        // Limit to 20 saved configs per user
        $count = SavedPrintConfig::where('user_id', Auth::id())->count();
        if ($count >= 20) {
            return response()->json(['error' => 'Has arribat al límit de 20 configuracions guardades.'], 422);
        }

        $config = SavedPrintConfig::create([
            'user_id'           => Auth::id(),
            'print_template_id' => $request->print_template_id,
            'name'              => $request->name,
            'configuration'     => $request->input('configuration', []),
            'quantity'          => $request->quantity,
            'artwork_notes'     => $request->artwork_notes,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $config->id, 'name' => $config->name]);
        }

        return back()->with('success', 'Configuració "' . $config->name . '" guardada correctament.');
    }

    /**
     * Delete a saved configuration.
     */
    public function destroy(SavedPrintConfig $config)
    {
        abort_unless($config->user_id === Auth::id(), 403);
        $config->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Configuració eliminada.');
    }

    /**
     * Add a saved configuration directly to the cart.
     */
    public function addToCart(SavedPrintConfig $config)
    {
        abort_unless($config->user_id === Auth::id(), 403);

        $template = $config->template;
        abort_unless($template && $template->is_active, 404);

        $template->load([
            'options.activeValues',
            'quantityTiers' => fn($q) => $q->where('is_active', true)->orderBy('min_quantity'),
        ]);

        $result = $this->calculator->calculate(
            $template,
            $config->configuration ?? [],
            $config->quantity,
            Auth::user()
        );

        DB::transaction(function () use ($config, $template, $result) {
            $job = PrintJob::create([
                'user_id'           => Auth::id(),
                'print_template_id' => $template->id,
                'status'            => 'in_cart',
                'configuration'     => $config->configuration,
                'quantity'          => $config->quantity,
                'unit_price'        => $result->unitPrice,
                'total_price'       => $result->totalPrice,
                'production_days'   => $result->productionDays,
                'artwork_notes'     => $config->artwork_notes,
            ]);

            CartItem::create([
                'user_id'                => Auth::id(),
                'product_id'             => null,
                'print_job_id'           => $job->id,
                'quantity'               => $config->quantity,
                'type'                   => 'cart',
                'unit_price'             => $result->unitPrice,
                'configuration_snapshot' => array_merge($config->configuration ?? [], [
                    '_template'      => $template->slug,
                    '_template_name' => $template->getTranslation('name', app()->getLocale()),
                    '_breakdown'     => $result->breakdown,
                    '_saved_config'  => $config->name,
                ]),
            ]);
        });

        return redirect()->route('cart.index')
            ->with('success', '🖨️ "' . $config->name . '" afegit a la cistella!');
    }
}
