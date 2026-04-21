<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintCompatibilityRule;
use App\Models\PrintJob;
use App\Models\PrintQuantityTier;
use App\Models\PrintTemplate;
use App\Models\PrintTemplateArtwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPrintTemplateController extends Controller
{
    public function index(Request $request)
    {
        $templates = PrintTemplate::withCount(['jobs', 'options'])
            ->when($request->search, fn($q, $s) =>
                $q->whereJsonContains('name->ca', $s)
                  ->orWhereJsonContains('name->es', $s)
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.print.templates.index', compact('templates'));
    }

    public function create()
    {
        $template = null;
        return view('admin.print.templates.create', compact('template'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTemplate($request);

        DB::transaction(function () use ($request, $validated) {
            $template = PrintTemplate::create([
                'slug'                 => Str::slug($validated['name_ca']),
                'base_price'           => $validated['base_price'],
                'vat_rate'             => $validated['vat_rate'],
                'base_production_days' => $validated['base_production_days'],
                'sort_order'           => $validated['sort_order'] ?? 0,
                'is_active'            => $request->boolean('is_active', true),
                'icon'                 => $validated['icon'] ?? null,
                'name' => [
                    'ca' => $validated['name_ca'],
                    'es' => $validated['name_es'],
                    'en' => $validated['name_en'] ?? $validated['name_es'],
                ],
                'description' => [
                    'ca' => $validated['description_ca'] ?? null,
                    'es' => $validated['description_es'] ?? null,
                    'en' => $validated['description_en'] ?? null,
                ],
            ]);

            $this->syncOptions($request, $template);
            $this->syncQuantityTiers($request, $template);
            $this->syncCompatibilityRules($request, $template);
            $this->syncArtworks($request, $template);
            $this->syncSpecifications($request, $template);
        });

        return redirect()->route('admin.print.templates.index')
            ->with('success', 'Plantilla d\'impressió creada correctament.');
    }

    public function edit(PrintTemplate $template)
    {
        $template->load(['options.values', 'quantityTiers', 'compatibilityRules', 'artworks']);
        return view('admin.print.templates.edit', compact('template'));
    }

    public function update(Request $request, PrintTemplate $template)
    {
        $validated = $this->validateTemplate($request, $template->id);

        DB::transaction(function () use ($request, $validated, $template) {
            $template->update([
                'slug'                 => Str::slug($validated['name_ca']),
                'base_price'           => $validated['base_price'],
                'vat_rate'             => $validated['vat_rate'],
                'base_production_days' => $validated['base_production_days'],
                'sort_order'           => $validated['sort_order'] ?? 0,
                'is_active'            => $request->boolean('is_active'),
                'icon'                 => $validated['icon'] ?? null,
                'name' => [
                    'ca' => $validated['name_ca'],
                    'es' => $validated['name_es'],
                    'en' => $validated['name_en'] ?? $validated['name_es'],
                ],
                'description' => [
                    'ca' => $validated['description_ca'] ?? null,
                    'es' => $validated['description_es'] ?? null,
                    'en' => $validated['description_en'] ?? null,
                ],
            ]);

            $this->syncOptions($request, $template);
            $this->syncQuantityTiers($request, $template);
            $this->syncCompatibilityRules($request, $template);
            $this->syncArtworks($request, $template);
            $this->syncSpecifications($request, $template);
        });

        return redirect()->route('admin.print.templates.index')
            ->with('success', 'Plantilla actualitzada correctament.');
    }

    public function destroy(PrintTemplate $template)
    {
        // Delete artwork files from storage
        foreach ($template->artworks as $artwork) {
            Storage::disk('public')->delete($artwork->file_path);
        }

        // Delete specifications file
        if ($template->specifications_path) {
            Storage::disk('public')->delete($template->specifications_path);
        }

        $template->delete();

        return redirect()->route('admin.print.templates.index')
            ->with('success', 'Plantilla eliminada correctament.');
    }

    public function toggle(PrintTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        return back()->with(
            'success',
            'Plantilla ' . ($template->is_active ? 'activada' : 'desactivada') . ' correctament.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Private sync helpers
    |--------------------------------------------------------------------------
    */

    private function validateTemplate(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name_ca'              => ['required', 'string', 'max:150'],
            'name_es'              => ['required', 'string', 'max:150'],
            'name_en'              => ['nullable', 'string', 'max:150'],
            'description_ca'       => ['nullable', 'string'],
            'description_es'       => ['nullable', 'string'],
            'description_en'       => ['nullable', 'string'],
            'icon'                 => ['nullable', 'string', 'max:20'],
            'base_price'           => ['required', 'numeric', 'min:0'],
            'vat_rate'             => ['required', 'numeric', 'min:0', 'max:100'],
            'base_production_days' => ['required', 'integer', 'min:1', 'max:90'],
            'sort_order'           => ['nullable', 'integer'],
            // Options
            'options.*.key'                         => ['nullable', 'string', 'max:80'],
            'options.*.label_ca'                    => ['nullable', 'string', 'max:150'],
            'options.*.label_es'                    => ['nullable', 'string', 'max:150'],
            'options.*.label_en'                    => ['nullable', 'string', 'max:150'],
            'options.*.input_type'                  => ['nullable', 'in:select,radio,toggle,number'],
            'options.*.sort_order'                  => ['nullable', 'integer'],
            // Option values
            'options.*.values.*.value_key'              => ['nullable', 'string', 'max:80'],
            'options.*.values.*.label_ca'               => ['nullable', 'string', 'max:150'],
            'options.*.values.*.label_es'               => ['nullable', 'string', 'max:150'],
            'options.*.values.*.label_en'               => ['nullable', 'string', 'max:150'],
            'options.*.values.*.price_modifier'         => ['nullable', 'numeric'],
            'options.*.values.*.price_modifier_type'    => ['nullable', 'in:flat,percent'],
            'options.*.values.*.production_days_modifier' => ['nullable', 'integer'],
            // Quantity tiers
            'tiers.*.min_quantity'    => ['nullable', 'integer', 'min:1'],
            'tiers.*.discount_percent'=> ['nullable', 'numeric', 'min:0', 'max:100'],
            'tiers.*.label_ca'        => ['nullable', 'string', 'max:100'],
            'tiers.*.label_es'        => ['nullable', 'string', 'max:100'],
            'tiers.*.label_en'        => ['nullable', 'string', 'max:100'],
            // Compatibility rules
            'rules.*.rule_type'            => ['nullable', 'in:incompatible,requires,warning'],
            'rules.*.condition_option_key' => ['nullable', 'string', 'max:80'],
            'rules.*.condition_value_key'  => ['nullable', 'string', 'max:80'],
            'rules.*.target_option_key'    => ['nullable', 'string', 'max:80'],
            'rules.*.target_value_key'     => ['nullable', 'string', 'max:80'],
            'rules.*.message_ca'           => ['nullable', 'string', 'max:300'],
            'rules.*.message_es'           => ['nullable', 'string', 'max:300'],
            'rules.*.message_en'           => ['nullable', 'string', 'max:300'],
            // Artworks
            'artworks.*'                   => ['nullable', 'image', 'max:8192'],
            // Specifications
            'specifications'               => ['nullable', 'file', 'mimes:pdf,ai,eps,zip,psd', 'max:51200'],
            'specifications_label'         => ['nullable', 'string', 'max:150'],
            'delete_specifications'        => ['nullable', 'boolean'],
        ]);
    }

    private function syncOptions(Request $request, PrintTemplate $template): void
    {
        if (!$request->has('options')) {
            return;
        }

        $submittedOptionIds = [];

        foreach ($request->options as $i => $optData) {
            if (empty($optData['key']) || empty($optData['label_ca'])) continue;

            $optAttrs = [
                'key'        => $optData['key'],
                'input_type' => $optData['input_type'] ?? 'select',
                'is_required'=> isset($optData['is_required']),
                'sort_order' => $optData['sort_order'] ?? $i,
                'label'      => [
                    'ca' => $optData['label_ca'],
                    'es' => $optData['label_es'] ?? $optData['label_ca'],
                    'en' => $optData['label_en'] ?? $optData['label_es'] ?? $optData['label_ca'],
                ],
            ];

            if (!empty($optData['id'])) {
                $option = $template->options()->find($optData['id']);
                if ($option) {
                    $option->update($optAttrs);
                    $submittedOptionIds[] = $option->id;
                }
            } else {
                $option = $template->options()->create($optAttrs);
                $submittedOptionIds[] = $option->id;
            }

            // Sync this option's values
            $this->syncOptionValues($optData['values'] ?? [], $option);
        }

        // Delete removed options
        $template->options()->whereNotIn('id', $submittedOptionIds)->each(function ($opt) {
            $opt->values()->delete();
            $opt->delete();
        });
    }

    private function syncOptionValues(array $valuesData, $option): void
    {
        $submittedValueIds = [];

        foreach ($valuesData as $j => $valData) {
            if (empty($valData['value_key']) || empty($valData['label_ca'])) continue;

            $valAttrs = [
                'value_key'                => $valData['value_key'],
                'price_modifier'           => $valData['price_modifier'] ?? 0,
                'price_modifier_type'      => $valData['price_modifier_type'] ?? 'flat',
                'production_days_modifier' => $valData['production_days_modifier'] ?? 0,
                'is_default'               => isset($valData['is_default']),
                'is_active'                => isset($valData['is_active']) ? (bool)$valData['is_active'] : true,
                'sort_order'               => $valData['sort_order'] ?? $j,
                'label'                    => [
                    'ca' => $valData['label_ca'],
                    'es' => $valData['label_es'] ?? $valData['label_ca'],
                    'en' => $valData['label_en'] ?? $valData['label_es'] ?? $valData['label_ca'],
                ],
            ];

            if (!empty($valData['id'])) {
                $value = $option->values()->find($valData['id']);
                if ($value) {
                    $value->update($valAttrs);
                    $submittedValueIds[] = $value->id;
                }
            } else {
                $value = $option->values()->create($valAttrs);
                $submittedValueIds[] = $value->id;
            }
        }

        $option->values()->whereNotIn('id', $submittedValueIds)->delete();
    }

    private function syncQuantityTiers(Request $request, PrintTemplate $template): void
    {
        if (!$request->has('tiers')) {
            $template->quantityTiers()->delete();
            return;
        }

        $submittedIds = [];

        foreach ($request->tiers as $tierData) {
            if (empty($tierData['min_quantity']) || !isset($tierData['discount_percent'])) continue;

            $attrs = [
                'min_quantity'     => $tierData['min_quantity'],
                'discount_percent' => $tierData['discount_percent'],
                'is_active'        => isset($tierData['is_active']),
                'label'            => [
                    'ca' => $tierData['label_ca'] ?? null,
                    'es' => $tierData['label_es'] ?? null,
                    'en' => $tierData['label_en'] ?? null,
                ],
            ];

            if (!empty($tierData['id'])) {
                $tier = $template->quantityTiers()->find($tierData['id']);
                if ($tier) {
                    $tier->update($attrs);
                    $submittedIds[] = $tier->id;
                }
            } else {
                $tier = $template->quantityTiers()->create($attrs);
                $submittedIds[] = $tier->id;
            }
        }

        $template->quantityTiers()->whereNotIn('id', $submittedIds)->delete();
    }

    private function syncCompatibilityRules(Request $request, PrintTemplate $template): void
    {
        // Rebuild all rules from scratch (simpler than diffing)
        $template->compatibilityRules()->delete();

        if (!$request->has('rules')) return;

        foreach ($request->rules as $ruleData) {
            if (
                empty($ruleData['condition_option_key']) ||
                empty($ruleData['condition_value_key']) ||
                empty($ruleData['target_option_key'])
            ) continue;

            $template->compatibilityRules()->create([
                'rule_type'            => $ruleData['rule_type'] ?? 'incompatible',
                'condition_option_key' => $ruleData['condition_option_key'],
                'condition_value_key'  => $ruleData['condition_value_key'],
                'target_option_key'    => $ruleData['target_option_key'],
                'target_value_key'     => $ruleData['target_value_key'] ?? null,
                'message'              => [
                    'ca' => $ruleData['message_ca'] ?? null,
                    'es' => $ruleData['message_es'] ?? null,
                    'en' => $ruleData['message_en'] ?? null,
                ],
            ]);
        }
    }

    private function syncArtworks(Request $request, PrintTemplate $template): void
    {
        // Delete artworks marked for removal
        if ($request->has('delete_artworks')) {
            foreach ((array) $request->delete_artworks as $artworkId) {
                $artwork = PrintTemplateArtwork::find($artworkId);
                if ($artwork && $artwork->print_template_id === $template->id) {
                    Storage::disk('public')->delete($artwork->file_path);
                    $artwork->delete();
                }
            }
        }

        if ($request->hasFile('artworks')) {
            $sort = $template->artworks()->max('sort_order') ?? 0;
            foreach ($request->file('artworks') as $file) {
                $path = $file->store('print/templates/' . $template->id, 'public');
                $template->artworks()->create([
                    'file_path'  => $path,
                    'mime_type'  => $file->getMimeType(),
                    'sort_order' => ++$sort,
                ]);
            }
        }
    }

    private function syncSpecifications(Request $request, PrintTemplate $template): void
    {
        // Delete existing spec file if requested
        if ($request->boolean('delete_specifications') && $template->specifications_path) {
            Storage::disk('public')->delete($template->specifications_path);
            $template->update(['specifications_path' => null, 'specifications_label' => null]);
        }

        if ($request->hasFile('specifications')) {
            // Replace old file
            if ($template->specifications_path) {
                Storage::disk('public')->delete($template->specifications_path);
            }

            $path = $request->file('specifications')
                ->store('print/templates/' . $template->id . '/specs', 'public');

            $template->update([
                'specifications_path'  => $path,
                'specifications_label' => $request->input('specifications_label') ?: null,
            ]);
        } elseif ($request->filled('specifications_label')) {
            $template->update(['specifications_label' => $request->input('specifications_label')]);
        }
    }
}
