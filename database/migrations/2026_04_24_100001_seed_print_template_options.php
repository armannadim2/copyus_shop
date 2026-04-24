<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seeds print_options, print_option_values, print_quantity_tiers and
 * print_compatibility_rules for the 31 templates introduced by
 * 2026_04_23_100001_seed_print_templates_catalogue.
 *
 * Re-runnable: wipes existing rows for each target slug before inserting,
 * keyed on template slug so it does not touch other templates (e.g. the
 * Business Cards / Flyers seeded by PrintTemplateSeeder).
 */
return new class extends Migration
{
    private $now;

    public function up(): void
    {
        $this->now = now();

        foreach ($this->allConfigs() as $slug => $cfg) {
            $tpl = DB::table('print_templates')->where('slug', $slug)->first();
            if (!$tpl) {
                continue;
            }

            DB::table('print_options')->where('print_template_id', $tpl->id)->delete();
            DB::table('print_quantity_tiers')->where('print_template_id', $tpl->id)->delete();
            DB::table('print_compatibility_rules')->where('print_template_id', $tpl->id)->delete();

            foreach ($cfg['options'] as $sort => $opt) {
                $optId = DB::table('print_options')->insertGetId([
                    'print_template_id' => $tpl->id,
                    'key'               => $opt['key'],
                    'label'             => $this->jsonl($opt['label']),
                    'input_type'        => $opt['input_type'] ?? 'select',
                    'is_required'       => $opt['is_required'] ?? true,
                    'sort_order'        => $sort + 1,
                    'created_at'        => $this->now,
                    'updated_at'        => $this->now,
                ]);

                foreach ($opt['values'] as $vSort => $v) {
                    DB::table('print_option_values')->insert([
                        'print_option_id'          => $optId,
                        'value_key'                => $v['value_key'],
                        'label'                    => $this->jsonl($v['label']),
                        'price_modifier'           => $v['price_modifier'] ?? 0,
                        'price_modifier_type'      => $v['price_modifier_type'] ?? 'flat',
                        'production_days_modifier' => $v['production_days_modifier'] ?? 0,
                        'is_default'               => $v['is_default'] ?? false,
                        'is_active'                => true,
                        'sort_order'               => $vSort + 1,
                        'created_at'               => $this->now,
                        'updated_at'               => $this->now,
                    ]);
                }
            }

            foreach (($cfg['tiers'] ?? []) as $tier) {
                DB::table('print_quantity_tiers')->insert([
                    'print_template_id' => $tpl->id,
                    'min_quantity'      => $tier['min_quantity'],
                    'discount_percent'  => $tier['discount_percent'],
                    'label'             => $this->jsonl($tier['label']),
                    'is_active'         => true,
                    'created_at'        => $this->now,
                    'updated_at'        => $this->now,
                ]);
            }

            foreach (($cfg['rules'] ?? []) as $r) {
                DB::table('print_compatibility_rules')->insert([
                    'print_template_id'    => $tpl->id,
                    'rule_type'            => $r['rule_type'],
                    'condition_option_key' => $r['condition_option_key'],
                    'condition_value_key'  => $r['condition_value_key'],
                    'target_option_key'    => $r['target_option_key'],
                    'target_value_key'     => $r['target_value_key'] ?? null,
                    'message'              => $this->jsonl($r['message']),
                    'created_at'           => $this->now,
                    'updated_at'           => $this->now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $ids = DB::table('print_templates')
            ->whereIn('slug', array_keys($this->allConfigs()))
            ->pluck('id');

        DB::table('print_compatibility_rules')->whereIn('print_template_id', $ids)->delete();
        DB::table('print_quantity_tiers')->whereIn('print_template_id', $ids)->delete();
        DB::table('print_options')->whereIn('print_template_id', $ids)->delete();
    }

    // ================================================================
    // Helpers
    // ================================================================

    private function jsonl(array $arr): string
    {
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    // --- Tier presets ------------------------------------------------

    private function tiersBulk(): array
    {
        return [
            ['min_quantity' => 100,  'discount_percent' => 0,  'label' => ['ca' => '100 unitats',   'es' => '100 unidades',   'en' => '100 units']],
            ['min_quantity' => 250,  'discount_percent' => 5,  'label' => ['ca' => '250 unitats',   'es' => '250 unidades',   'en' => '250 units']],
            ['min_quantity' => 500,  'discount_percent' => 10, 'label' => ['ca' => '500 unitats',   'es' => '500 unidades',   'en' => '500 units']],
            ['min_quantity' => 1000, 'discount_percent' => 15, 'label' => ['ca' => '1.000 unitats', 'es' => '1.000 unidades', 'en' => '1,000 units']],
            ['min_quantity' => 2500, 'discount_percent' => 20, 'label' => ['ca' => '2.500 unitats', 'es' => '2.500 unidades', 'en' => '2,500 units']],
            ['min_quantity' => 5000, 'discount_percent' => 25, 'label' => ['ca' => '5.000 unitats', 'es' => '5.000 unidades', 'en' => '5,000 units']],
        ];
    }

    private function tiersMedium(): array
    {
        return [
            ['min_quantity' => 25,   'discount_percent' => 0,  'label' => ['ca' => '25 unitats',  'es' => '25 unidades',  'en' => '25 units']],
            ['min_quantity' => 50,   'discount_percent' => 5,  'label' => ['ca' => '50 unitats',  'es' => '50 unidades',  'en' => '50 units']],
            ['min_quantity' => 100,  'discount_percent' => 10, 'label' => ['ca' => '100 unitats', 'es' => '100 unidades', 'en' => '100 units']],
            ['min_quantity' => 250,  'discount_percent' => 15, 'label' => ['ca' => '250 unitats', 'es' => '250 unidades', 'en' => '250 units']],
            ['min_quantity' => 500,  'discount_percent' => 20, 'label' => ['ca' => '500 unitats', 'es' => '500 unidades', 'en' => '500 units']],
        ];
    }

    private function tiersSmallBatch(): array
    {
        return [
            ['min_quantity' => 10,  'discount_percent' => 0,  'label' => ['ca' => '10 unitats',  'es' => '10 unidades',  'en' => '10 units']],
            ['min_quantity' => 25,  'discount_percent' => 5,  'label' => ['ca' => '25 unitats',  'es' => '25 unidades',  'en' => '25 units']],
            ['min_quantity' => 50,  'discount_percent' => 10, 'label' => ['ca' => '50 unitats',  'es' => '50 unidades',  'en' => '50 units']],
            ['min_quantity' => 100, 'discount_percent' => 15, 'label' => ['ca' => '100 unitats', 'es' => '100 unidades', 'en' => '100 units']],
            ['min_quantity' => 250, 'discount_percent' => 20, 'label' => ['ca' => '250 unitats', 'es' => '250 unidades', 'en' => '250 units']],
        ];
    }

    private function tiersLowVolume(): array
    {
        return [
            ['min_quantity' => 1,   'discount_percent' => 0,  'label' => ['ca' => '1 unitat',    'es' => '1 unidad',      'en' => '1 unit']],
            ['min_quantity' => 5,   'discount_percent' => 3,  'label' => ['ca' => '5 unitats',   'es' => '5 unidades',    'en' => '5 units']],
            ['min_quantity' => 10,  'discount_percent' => 8,  'label' => ['ca' => '10 unitats',  'es' => '10 unidades',   'en' => '10 units']],
            ['min_quantity' => 25,  'discount_percent' => 12, 'label' => ['ca' => '25 unitats',  'es' => '25 unidades',   'en' => '25 units']],
            ['min_quantity' => 50,  'discount_percent' => 18, 'label' => ['ca' => '50 unitats',  'es' => '50 unidades',   'en' => '50 units']],
        ];
    }

    private function tiersApparel(): array
    {
        return [
            ['min_quantity' => 1,   'discount_percent' => 0,  'label' => ['ca' => '1 unitat',    'es' => '1 unidad',      'en' => '1 unit']],
            ['min_quantity' => 10,  'discount_percent' => 5,  'label' => ['ca' => '10 unitats',  'es' => '10 unidades',   'en' => '10 units']],
            ['min_quantity' => 25,  'discount_percent' => 10, 'label' => ['ca' => '25 unitats',  'es' => '25 unidades',   'en' => '25 units']],
            ['min_quantity' => 50,  'discount_percent' => 15, 'label' => ['ca' => '50 unitats',  'es' => '50 unidades',   'en' => '50 units']],
            ['min_quantity' => 100, 'discount_percent' => 20, 'label' => ['ca' => '100 unitats', 'es' => '100 unidades',  'en' => '100 units']],
            ['min_quantity' => 250, 'discount_percent' => 25, 'label' => ['ca' => '250 unitats', 'es' => '250 unidades',  'en' => '250 units']],
        ];
    }

    // --- Reusable option builders ------------------------------------

    private function optColorMode(array $keys = ['4_0', '4_4', '1_0', '1_1'], string $default = '4_4'): array
    {
        $catalog = [
            '4_0' => ['ca' => '4+0 (color una cara)',   'es' => '4+0 (color una cara)',  'en' => '4+0 (colour one side)',   'mod' => 0],
            '4_4' => ['ca' => '4+4 (color dues cares)', 'es' => '4+4 (color dos caras)', 'en' => '4+4 (colour both sides)', 'mod' => 0.005],
            '1_0' => ['ca' => '1+0 (B/N una cara)',     'es' => '1+0 (B/N una cara)',    'en' => '1+0 (B&W one side)',      'mod' => -0.005],
            '1_1' => ['ca' => '1+1 (B/N dues cares)',   'es' => '1+1 (B/N dos caras)',   'en' => '1+1 (B&W both sides)',    'mod' => -0.003],
        ];
        $values = [];
        foreach ($keys as $k) {
            $c = $catalog[$k];
            $values[] = [
                'value_key'      => $k,
                'label'          => ['ca' => $c['ca'], 'es' => $c['es'], 'en' => $c['en']],
                'price_modifier' => $c['mod'],
                'is_default'     => $k === $default,
            ];
        }

        return [
            'key'        => 'color_mode',
            'label'      => ['ca' => 'Color', 'es' => 'Color', 'en' => 'Color'],
            'input_type' => 'radio',
            'values'     => $values,
        ];
    }

    private function optPaperWeight(array $weights, string $defaultKey): array
    {
        // flat modifiers in € per unit
        $priceBy = [
            80  => -0.0020, 90  => -0.0010, 100 => 0,      120 => 0.0010, 135 => 0,
            170 => 0.0030,  200 => 0.0060,  250 => 0.0100, 300 => 0.0120, 350 => 0.0150,
            400 => 0.0180,
        ];
        $values = [];
        foreach ($weights as $w) {
            $key = $w . 'gr';
            $values[] = [
                'value_key'      => $key,
                'label'          => ['ca' => "{$w} gr/m²", 'es' => "{$w} gr/m²", 'en' => "{$w} gsm"],
                'price_modifier' => $priceBy[$w] ?? 0,
                'is_default'     => $key === $defaultKey,
            ];
        }

        return [
            'key'        => 'paper_weight',
            'label'      => ['ca' => 'Gramatge', 'es' => 'Gramaje', 'en' => 'Paper weight'],
            'input_type' => 'radio',
            'values'     => $values,
        ];
    }

    private function optPaperType(string $default = 'coated_silk'): array
    {
        return [
            'key'        => 'paper_type',
            'label'      => ['ca' => 'Tipus de paper', 'es' => 'Tipo de papel', 'en' => 'Paper type'],
            'input_type' => 'select',
            'values'     => [
                ['value_key' => 'coated_silk',  'label' => ['ca' => 'Couché Satinat',  'es' => 'Couché Satinado',  'en' => 'Silk Coated'],     'price_modifier' => 0,       'is_default' => $default === 'coated_silk'],
                ['value_key' => 'coated_gloss', 'label' => ['ca' => 'Couché Brillant', 'es' => 'Couché Brillante', 'en' => 'Gloss Coated'],    'price_modifier' => 0,       'is_default' => $default === 'coated_gloss'],
                ['value_key' => 'uncoated',     'label' => ['ca' => 'Offset (mat)',    'es' => 'Offset (mate)',    'en' => 'Uncoated Offset'], 'price_modifier' => -0.0020, 'is_default' => $default === 'uncoated'],
                ['value_key' => 'recycled',     'label' => ['ca' => 'Reciclat',        'es' => 'Reciclado',        'en' => 'Recycled'],        'price_modifier' => 0.0030,  'is_default' => $default === 'recycled',  'production_days_modifier' => 1],
            ],
        ];
    }

    private function optFinishingStandard(): array
    {
        return [
            'key'         => 'finishing',
            'label'       => ['ca' => 'Acabat', 'es' => 'Acabado', 'en' => 'Finishing'],
            'input_type'  => 'select',
            'is_required' => false,
            'values'      => [
                ['value_key' => 'none',           'label' => ['ca' => 'Sense acabat',         'es' => 'Sin acabado',          'en' => 'No finishing'],  'price_modifier' => 0,  'price_modifier_type' => 'flat',    'is_default' => true],
                ['value_key' => 'matte_laminate', 'label' => ['ca' => 'Plastificat mat',      'es' => 'Plastificado mate',    'en' => 'Matte laminate'], 'price_modifier' => 8,  'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                ['value_key' => 'gloss_laminate', 'label' => ['ca' => 'Plastificat brillant', 'es' => 'Plastificado brillo',  'en' => 'Gloss laminate'], 'price_modifier' => 8,  'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                ['value_key' => 'spot_uv',        'label' => ['ca' => 'Vernís selectiu UV',   'es' => 'Barniz selectivo UV',  'en' => 'Spot UV varnish'], 'price_modifier' => 20, 'price_modifier_type' => 'percent', 'production_days_modifier' => 2],
            ],
        ];
    }

    // ================================================================
    // Per-template configurations
    // ================================================================

    private function allConfigs(): array
    {
        $bulk   = $this->tiersBulk();
        $medium = $this->tiersMedium();
        $small  = $this->tiersSmallBatch();
        $low    = $this->tiersLowVolume();
        $app    = $this->tiersApparel();

        return [

            // ═══════════════════════════════════════════════════════
            // FAMILY: Paper-print (flat sheets, 4-colour offset)
            // ═══════════════════════════════════════════════════════

            'fulletons' => [
                'tiers'   => $bulk,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a6', 'label' => ['ca' => 'A6 (105×148 mm)', 'es' => 'A6 (105×148 mm)', 'en' => 'A6 (105×148 mm)'], 'price_modifier' => -0.0020],
                            ['value_key' => 'a5', 'label' => ['ca' => 'A5 (148×210 mm)', 'es' => 'A5 (148×210 mm)', 'en' => 'A5 (148×210 mm)'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'a4', 'label' => ['ca' => 'A4 (210×297 mm)', 'es' => 'A4 (210×297 mm)', 'en' => 'A4 (210×297 mm)'], 'price_modifier' => 0.0040],
                            ['value_key' => 'a3', 'label' => ['ca' => 'A3 (297×420 mm)', 'es' => 'A3 (297×420 mm)', 'en' => 'A3 (297×420 mm)'], 'price_modifier' => 0.0100],
                            ['value_key' => 'dl', 'label' => ['ca' => 'DL (99×210 mm)',  'es' => 'DL (99×210 mm)',  'en' => 'DL (99×210 mm)'],  'price_modifier' => -0.0010],
                        ],
                    ],
                    $this->optPaperWeight([90, 135, 170, 250], '135gr'),
                    $this->optPaperType(),
                    $this->optColorMode(),
                    $this->optFinishingStandard(),
                ],
            ],

            'postals' => [
                'tiers'   => $bulk,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a6',     'label' => ['ca' => 'A6 (105×148 mm)',   'es' => 'A6 (105×148 mm)',   'en' => 'A6 (105×148 mm)'],   'price_modifier' => 0,       'is_default' => true],
                            ['value_key' => '10x15',  'label' => ['ca' => '10×15 cm',          'es' => '10×15 cm',          'en' => '10×15 cm'],          'price_modifier' => 0.0010],
                            ['value_key' => 'dl',     'label' => ['ca' => 'DL (99×210 mm)',    'es' => 'DL (99×210 mm)',    'en' => 'DL (99×210 mm)'],    'price_modifier' => 0.0020],
                            ['value_key' => '15x21',  'label' => ['ca' => '15×21 cm',          'es' => '15×21 cm',          'en' => '15×21 cm'],          'price_modifier' => 0.0040],
                        ],
                    ],
                    $this->optPaperWeight([250, 300, 350], '300gr'),
                    $this->optPaperType('coated_gloss'),
                    $this->optColorMode(),
                    $this->optFinishingStandard(),
                ],
            ],

            'cartes-menu' => [
                'tiers'   => $medium,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Format', 'es' => 'Formato', 'en' => 'Format'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a5',        'label' => ['ca' => 'A5 senzill',       'es' => 'A5 simple',          'en' => 'A5 single sheet'], 'price_modifier' => 0,      'is_default' => true],
                            ['value_key' => 'a4',        'label' => ['ca' => 'A4 senzill',       'es' => 'A4 simple',          'en' => 'A4 single sheet'], 'price_modifier' => 0.0080],
                            ['value_key' => 'a3_folded', 'label' => ['ca' => 'A3 plegat (A4)',   'es' => 'A3 plegado (A4)',    'en' => 'A3 folded to A4'], 'price_modifier' => 0.0200, 'production_days_modifier' => 1],
                            ['value_key' => 'trifold',   'label' => ['ca' => 'Tríptic A4',        'es' => 'Tríptico A4',         'en' => 'A4 tri-fold'],     'price_modifier' => 0.0150, 'production_days_modifier' => 1],
                        ],
                    ],
                    $this->optPaperWeight([170, 250, 350], '250gr'),
                    $this->optPaperType('coated_silk'),
                    [
                        'key'         => 'finishing',
                        'label'       => ['ca' => 'Acabat', 'es' => 'Acabado', 'en' => 'Finishing'],
                        'input_type'  => 'select',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'none',            'label' => ['ca' => 'Sense acabat',    'es' => 'Sin acabado',    'en' => 'No finishing'],      'price_modifier' => 0,  'price_modifier_type' => 'flat',    'is_default' => true],
                            ['value_key' => 'matte_laminate',  'label' => ['ca' => 'Plastificat mat', 'es' => 'Plastificado mate', 'en' => 'Matte laminate'], 'price_modifier' => 10, 'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                            ['value_key' => 'gloss_laminate',  'label' => ['ca' => 'Plastificat brillant', 'es' => 'Plastificado brillo', 'en' => 'Gloss laminate'], 'price_modifier' => 10, 'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                            ['value_key' => 'water_resistant', 'label' => ['ca' => 'Resistent a l\'aigua', 'es' => 'Resistente al agua', 'en' => 'Water-resistant'], 'price_modifier' => 25, 'price_modifier_type' => 'percent', 'production_days_modifier' => 2],
                        ],
                    ],
                ],
            ],

            'felicitacions-aniversari' => [
                'tiers'   => $small,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida (plegada)', 'es' => 'Tamaño (plegado)', 'en' => 'Size (folded)'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => '10x15', 'label' => ['ca' => '10×15 cm', 'es' => '10×15 cm', 'en' => '10×15 cm'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'a6',    'label' => ['ca' => 'A6',        'es' => 'A6',        'en' => 'A6'],        'price_modifier' => 0.0020],
                            ['value_key' => 'a5',    'label' => ['ca' => 'A5',        'es' => 'A5',        'en' => 'A5'],        'price_modifier' => 0.0060],
                        ],
                    ],
                    $this->optPaperWeight([250, 300], '300gr'),
                    $this->optPaperType('coated_silk'),
                    [
                        'key'         => 'envelope_included',
                        'label'       => ['ca' => 'Inclou sobre', 'es' => 'Incluye sobre', 'en' => 'Envelope included'],
                        'input_type'  => 'toggle',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'yes', 'label' => ['ca' => 'Sí', 'es' => 'Sí', 'en' => 'Yes'], 'price_modifier' => 0.10, 'is_default' => true],
                            ['value_key' => 'no',  'label' => ['ca' => 'No', 'es' => 'No', 'en' => 'No'],  'price_modifier' => 0],
                        ],
                    ],
                    $this->optFinishingStandard(),
                ],
            ],

            'certificats-diplomes' => [
                'tiers'   => $small,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a5', 'label' => ['ca' => 'A5', 'es' => 'A5', 'en' => 'A5'], 'price_modifier' => 0],
                            ['value_key' => 'a4', 'label' => ['ca' => 'A4', 'es' => 'A4', 'en' => 'A4'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'a3', 'label' => ['ca' => 'A3', 'es' => 'A3', 'en' => 'A3'], 'price_modifier' => 0.0080],
                        ],
                    ],
                    $this->optPaperWeight([170, 250, 350], '250gr'),
                    [
                        'key'        => 'paper_type',
                        'label'      => ['ca' => 'Tipus de paper', 'es' => 'Tipo de papel', 'en' => 'Paper type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'ivory',     'label' => ['ca' => 'Ivori clàssic',   'es' => 'Marfil clásico',  'en' => 'Classic ivory'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'white',     'label' => ['ca' => 'Blanc satinat',   'es' => 'Blanco satinado', 'en' => 'Satin white'],    'price_modifier' => 0],
                            ['value_key' => 'parchment', 'label' => ['ca' => 'Efecte pergamí',  'es' => 'Efecto pergamino', 'en' => 'Parchment effect'], 'price_modifier' => 0.0080, 'production_days_modifier' => 1],
                            ['value_key' => 'linen',     'label' => ['ca' => 'Textura lli',     'es' => 'Textura lino',    'en' => 'Linen texture'],  'price_modifier' => 0.0120, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'         => 'finishing',
                        'label'       => ['ca' => 'Acabat', 'es' => 'Acabado', 'en' => 'Finishing'],
                        'input_type'  => 'select',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'none',        'label' => ['ca' => 'Sense acabat',    'es' => 'Sin acabado',      'en' => 'No finishing'],  'price_modifier' => 0,  'price_modifier_type' => 'flat',    'is_default' => true],
                            ['value_key' => 'foil_gold',   'label' => ['ca' => 'Estampació or',   'es' => 'Estampación oro',  'en' => 'Gold foil'],     'price_modifier' => 30, 'price_modifier_type' => 'percent', 'production_days_modifier' => 2],
                            ['value_key' => 'foil_silver', 'label' => ['ca' => 'Estampació plata','es' => 'Estampación plata','en' => 'Silver foil'],   'price_modifier' => 30, 'price_modifier_type' => 'percent', 'production_days_modifier' => 2],
                            ['value_key' => 'embossed',    'label' => ['ca' => 'Gofrat en relleu','es' => 'Gofrado en relieve','en' => 'Embossed'],     'price_modifier' => 35, 'price_modifier_type' => 'percent', 'production_days_modifier' => 3],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Stationery
            // ═══════════════════════════════════════════════════════

            'paper-carta' => [
                'tiers'   => $bulk,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a4', 'label' => ['ca' => 'A4 (210×297 mm)', 'es' => 'A4 (210×297 mm)', 'en' => 'A4 (210×297 mm)'], 'price_modifier' => 0, 'is_default' => true],
                        ],
                    ],
                    $this->optPaperWeight([80, 90, 100, 120], '90gr'),
                    [
                        'key'        => 'paper_type',
                        'label'      => ['ca' => 'Tipus de paper', 'es' => 'Tipo de papel', 'en' => 'Paper type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'offset_white', 'label' => ['ca' => 'Offset blanc',  'es' => 'Offset blanco',  'en' => 'White offset'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'recycled',     'label' => ['ca' => 'Reciclat',       'es' => 'Reciclado',      'en' => 'Recycled'],      'price_modifier' => 0.0020, 'production_days_modifier' => 1],
                            ['value_key' => 'laid',         'label' => ['ca' => 'Verjurat',       'es' => 'Verjurado',      'en' => 'Laid texture'],  'price_modifier' => 0.0080, 'production_days_modifier' => 1],
                            ['value_key' => 'cotton',       'label' => ['ca' => 'Cotó premium',   'es' => 'Algodón premium', 'en' => 'Cotton premium'], 'price_modifier' => 0.0200, 'production_days_modifier' => 2],
                        ],
                    ],
                    $this->optColorMode(['4_0', '1_0', '1_1'], '4_0'),
                ],
            ],

            'sobres-personalitzats' => [
                'tiers'   => $bulk,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Format', 'es' => 'Formato', 'en' => 'Format'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'dl',       'label' => ['ca' => 'DL (110×220 mm)',  'es' => 'DL (110×220 mm)',  'en' => 'DL (110×220 mm)'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'c6',       'label' => ['ca' => 'C6 (114×162 mm)',  'es' => 'C6 (114×162 mm)',  'en' => 'C6 (114×162 mm)'],  'price_modifier' => 0],
                            ['value_key' => 'c5',       'label' => ['ca' => 'C5 (162×229 mm)',  'es' => 'C5 (162×229 mm)',  'en' => 'C5 (162×229 mm)'],  'price_modifier' => 0.0080],
                            ['value_key' => 'c4',       'label' => ['ca' => 'C4 (229×324 mm)',  'es' => 'C4 (229×324 mm)',  'en' => 'C4 (229×324 mm)'],  'price_modifier' => 0.0150],
                            ['value_key' => 'american', 'label' => ['ca' => 'Americà (110×225 mm)', 'es' => 'Americano (110×225 mm)', 'en' => 'American (110×225 mm)'], 'price_modifier' => 0.0020],
                        ],
                    ],
                    $this->optPaperWeight([80, 90, 100, 120], '90gr'),
                    [
                        'key'         => 'window',
                        'label'       => ['ca' => 'Amb finestra', 'es' => 'Con ventana', 'en' => 'With window'],
                        'input_type'  => 'toggle',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'no',    'label' => ['ca' => 'Sense finestra', 'es' => 'Sin ventana',   'en' => 'No window'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'right', 'label' => ['ca' => 'Finestra dreta', 'es' => 'Ventana derecha', 'en' => 'Right window'], 'price_modifier' => 0.0030, 'production_days_modifier' => 1],
                            ['value_key' => 'left',  'label' => ['ca' => 'Finestra esquerra', 'es' => 'Ventana izquierda', 'en' => 'Left window'],  'price_modifier' => 0.0030, 'production_days_modifier' => 1],
                        ],
                    ],
                    $this->optColorMode(['4_0', '1_0'], '4_0'),
                ],
            ],

            'carpetes-presentacio' => [
                'tiers'   => $medium,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a4', 'label' => ['ca' => 'A4 (per fulls A4)', 'es' => 'A4 (para hojas A4)', 'en' => 'A4 (holds A4 sheets)'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'a5', 'label' => ['ca' => 'A5 (per fulls A5)', 'es' => 'A5 (para hojas A5)', 'en' => 'A5 (holds A5 sheets)'], 'price_modifier' => -0.30],
                        ],
                    ],
                    [
                        'key'        => 'material',
                        'label'      => ['ca' => 'Material', 'es' => 'Material', 'en' => 'Material'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'card_300', 'label' => ['ca' => 'Cartolina 300 gr',  'es' => 'Cartulina 300 gr',  'en' => '300 gsm cardstock'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'card_350', 'label' => ['ca' => 'Cartolina 350 gr',  'es' => 'Cartulina 350 gr',  'en' => '350 gsm cardstock'], 'price_modifier' => 0.15],
                            ['value_key' => 'card_400', 'label' => ['ca' => 'Cartolina 400 gr',  'es' => 'Cartulina 400 gr',  'en' => '400 gsm cardstock'], 'price_modifier' => 0.30],
                        ],
                    ],
                    [
                        'key'         => 'pockets',
                        'label'       => ['ca' => 'Butxaques', 'es' => 'Bolsillos', 'en' => 'Pockets'],
                        'input_type'  => 'radio',
                        'values'      => [
                            ['value_key' => '1', 'label' => ['ca' => '1 butxaca',   'es' => '1 bolsillo',   'en' => '1 pocket'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '2', 'label' => ['ca' => '2 butxaques', 'es' => '2 bolsillos', 'en' => '2 pockets'], 'price_modifier' => 0.20, 'production_days_modifier' => 1],
                        ],
                    ],
                    $this->optFinishingStandard(),
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Bound print
            // ═══════════════════════════════════════════════════════

            'catalegs-revistes' => [
                'tiers'   => $low,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Format', 'es' => 'Formato', 'en' => 'Format'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a5',         'label' => ['ca' => 'A5',             'es' => 'A5',             'en' => 'A5'],              'price_modifier' => 0],
                            ['value_key' => 'a4',         'label' => ['ca' => 'A4',             'es' => 'A4',             'en' => 'A4'],              'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'square_21',  'label' => ['ca' => 'Quadrat 21×21',  'es' => 'Cuadrado 21×21',  'en' => 'Square 21×21 cm'], 'price_modifier' => 1.50, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'page_count',
                        'label'      => ['ca' => 'Pàgines', 'es' => 'Páginas', 'en' => 'Page count'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => '8',  'label' => ['ca' => '8 pàgines',  'es' => '8 páginas',  'en' => '8 pages'],  'price_modifier' => -4.00],
                            ['value_key' => '16', 'label' => ['ca' => '16 pàgines', 'es' => '16 páginas', 'en' => '16 pages'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '24', 'label' => ['ca' => '24 pàgines', 'es' => '24 páginas', 'en' => '24 pages'], 'price_modifier' => 3.00],
                            ['value_key' => '32', 'label' => ['ca' => '32 pàgines', 'es' => '32 páginas', 'en' => '32 pages'], 'price_modifier' => 6.00, 'production_days_modifier' => 1],
                            ['value_key' => '48', 'label' => ['ca' => '48 pàgines', 'es' => '48 páginas', 'en' => '48 pages'], 'price_modifier' => 12.00, 'production_days_modifier' => 2],
                            ['value_key' => '64', 'label' => ['ca' => '64 pàgines', 'es' => '64 páginas', 'en' => '64 pages'], 'price_modifier' => 18.00, 'production_days_modifier' => 3],
                        ],
                    ],
                    [
                        'key'        => 'binding',
                        'label'      => ['ca' => 'Enquadernació', 'es' => 'Encuadernación', 'en' => 'Binding'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'saddle_stitch', 'label' => ['ca' => 'Grapat al llom', 'es' => 'Grapado al lomo', 'en' => 'Saddle-stitched'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'perfect_bound', 'label' => ['ca' => 'Rústica cosida',  'es' => 'Rústica cosida',   'en' => 'Perfect-bound'],   'price_modifier' => 3.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'cover_type',
                        'label'      => ['ca' => 'Coberta', 'es' => 'Portada', 'en' => 'Cover'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'soft_135', 'label' => ['ca' => 'Tova 135 gr',         'es' => 'Blanda 135 gr',         'en' => 'Soft 135 gsm'],        'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'soft_250', 'label' => ['ca' => 'Tova 250 gr + plast.', 'es' => 'Blanda 250 gr + plast.', 'en' => 'Soft 250 gsm + lam.'], 'price_modifier' => 1.80, 'production_days_modifier' => 1],
                        ],
                    ],
                ],
                'rules' => [
                    [
                        'rule_type'            => 'incompatible',
                        'condition_option_key' => 'page_count',
                        'condition_value_key'  => '8',
                        'target_option_key'    => 'binding',
                        'target_value_key'     => 'perfect_bound',
                        'message' => [
                            'ca' => 'Rústica cosida requereix com a mínim 32 pàgines.',
                            'es' => 'Rústica cosida requiere al menos 32 páginas.',
                            'en' => 'Perfect-bound requires at least 32 pages.',
                        ],
                    ],
                    [
                        'rule_type'            => 'incompatible',
                        'condition_option_key' => 'page_count',
                        'condition_value_key'  => '16',
                        'target_option_key'    => 'binding',
                        'target_value_key'     => 'perfect_bound',
                        'message' => [
                            'ca' => 'Rústica cosida requereix com a mínim 32 pàgines.',
                            'es' => 'Rústica cosida requiere al menos 32 páginas.',
                            'en' => 'Perfect-bound requires at least 32 pages.',
                        ],
                    ],
                ],
            ],

            'llibretes-agendes' => [
                'tiers'   => $small,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a6', 'label' => ['ca' => 'A6 butxaca', 'es' => 'A6 bolsillo', 'en' => 'A6 pocket'], 'price_modifier' => -1.50],
                            ['value_key' => 'a5', 'label' => ['ca' => 'A5',         'es' => 'A5',         'en' => 'A5'],         'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'a4', 'label' => ['ca' => 'A4',         'es' => 'A4',         'en' => 'A4'],         'price_modifier' => 2.00],
                        ],
                    ],
                    [
                        'key'        => 'page_count',
                        'label'      => ['ca' => 'Pàgines', 'es' => 'Páginas', 'en' => 'Page count'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => '48',  'label' => ['ca' => '48 pàgines',  'es' => '48 páginas',  'en' => '48 pages'],  'price_modifier' => -1.50],
                            ['value_key' => '80',  'label' => ['ca' => '80 pàgines',  'es' => '80 páginas',  'en' => '80 pages'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '120', 'label' => ['ca' => '120 pàgines', 'es' => '120 páginas', 'en' => '120 pages'], 'price_modifier' => 2.00, 'production_days_modifier' => 1],
                            ['value_key' => '200', 'label' => ['ca' => '200 pàgines', 'es' => '200 páginas', 'en' => '200 pages'], 'price_modifier' => 4.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'cover_type',
                        'label'      => ['ca' => 'Coberta', 'es' => 'Portada', 'en' => 'Cover'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'soft',       'label' => ['ca' => 'Tapa tova',          'es' => 'Tapa blanda',       'en' => 'Soft cover'],         'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'hard',       'label' => ['ca' => 'Tapa dura',          'es' => 'Tapa dura',         'en' => 'Hard cover'],         'price_modifier' => 3.00, 'production_days_modifier' => 2],
                            ['value_key' => 'leatherette','label' => ['ca' => 'Polipell premium',   'es' => 'Polipiel premium',   'en' => 'Premium leatherette'], 'price_modifier' => 5.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'binding',
                        'label'      => ['ca' => 'Enquadernació', 'es' => 'Encuadernación', 'en' => 'Binding'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'spiral',        'label' => ['ca' => 'Espiral metàl·lica', 'es' => 'Espiral metálica', 'en' => 'Wire-O spiral'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'perfect_bound', 'label' => ['ca' => 'Rústica',             'es' => 'Rústica',           'en' => 'Perfect bound'],  'price_modifier' => 1.50, 'production_days_modifier' => 1],
                            ['value_key' => 'stitched',      'label' => ['ca' => 'Cosida',              'es' => 'Cosida',            'en' => 'Thread-sewn'],    'price_modifier' => 2.50, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'interior',
                        'label'      => ['ca' => 'Interior', 'es' => 'Interior', 'en' => 'Interior'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'blank',  'label' => ['ca' => 'Blanc',    'es' => 'Blanco',     'en' => 'Blank'],        'price_modifier' => 0],
                            ['value_key' => 'lined',  'label' => ['ca' => 'Ratllat',  'es' => 'Rayado',     'en' => 'Lined'],        'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'dotted', 'label' => ['ca' => 'Puntejat', 'es' => 'Punteado',   'en' => 'Dotted'],       'price_modifier' => 0],
                            ['value_key' => 'grid',   'label' => ['ca' => 'Quadrícula','es' => 'Cuadrícula', 'en' => 'Grid'],         'price_modifier' => 0],
                        ],
                    ],
                ],
            ],

            'calendaris' => [
                'tiers'   => $medium,
                'options' => [
                    [
                        'key'        => 'type',
                        'label'      => ['ca' => 'Tipus', 'es' => 'Tipo', 'en' => 'Type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'wall_a3',  'label' => ['ca' => 'Paret A3',  'es' => 'Pared A3',  'en' => 'Wall A3'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'wall_a4',  'label' => ['ca' => 'Paret A4',  'es' => 'Pared A4',  'en' => 'Wall A4'],  'price_modifier' => -1.00],
                            ['value_key' => 'desk',     'label' => ['ca' => 'Sobretaula','es' => 'Sobremesa', 'en' => 'Desk'],     'price_modifier' => 1.50],
                            ['value_key' => 'pocket',   'label' => ['ca' => 'Butxaca',   'es' => 'Bolsillo',  'en' => 'Pocket'],   'price_modifier' => -2.50],
                        ],
                    ],
                    [
                        'key'        => 'pages',
                        'label'      => ['ca' => 'Pàgines', 'es' => 'Páginas', 'en' => 'Sheets'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => '7',  'label' => ['ca' => '7 làmines',  'es' => '7 láminas',  'en' => '7 sheets'],  'price_modifier' => 0],
                            ['value_key' => '13', 'label' => ['ca' => '13 làmines', 'es' => '13 láminas', 'en' => '13 sheets'], 'price_modifier' => 1.50, 'is_default' => true],
                        ],
                    ],
                    $this->optPaperWeight([170, 200, 250], '200gr'),
                    [
                        'key'        => 'binding',
                        'label'      => ['ca' => 'Enquadernació', 'es' => 'Encuadernación', 'en' => 'Binding'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'spiral',  'label' => ['ca' => 'Espiral',    'es' => 'Espiral',    'en' => 'Spiral'],   'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'stapled', 'label' => ['ca' => 'Grapat',     'es' => 'Grapado',    'en' => 'Stapled'],  'price_modifier' => -0.40],
                            ['value_key' => 'hanger',  'label' => ['ca' => 'Amb penjador','es' => 'Con colgador','en' => 'With hanger'], 'price_modifier' => 0.30],
                        ],
                    ],
                ],
            ],

            'talonaris-factures' => [
                'tiers'   => $small,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a6', 'label' => ['ca' => 'A6', 'es' => 'A6', 'en' => 'A6'], 'price_modifier' => -2.00],
                            ['value_key' => 'a5', 'label' => ['ca' => 'A5', 'es' => 'A5', 'en' => 'A5'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'a4', 'label' => ['ca' => 'A4', 'es' => 'A4', 'en' => 'A4'], 'price_modifier' => 2.50],
                        ],
                    ],
                    [
                        'key'        => 'copies',
                        'label'      => ['ca' => 'Còpies', 'es' => 'Copias', 'en' => 'Copies'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'duplicate',  'label' => ['ca' => 'Duplicat (original + còpia)',       'es' => 'Duplicado (original + copia)',        'en' => 'Duplicate (original + copy)'],           'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'triplicate', 'label' => ['ca' => 'Triplicat (original + 2 còpies)',   'es' => 'Triplicado (original + 2 copias)',   'en' => 'Triplicate (original + 2 copies)'],      'price_modifier' => 2.00, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'sheets_per_book',
                        'label'      => ['ca' => 'Fulls per talonari', 'es' => 'Hojas por talonario', 'en' => 'Sheets per book'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => '50',  'label' => ['ca' => '50 jocs',  'es' => '50 juegos',  'en' => '50 sets'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '100', 'label' => ['ca' => '100 jocs', 'es' => '100 juegos', 'en' => '100 sets'], 'price_modifier' => 2.00],
                        ],
                    ],
                    [
                        'key'         => 'numbering',
                        'label'       => ['ca' => 'Numeració', 'es' => 'Numeración', 'en' => 'Numbering'],
                        'input_type'  => 'toggle',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'sequential', 'label' => ['ca' => 'Numeració seqüencial', 'es' => 'Numeración secuencial', 'en' => 'Sequential numbering'], 'price_modifier' => 1.50, 'is_default' => true],
                            ['value_key' => 'none',       'label' => ['ca' => 'Sense numeració',       'es' => 'Sin numeración',       'en' => 'No numbering'],         'price_modifier' => 0],
                        ],
                    ],
                ],
            ],

            'fotografies-albums' => [
                'tiers'   => $low,
                'options' => [
                    [
                        'key'        => 'product_type',
                        'label'      => ['ca' => 'Producte', 'es' => 'Producto', 'en' => 'Product'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'prints_10x15', 'label' => ['ca' => 'Còpies 10×15 cm',    'es' => 'Copias 10×15 cm',    'en' => '10×15 cm prints'],    'price_modifier' => -2.00],
                            ['value_key' => 'prints_13x18', 'label' => ['ca' => 'Còpies 13×18 cm',    'es' => 'Copias 13×18 cm',    'en' => '13×18 cm prints'],    'price_modifier' => -1.00, 'is_default' => true],
                            ['value_key' => 'prints_20x30', 'label' => ['ca' => 'Còpies 20×30 cm',    'es' => 'Copias 20×30 cm',    'en' => '20×30 cm prints'],    'price_modifier' => 1.50],
                            ['value_key' => 'album_a4',     'label' => ['ca' => 'Àlbum A4',           'es' => 'Álbum A4',           'en' => 'A4 photo album'],     'price_modifier' => 15.00, 'production_days_modifier' => 3],
                            ['value_key' => 'album_30x30',  'label' => ['ca' => 'Àlbum 30×30 cm',     'es' => 'Álbum 30×30 cm',     'en' => '30×30 cm photo album'], 'price_modifier' => 22.00, 'production_days_modifier' => 4],
                        ],
                    ],
                    [
                        'key'        => 'paper_type',
                        'label'      => ['ca' => 'Paper fotogràfic', 'es' => 'Papel fotográfico', 'en' => 'Photo paper'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'glossy', 'label' => ['ca' => 'Brillant',       'es' => 'Brillante',       'en' => 'Glossy'],       'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'matte',  'label' => ['ca' => 'Mat',             'es' => 'Mate',             'en' => 'Matte'],        'price_modifier' => 0],
                            ['value_key' => 'pearl',  'label' => ['ca' => 'Premium perlat',  'es' => 'Premium perlado',  'en' => 'Premium pearl'], 'price_modifier' => 1.50, 'production_days_modifier' => 1],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Large-format
            // ═══════════════════════════════════════════════════════

            'posters' => [
                'tiers'   => $medium,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'a3',      'label' => ['ca' => 'A3 (29,7×42 cm)',   'es' => 'A3 (29,7×42 cm)',   'en' => 'A3 (29.7×42 cm)'],   'price_modifier' => -2.50],
                            ['value_key' => 'a2',      'label' => ['ca' => 'A2 (42×59,4 cm)',   'es' => 'A2 (42×59,4 cm)',   'en' => 'A2 (42×59.4 cm)'],   'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'a1',      'label' => ['ca' => 'A1 (59,4×84 cm)',   'es' => 'A1 (59,4×84 cm)',   'en' => 'A1 (59.4×84 cm)'],   'price_modifier' => 4.50, 'production_days_modifier' => 1],
                            ['value_key' => 'a0',      'label' => ['ca' => 'A0 (84×119 cm)',    'es' => 'A0 (84×119 cm)',    'en' => 'A0 (84×119 cm)'],    'price_modifier' => 12.00, 'production_days_modifier' => 2],
                            ['value_key' => '70x100',  'label' => ['ca' => '70×100 cm',          'es' => '70×100 cm',          'en' => '70×100 cm'],          'price_modifier' => 8.00, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'material',
                        'label'      => ['ca' => 'Material', 'es' => 'Material', 'en' => 'Material'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'paper_135',  'label' => ['ca' => 'Paper 135 gr',       'es' => 'Papel 135 gr',       'en' => '135 gsm paper'],        'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'paper_200',  'label' => ['ca' => 'Paper 200 gr',       'es' => 'Papel 200 gr',       'en' => '200 gsm paper'],        'price_modifier' => 1.20],
                            ['value_key' => 'photo_250',  'label' => ['ca' => 'Fotogràfic 250 gr',  'es' => 'Fotográfico 250 gr',  'en' => '250 gsm photo paper'],  'price_modifier' => 3.50, 'production_days_modifier' => 1],
                            ['value_key' => 'canvas',     'label' => ['ca' => 'Tela canvas',          'es' => 'Tela canvas',          'en' => 'Canvas'],              'price_modifier' => 8.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'         => 'finishing',
                        'label'       => ['ca' => 'Acabat', 'es' => 'Acabado', 'en' => 'Finishing'],
                        'input_type'  => 'select',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'none',           'label' => ['ca' => 'Sense acabat',       'es' => 'Sin acabado',       'en' => 'No finishing'], 'price_modifier' => 0, 'price_modifier_type' => 'flat', 'is_default' => true],
                            ['value_key' => 'matte_laminate', 'label' => ['ca' => 'Plastificat mat',    'es' => 'Plastificado mate',  'en' => 'Matte laminate'], 'price_modifier' => 15, 'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                            ['value_key' => 'gloss_laminate', 'label' => ['ca' => 'Plastificat brillant','es' => 'Plastificado brillo','en' => 'Gloss laminate'], 'price_modifier' => 15, 'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                        ],
                    ],
                ],
            ],

            'lones-banners' => [
                'tiers'   => $low,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => '1x05',  'label' => ['ca' => '1 × 0,5 m',   'es' => '1 × 0,5 m',   'en' => '1 × 0.5 m'],   'price_modifier' => -10.00],
                            ['value_key' => '2x1',   'label' => ['ca' => '2 × 1 m',     'es' => '2 × 1 m',     'en' => '2 × 1 m'],     'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '3x15',  'label' => ['ca' => '3 × 1,5 m',   'es' => '3 × 1,5 m',   'en' => '3 × 1.5 m'],   'price_modifier' => 18.00, 'production_days_modifier' => 1],
                            ['value_key' => '5x2',   'label' => ['ca' => '5 × 2 m',     'es' => '5 × 2 m',     'en' => '5 × 2 m'],     'price_modifier' => 40.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'material',
                        'label'      => ['ca' => 'Material', 'es' => 'Material', 'en' => 'Material'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'vinyl_440', 'label' => ['ca' => 'Lona PVC 440 gr', 'es' => 'Lona PVC 440 gr', 'en' => 'PVC vinyl 440 gsm'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'vinyl_510', 'label' => ['ca' => 'Lona PVC 510 gr', 'es' => 'Lona PVC 510 gr', 'en' => 'PVC vinyl 510 gsm'], 'price_modifier' => 4.00],
                            ['value_key' => 'mesh',      'label' => ['ca' => 'Lona microperforada','es' => 'Lona microperforada', 'en' => 'Mesh banner'], 'price_modifier' => 6.00, 'production_days_modifier' => 1],
                            ['value_key' => 'fabric',    'label' => ['ca' => 'Tèxtil sublimació','es' => 'Textil sublimación','en' => 'Fabric sublimation'], 'price_modifier' => 10.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'eyelets',
                        'label'      => ['ca' => 'Ullets', 'es' => 'Ojales', 'en' => 'Eyelets'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'corners',    'label' => ['ca' => 'Només a les cantonades', 'es' => 'Sólo en esquinas',    'en' => 'Corners only'],    'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'every_50cm', 'label' => ['ca' => 'Cada 50 cm',              'es' => 'Cada 50 cm',             'en' => 'Every 50 cm'],     'price_modifier' => 4.00],
                            ['value_key' => 'none',       'label' => ['ca' => 'Sense ullets',             'es' => 'Sin ojales',             'en' => 'No eyelets'],      'price_modifier' => -2.00],
                        ],
                    ],
                    [
                        'key'         => 'hemming',
                        'label'       => ['ca' => 'Ribet perimetral', 'es' => 'Dobladillo', 'en' => 'Hemmed edge'],
                        'input_type'  => 'toggle',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'yes', 'label' => ['ca' => 'Amb ribet', 'es' => 'Con dobladillo', 'en' => 'Hemmed'],       'price_modifier' => 3.00, 'is_default' => true],
                            ['value_key' => 'no',  'label' => ['ca' => 'Sense ribet','es' => 'Sin dobladillo', 'en' => 'No hemming'],   'price_modifier' => 0],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Stickers
            // ═══════════════════════════════════════════════════════

            'adhesius-etiquetes' => [
                'tiers'   => $bulk,
                'options' => [
                    [
                        'key'        => 'material',
                        'label'      => ['ca' => 'Material', 'es' => 'Material', 'en' => 'Material'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'vinyl_gloss', 'label' => ['ca' => 'Vinil brillant',    'es' => 'Vinilo brillante',   'en' => 'Gloss vinyl'],       'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'vinyl_matte', 'label' => ['ca' => 'Vinil mat',          'es' => 'Vinilo mate',         'en' => 'Matte vinyl'],       'price_modifier' => 0],
                            ['value_key' => 'transparent', 'label' => ['ca' => 'Vinil transparent',  'es' => 'Vinilo transparente', 'en' => 'Transparent vinyl'], 'price_modifier' => 0.0040],
                            ['value_key' => 'paper',       'label' => ['ca' => 'Paper adhesiu',      'es' => 'Papel adhesivo',      'en' => 'Paper sticker'],     'price_modifier' => -0.0030],
                            ['value_key' => 'removable',   'label' => ['ca' => 'Adhesiu removible',  'es' => 'Adhesivo removible',  'en' => 'Removable'],         'price_modifier' => 0.0050],
                        ],
                    ],
                    [
                        'key'        => 'shape',
                        'label'      => ['ca' => 'Forma', 'es' => 'Forma', 'en' => 'Shape'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'rectangle', 'label' => ['ca' => 'Rectangle', 'es' => 'Rectángulo', 'en' => 'Rectangle'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'square',    'label' => ['ca' => 'Quadrat',   'es' => 'Cuadrado',    'en' => 'Square'],    'price_modifier' => 0],
                            ['value_key' => 'circle',    'label' => ['ca' => 'Cercle',    'es' => 'Círculo',     'en' => 'Circle'],    'price_modifier' => 0.0020],
                            ['value_key' => 'oval',      'label' => ['ca' => 'Oval',      'es' => 'Oval',        'en' => 'Oval'],      'price_modifier' => 0.0020],
                            ['value_key' => 'die_cut',   'label' => ['ca' => 'Tall a mida','es' => 'Troquelado a medida','en' => 'Custom die-cut'], 'price_modifier' => 0.0080, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida (aprox.)', 'es' => 'Tamaño (aprox.)', 'en' => 'Size (approx.)'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'xs',      'label' => ['ca' => 'Fins a 3×3 cm',  'es' => 'Hasta 3×3 cm',  'en' => 'Up to 3×3 cm'],  'price_modifier' => -0.0040],
                            ['value_key' => 's',       'label' => ['ca' => 'Fins a 5×5 cm',  'es' => 'Hasta 5×5 cm',  'en' => 'Up to 5×5 cm'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'm',       'label' => ['ca' => 'Fins a 10×10 cm','es' => 'Hasta 10×10 cm','en' => 'Up to 10×10 cm'],'price_modifier' => 0.0040],
                            ['value_key' => 'l',       'label' => ['ca' => 'Fins a 15×15 cm','es' => 'Hasta 15×15 cm','en' => 'Up to 15×15 cm'],'price_modifier' => 0.0100],
                            ['value_key' => 'custom',  'label' => ['ca' => 'Mida personalitzada', 'es' => 'Tamaño personalizado', 'en' => 'Custom size'],   'price_modifier' => 0.0200, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'         => 'lamination',
                        'label'       => ['ca' => 'Laminat protector', 'es' => 'Laminado protector', 'en' => 'Protective lamination'],
                        'input_type'  => 'select',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'none',       'label' => ['ca' => 'Cap',          'es' => 'Ninguno',        'en' => 'None'],         'price_modifier' => 0, 'price_modifier_type' => 'flat', 'is_default' => true],
                            ['value_key' => 'gloss',      'label' => ['ca' => 'Brillant',      'es' => 'Brillante',       'en' => 'Gloss'],        'price_modifier' => 10, 'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                            ['value_key' => 'matte',      'label' => ['ca' => 'Mat',            'es' => 'Mate',             'en' => 'Matte'],        'price_modifier' => 10, 'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                            ['value_key' => 'waterproof', 'label' => ['ca' => 'Resistent a l\'aigua', 'es' => 'Resistente al agua', 'en' => 'Waterproof'], 'price_modifier' => 20, 'price_modifier_type' => 'percent', 'production_days_modifier' => 1],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Premium cards (invitations, tickets)
            // ═══════════════════════════════════════════════════════

            'invitacions-casament' => [
                'tiers'   => $small,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => '10x15',   'label' => ['ca' => '10×15 cm',        'es' => '10×15 cm',        'en' => '10×15 cm'],          'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'a5',      'label' => ['ca' => 'A5 (148×210 mm)', 'es' => 'A5 (148×210 mm)', 'en' => 'A5 (148×210 mm)'],   'price_modifier' => 0.30],
                            ['value_key' => 'square',  'label' => ['ca' => '15×15 cm',         'es' => '15×15 cm',         'en' => '15×15 cm square'],    'price_modifier' => 0.20],
                            ['value_key' => 'dl',      'label' => ['ca' => 'DL (99×210 mm)',   'es' => 'DL (99×210 mm)',   'en' => 'DL (99×210 mm)'],     'price_modifier' => 0.10],
                        ],
                    ],
                    [
                        'key'        => 'paper_type',
                        'label'      => ['ca' => 'Tipus de paper', 'es' => 'Tipo de papel', 'en' => 'Paper type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'premium_smooth',  'label' => ['ca' => 'Premium llis',       'es' => 'Premium liso',        'en' => 'Premium smooth'],        'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'textured',        'label' => ['ca' => 'Texturat',            'es' => 'Texturizado',         'en' => 'Textured'],              'price_modifier' => 0.20],
                            ['value_key' => 'kraft',           'label' => ['ca' => 'Kraft natural',       'es' => 'Kraft natural',        'en' => 'Natural kraft'],          'price_modifier' => 0.10],
                            ['value_key' => 'cotton',          'label' => ['ca' => 'Cotó premium',        'es' => 'Algodón premium',      'en' => 'Cotton premium'],         'price_modifier' => 0.50, 'production_days_modifier' => 1],
                            ['value_key' => 'pearlescent',     'label' => ['ca' => 'Metal·litzat perla',  'es' => 'Metalizado perla',     'en' => 'Pearlescent metallic'],   'price_modifier' => 0.40, 'production_days_modifier' => 1],
                        ],
                    ],
                    $this->optPaperWeight([250, 300, 350], '300gr'),
                    [
                        'key'         => 'finishing',
                        'label'       => ['ca' => 'Acabat especial', 'es' => 'Acabado especial', 'en' => 'Special finish'],
                        'input_type'  => 'select',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'none',         'label' => ['ca' => 'Sense acabat',     'es' => 'Sin acabado',       'en' => 'No special finish'], 'price_modifier' => 0,  'price_modifier_type' => 'flat',    'is_default' => true],
                            ['value_key' => 'foil_gold',    'label' => ['ca' => 'Estampació or',     'es' => 'Estampación oro',   'en' => 'Gold foil'],         'price_modifier' => 40, 'price_modifier_type' => 'percent', 'production_days_modifier' => 2],
                            ['value_key' => 'foil_silver',  'label' => ['ca' => 'Estampació plata',  'es' => 'Estampación plata', 'en' => 'Silver foil'],       'price_modifier' => 40, 'price_modifier_type' => 'percent', 'production_days_modifier' => 2],
                            ['value_key' => 'foil_rose',    'label' => ['ca' => 'Estampació or rosa', 'es' => 'Estampación oro rosa', 'en' => 'Rose gold foil'],'price_modifier' => 45, 'price_modifier_type' => 'percent', 'production_days_modifier' => 3],
                            ['value_key' => 'embossed',     'label' => ['ca' => 'Gofrat en relleu',  'es' => 'Gofrado en relieve','en' => 'Embossed'],          'price_modifier' => 35, 'price_modifier_type' => 'percent', 'production_days_modifier' => 3],
                        ],
                    ],
                    [
                        'key'         => 'envelope_included',
                        'label'       => ['ca' => 'Sobre a joc', 'es' => 'Sobre a juego', 'en' => 'Matching envelope'],
                        'input_type'  => 'toggle',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'yes', 'label' => ['ca' => 'Sí', 'es' => 'Sí', 'en' => 'Yes'], 'price_modifier' => 0.15, 'is_default' => true],
                            ['value_key' => 'no',  'label' => ['ca' => 'No', 'es' => 'No', 'en' => 'No'],  'price_modifier' => 0],
                        ],
                    ],
                ],
                'rules' => [
                    [
                        'rule_type'            => 'warning',
                        'condition_option_key' => 'paper_type',
                        'condition_value_key'  => 'cotton',
                        'target_option_key'    => 'finishing',
                        'target_value_key'     => 'foil_gold',
                        'message' => [
                            'ca' => 'Foil sobre paper de cotó pot presentar imperfeccions. Recomanem mostra prèvia.',
                            'es' => 'Foil sobre papel de algodón puede presentar imperfecciones. Recomendamos muestra previa.',
                            'en' => 'Foil on cotton paper may show imperfections. A sample proof is recommended.',
                        ],
                    ],
                ],
            ],

            'entrades-esdeveniments' => [
                'tiers'   => $bulk,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'standard', 'label' => ['ca' => '5,5×21 cm (estàndard)', 'es' => '5,5×21 cm (estándar)', 'en' => '5.5×21 cm (standard)'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'festival', 'label' => ['ca' => '7×15 cm festival',       'es' => '7×15 cm festival',      'en' => '7×15 cm festival'],     'price_modifier' => 0.0020],
                            ['value_key' => 'wristband','label' => ['ca' => 'Polsera 2,5×25 cm',      'es' => 'Pulsera 2,5×25 cm',     'en' => 'Wristband 2.5×25 cm'],  'price_modifier' => 0.0080, 'production_days_modifier' => 2],
                        ],
                    ],
                    $this->optPaperWeight([170, 250, 350], '250gr'),
                    [
                        'key'         => 'numbering',
                        'label'       => ['ca' => 'Numeració', 'es' => 'Numeración', 'en' => 'Numbering'],
                        'input_type'  => 'toggle',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'sequential', 'label' => ['ca' => 'Numeració seqüencial', 'es' => 'Numeración secuencial', 'en' => 'Sequential numbering'], 'price_modifier' => 0.0020, 'is_default' => true],
                            ['value_key' => 'none',       'label' => ['ca' => 'Sense numeració',      'es' => 'Sin numeración',      'en' => 'No numbering'],          'price_modifier' => 0],
                        ],
                    ],
                    [
                        'key'         => 'perforation',
                        'label'       => ['ca' => 'Perforació', 'es' => 'Perforación', 'en' => 'Perforation'],
                        'input_type'  => 'toggle',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'yes', 'label' => ['ca' => 'Amb perforació (matriu)', 'es' => 'Con perforación (matriz)', 'en' => 'With tear-off stub'], 'price_modifier' => 0.0020, 'is_default' => true],
                            ['value_key' => 'no',  'label' => ['ca' => 'Sense perforació',         'es' => 'Sin perforación',         'en' => 'No perforation'],    'price_modifier' => 0],
                        ],
                    ],
                    [
                        'key'         => 'security',
                        'label'       => ['ca' => 'Seguretat', 'es' => 'Seguridad', 'en' => 'Security features'],
                        'input_type'  => 'select',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'none',      'label' => ['ca' => 'Cap',             'es' => 'Ninguna',       'en' => 'None'],               'price_modifier' => 0, 'price_modifier_type' => 'flat', 'is_default' => true],
                            ['value_key' => 'watermark', 'label' => ['ca' => 'Filigrana',        'es' => 'Filigrana',       'en' => 'Watermark'],          'price_modifier' => 15, 'price_modifier_type' => 'percent', 'production_days_modifier' => 2],
                            ['value_key' => 'foil',      'label' => ['ca' => 'Franja foil',      'es' => 'Franja foil',     'en' => 'Foil strip'],         'price_modifier' => 25, 'price_modifier_type' => 'percent', 'production_days_modifier' => 2],
                            ['value_key' => 'hologram',  'label' => ['ca' => 'Hologrameta',      'es' => 'Holograma',       'en' => 'Hologram sticker'],   'price_modifier' => 40, 'price_modifier_type' => 'percent', 'production_days_modifier' => 3],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Apparel
            // ═══════════════════════════════════════════════════════

            'samarretes-dessuadores' => [
                'tiers'   => $app,
                'options' => [
                    [
                        'key'        => 'garment',
                        'label'      => ['ca' => 'Peça', 'es' => 'Prenda', 'en' => 'Garment'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'tshirt_short', 'label' => ['ca' => 'Samarreta màniga curta', 'es' => 'Camiseta manga corta', 'en' => 'T-shirt (short sleeve)'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'tshirt_long',  'label' => ['ca' => 'Samarreta màniga llarga','es' => 'Camiseta manga larga',  'en' => 'Long-sleeve T-shirt'],    'price_modifier' => 3.00],
                            ['value_key' => 'polo',         'label' => ['ca' => 'Polo',                    'es' => 'Polo',                   'en' => 'Polo'],                   'price_modifier' => 5.00],
                            ['value_key' => 'sweatshirt',   'label' => ['ca' => 'Dessuadora',              'es' => 'Sudadera',              'en' => 'Sweatshirt'],             'price_modifier' => 9.00],
                            ['value_key' => 'hoodie',       'label' => ['ca' => 'Dessuadora amb caputxa',  'es' => 'Sudadera con capucha',   'en' => 'Hoodie'],                 'price_modifier' => 12.00],
                        ],
                    ],
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Talla', 'es' => 'Talla', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'xs',  'label' => ['ca' => 'XS',  'es' => 'XS',  'en' => 'XS'],  'price_modifier' => 0],
                            ['value_key' => 's',   'label' => ['ca' => 'S',   'es' => 'S',   'en' => 'S'],   'price_modifier' => 0],
                            ['value_key' => 'm',   'label' => ['ca' => 'M',   'es' => 'M',   'en' => 'M'],   'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'l',   'label' => ['ca' => 'L',   'es' => 'L',   'en' => 'L'],   'price_modifier' => 0],
                            ['value_key' => 'xl',  'label' => ['ca' => 'XL',  'es' => 'XL',  'en' => 'XL'],  'price_modifier' => 0.50],
                            ['value_key' => 'xxl', 'label' => ['ca' => 'XXL', 'es' => 'XXL', 'en' => 'XXL'], 'price_modifier' => 1.00],
                        ],
                    ],
                    [
                        'key'        => 'color',
                        'label'      => ['ca' => 'Color peça', 'es' => 'Color prenda', 'en' => 'Garment colour'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'white',   'label' => ['ca' => 'Blanc',  'es' => 'Blanco',  'en' => 'White'],   'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'black',   'label' => ['ca' => 'Negre',  'es' => 'Negro',   'en' => 'Black'],   'price_modifier' => 0.50],
                            ['value_key' => 'navy',    'label' => ['ca' => 'Marí',   'es' => 'Marino',  'en' => 'Navy'],    'price_modifier' => 0.50],
                            ['value_key' => 'grey',    'label' => ['ca' => 'Gris',   'es' => 'Gris',    'en' => 'Grey'],    'price_modifier' => 0.50],
                            ['value_key' => 'red',     'label' => ['ca' => 'Vermell','es' => 'Rojo',    'en' => 'Red'],     'price_modifier' => 0.50],
                            ['value_key' => 'colored', 'label' => ['ca' => 'Altres colors','es' => 'Otros colores','en' => 'Other colours'], 'price_modifier' => 1.00],
                        ],
                    ],
                    [
                        'key'        => 'print_method',
                        'label'      => ['ca' => 'Tècnica d\'impressió', 'es' => 'Técnica de impresión', 'en' => 'Print method'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'dtg',       'label' => ['ca' => 'DTG digital',     'es' => 'DTG digital',     'en' => 'DTG digital'],      'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'screen',    'label' => ['ca' => 'Serigrafia',       'es' => 'Serigrafía',       'en' => 'Screen printing'],   'price_modifier' => 1.50, 'production_days_modifier' => 2],
                            ['value_key' => 'vinyl',     'label' => ['ca' => 'Vinil tèxtil',     'es' => 'Vinilo textil',     'en' => 'Heat transfer vinyl'], 'price_modifier' => 2.00],
                            ['value_key' => 'embroidery','label' => ['ca' => 'Brodat',           'es' => 'Bordado',            'en' => 'Embroidery'],        'price_modifier' => 5.00, 'production_days_modifier' => 3],
                        ],
                    ],
                    [
                        'key'        => 'print_location',
                        'label'      => ['ca' => 'Ubicació de la impressió', 'es' => 'Ubicación de la impresión', 'en' => 'Print location'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'front', 'label' => ['ca' => 'Davant',         'es' => 'Delante',        'en' => 'Front'],        'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'back',  'label' => ['ca' => 'Darrere',         'es' => 'Detrás',          'en' => 'Back'],         'price_modifier' => 0],
                            ['value_key' => 'both',  'label' => ['ca' => 'Davant + darrere', 'es' => 'Delante + detrás', 'en' => 'Front + back'], 'price_modifier' => 2.50],
                        ],
                    ],
                ],
            ],

            'gorres-barrets' => [
                'tiers'   => $app,
                'options' => [
                    [
                        'key'        => 'cap_type',
                        'label'      => ['ca' => 'Tipus de gorra', 'es' => 'Tipo de gorra', 'en' => 'Cap type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'baseball', 'label' => ['ca' => 'Beisbol clàssica', 'es' => 'Béisbol clásica', 'en' => 'Baseball cap'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'snapback', 'label' => ['ca' => 'Snapback',          'es' => 'Snapback',         'en' => 'Snapback'],     'price_modifier' => 1.50],
                            ['value_key' => 'trucker',  'label' => ['ca' => 'Trucker',           'es' => 'Trucker',          'en' => 'Trucker'],      'price_modifier' => 1.00],
                            ['value_key' => 'beanie',   'label' => ['ca' => 'Gorra d\'hivern',   'es' => 'Gorro de invierno', 'en' => 'Beanie'],       'price_modifier' => 1.80],
                            ['value_key' => 'bucket',   'label' => ['ca' => 'Bucket',            'es' => 'Bucket',           'en' => 'Bucket hat'],   'price_modifier' => 2.20],
                        ],
                    ],
                    [
                        'key'        => 'color',
                        'label'      => ['ca' => 'Color', 'es' => 'Color', 'en' => 'Colour'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'black', 'label' => ['ca' => 'Negre', 'es' => 'Negro', 'en' => 'Black'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'white', 'label' => ['ca' => 'Blanc', 'es' => 'Blanco', 'en' => 'White'], 'price_modifier' => 0],
                            ['value_key' => 'navy',  'label' => ['ca' => 'Marí',  'es' => 'Marino', 'en' => 'Navy'],  'price_modifier' => 0],
                            ['value_key' => 'grey',  'label' => ['ca' => 'Gris',  'es' => 'Gris',   'en' => 'Grey'],  'price_modifier' => 0],
                            ['value_key' => 'red',   'label' => ['ca' => 'Vermell','es' => 'Rojo',   'en' => 'Red'],   'price_modifier' => 0],
                        ],
                    ],
                    [
                        'key'        => 'print_method',
                        'label'      => ['ca' => 'Tècnica', 'es' => 'Técnica', 'en' => 'Print method'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'embroidery', 'label' => ['ca' => 'Brodat',      'es' => 'Bordado',      'en' => 'Embroidery'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'screen',     'label' => ['ca' => 'Serigrafia',  'es' => 'Serigrafía',   'en' => 'Screen printing'], 'price_modifier' => -0.50],
                            ['value_key' => 'patch',      'label' => ['ca' => 'Pegat brodat','es' => 'Parche bordado','en' => 'Embroidered patch'], 'price_modifier' => 1.50, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'print_location',
                        'label'      => ['ca' => 'Ubicació', 'es' => 'Ubicación', 'en' => 'Location'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'front', 'label' => ['ca' => 'Davant',  'es' => 'Delante',  'en' => 'Front'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'side',  'label' => ['ca' => 'Lateral', 'es' => 'Lateral',  'en' => 'Side'],   'price_modifier' => 0],
                            ['value_key' => 'back',  'label' => ['ca' => 'Darrere', 'es' => 'Detrás',   'en' => 'Back'],   'price_modifier' => 0],
                        ],
                    ],
                ],
            ],

            'bosses-tote' => [
                'tiers'   => $app,
                'options' => [
                    [
                        'key'        => 'bag_type',
                        'label'      => ['ca' => 'Tipus de bossa', 'es' => 'Tipo de bolsa', 'en' => 'Bag type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'tote_cotton',  'label' => ['ca' => 'Tote cotó 140 gr',   'es' => 'Tote algodón 140 gr',    'en' => '140 gsm cotton tote'],   'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'tote_heavy',   'label' => ['ca' => 'Tote cotó 220 gr',   'es' => 'Tote algodón 220 gr',    'en' => '220 gsm heavy cotton tote'], 'price_modifier' => 1.50],
                            ['value_key' => 'jute',         'label' => ['ca' => 'Jute natural',        'es' => 'Yute natural',            'en' => 'Natural jute'],          'price_modifier' => 3.00],
                            ['value_key' => 'drawstring',   'label' => ['ca' => 'Sarró amb cordó',     'es' => 'Mochila con cordón',       'en' => 'Drawstring backpack'],    'price_modifier' => 2.00],
                            ['value_key' => 'canvas',       'label' => ['ca' => 'Canvas gruixut',      'es' => 'Canvas grueso',            'en' => 'Heavy canvas'],          'price_modifier' => 3.50],
                        ],
                    ],
                    [
                        'key'        => 'color',
                        'label'      => ['ca' => 'Color', 'es' => 'Color', 'en' => 'Colour'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'natural', 'label' => ['ca' => 'Natural cru',     'es' => 'Natural crudo',    'en' => 'Natural raw'],   'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'black',   'label' => ['ca' => 'Negre',            'es' => 'Negro',             'en' => 'Black'],         'price_modifier' => 0.30],
                            ['value_key' => 'white',   'label' => ['ca' => 'Blanc',            'es' => 'Blanco',            'en' => 'White'],         'price_modifier' => 0.30],
                            ['value_key' => 'colored', 'label' => ['ca' => 'Colors vius',      'es' => 'Colores vivos',     'en' => 'Bright colours'], 'price_modifier' => 0.60],
                        ],
                    ],
                    [
                        'key'        => 'print_method',
                        'label'      => ['ca' => 'Tècnica', 'es' => 'Técnica', 'en' => 'Print method'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'screen',     'label' => ['ca' => 'Serigrafia', 'es' => 'Serigrafía', 'en' => 'Screen printing'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'dtg',        'label' => ['ca' => 'DTG digital','es' => 'DTG digital','en' => 'DTG digital'],     'price_modifier' => 1.00],
                            ['value_key' => 'embroidery', 'label' => ['ca' => 'Brodat',     'es' => 'Bordado',    'en' => 'Embroidery'],      'price_modifier' => 3.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'print_location',
                        'label'      => ['ca' => 'Ubicació', 'es' => 'Ubicación', 'en' => 'Location'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'one_side',  'label' => ['ca' => 'Una cara',  'es' => 'Una cara',  'en' => 'One side'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'both_sides','label' => ['ca' => 'Dues cares','es' => 'Dos caras','en' => 'Both sides'],'price_modifier' => 1.50],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Drinkware
            // ═══════════════════════════════════════════════════════

            'tasses' => [
                'tiers'   => $low,
                'options' => [
                    [
                        'key'        => 'mug_type',
                        'label'      => ['ca' => 'Tipus de tassa', 'es' => 'Tipo de taza', 'en' => 'Mug type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'ceramic_white', 'label' => ['ca' => 'Ceràmica blanca',        'es' => 'Cerámica blanca',       'en' => 'White ceramic'],     'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'ceramic_black', 'label' => ['ca' => 'Ceràmica interior negre', 'es' => 'Cerámica interior negro','en' => 'Black-inside ceramic'], 'price_modifier' => 1.00],
                            ['value_key' => 'magic',         'label' => ['ca' => 'Màgica (canvi color)',   'es' => 'Mágica (cambio color)', 'en' => 'Magic (colour-change)'], 'price_modifier' => 2.50, 'production_days_modifier' => 1],
                            ['value_key' => 'thermal',       'label' => ['ca' => 'Termos metàl·lic',        'es' => 'Termo metálico',         'en' => 'Thermal metal'],     'price_modifier' => 4.50],
                            ['value_key' => 'glass',         'label' => ['ca' => 'Vidre transparent',       'es' => 'Cristal transparente',   'en' => 'Clear glass'],       'price_modifier' => 3.00, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'capacity',
                        'label'      => ['ca' => 'Capacitat', 'es' => 'Capacidad', 'en' => 'Capacity'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => '325ml', 'label' => ['ca' => '325 ml (11 oz)', 'es' => '325 ml (11 oz)', 'en' => '325 ml (11 oz)'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '450ml', 'label' => ['ca' => '450 ml (15 oz)', 'es' => '450 ml (15 oz)', 'en' => '450 ml (15 oz)'], 'price_modifier' => 1.50],
                        ],
                    ],
                    [
                        'key'        => 'print_area',
                        'label'      => ['ca' => 'Àrea d\'impressió', 'es' => 'Área de impresión', 'en' => 'Print area'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'one_side',   'label' => ['ca' => 'Una cara',       'es' => 'Una cara',        'en' => 'One side'],       'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'both_sides', 'label' => ['ca' => 'Dues cares',     'es' => 'Dos caras',        'en' => 'Both sides'],     'price_modifier' => 1.00],
                            ['value_key' => 'wraparound', 'label' => ['ca' => 'Impressió 360°', 'es' => 'Impresión 360°',   'en' => 'Full wraparound'],'price_modifier' => 2.00, 'production_days_modifier' => 1],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Promo (small items)
            // ═══════════════════════════════════════════════════════

            'fundes-mobil' => [
                'tiers'   => $low,
                'options' => [
                    [
                        'key'        => 'phone_brand',
                        'label'      => ['ca' => 'Marca del mòbil', 'es' => 'Marca del móvil', 'en' => 'Phone brand'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'iphone',      'label' => ['ca' => 'iPhone',         'es' => 'iPhone',         'en' => 'iPhone'],        'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'samsung',     'label' => ['ca' => 'Samsung Galaxy', 'es' => 'Samsung Galaxy', 'en' => 'Samsung Galaxy'],'price_modifier' => 0],
                            ['value_key' => 'xiaomi',      'label' => ['ca' => 'Xiaomi',          'es' => 'Xiaomi',          'en' => 'Xiaomi'],         'price_modifier' => 0],
                            ['value_key' => 'huawei',      'label' => ['ca' => 'Huawei',          'es' => 'Huawei',          'en' => 'Huawei'],         'price_modifier' => 0],
                            ['value_key' => 'other',       'label' => ['ca' => 'Altre (consultar)','es' => 'Otro (consultar)','en' => 'Other (enquire)'], 'price_modifier' => 1.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'case_type',
                        'label'      => ['ca' => 'Tipus de funda', 'es' => 'Tipo de funda', 'en' => 'Case type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'hard_plastic', 'label' => ['ca' => 'Rígida plàstic', 'es' => 'Rígida plástico', 'en' => 'Hard plastic'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'silicone',     'label' => ['ca' => 'Silicona TPU',   'es' => 'Silicona TPU',   'en' => 'TPU silicone'],  'price_modifier' => 2.00],
                            ['value_key' => 'wallet',       'label' => ['ca' => 'Tipus cartera',  'es' => 'Tipo cartera',    'en' => 'Wallet-style'],  'price_modifier' => 5.00, 'production_days_modifier' => 1],
                            ['value_key' => 'tough',        'label' => ['ca' => 'Reforçada',      'es' => 'Reforzada',       'en' => 'Tough case'],    'price_modifier' => 3.50],
                        ],
                    ],
                    [
                        'key'        => 'base_color',
                        'label'      => ['ca' => 'Color base', 'es' => 'Color base', 'en' => 'Base colour'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'clear', 'label' => ['ca' => 'Transparent', 'es' => 'Transparente', 'en' => 'Clear'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'white', 'label' => ['ca' => 'Blanc',        'es' => 'Blanco',        'en' => 'White'], 'price_modifier' => 0],
                            ['value_key' => 'black', 'label' => ['ca' => 'Negre',        'es' => 'Negro',         'en' => 'Black'], 'price_modifier' => 0],
                        ],
                    ],
                ],
            ],

            'alfombretes' => [
                'tiers'   => $medium,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'standard', 'label' => ['ca' => 'Estàndard 22×18 cm', 'es' => 'Estándar 22×18 cm', 'en' => 'Standard 22×18 cm'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'large',    'label' => ['ca' => 'Gran 30×25 cm',      'es' => 'Grande 30×25 cm',    'en' => 'Large 30×25 cm'],    'price_modifier' => 1.00],
                            ['value_key' => 'gaming',   'label' => ['ca' => 'Gaming 80×30 cm',    'es' => 'Gaming 80×30 cm',    'en' => 'Gaming 80×30 cm'],   'price_modifier' => 4.50, 'production_days_modifier' => 1],
                            ['value_key' => 'xxl',      'label' => ['ca' => 'XXL 90×40 cm',       'es' => 'XXL 90×40 cm',       'en' => 'XXL 90×40 cm'],      'price_modifier' => 6.00, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'thickness',
                        'label'      => ['ca' => 'Gruix', 'es' => 'Grosor', 'en' => 'Thickness'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => '2mm', 'label' => ['ca' => '2 mm', 'es' => '2 mm', 'en' => '2 mm'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '3mm', 'label' => ['ca' => '3 mm', 'es' => '3 mm', 'en' => '3 mm'], 'price_modifier' => 0.80],
                            ['value_key' => '5mm', 'label' => ['ca' => '5 mm', 'es' => '5 mm', 'en' => '5 mm'], 'price_modifier' => 1.80],
                        ],
                    ],
                    [
                        'key'         => 'edge',
                        'label'       => ['ca' => 'Vora', 'es' => 'Borde', 'en' => 'Edge'],
                        'input_type'  => 'radio',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'unstitched', 'label' => ['ca' => 'Tallada',     'es' => 'Cortado',      'en' => 'Cut edge'],      'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'stitched',   'label' => ['ca' => 'Cosida',       'es' => 'Cosido',        'en' => 'Stitched edge'], 'price_modifier' => 1.50, 'production_days_modifier' => 1],
                        ],
                    ],
                ],
            ],

            'clauers' => [
                'tiers'   => $bulk,
                'options' => [
                    [
                        'key'        => 'material',
                        'label'      => ['ca' => 'Material', 'es' => 'Material', 'en' => 'Material'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'acrylic', 'label' => ['ca' => 'Metacrilat',    'es' => 'Metacrilato',    'en' => 'Acrylic'],      'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'metal',   'label' => ['ca' => 'Metall gravat', 'es' => 'Metal grabado',   'en' => 'Engraved metal'],'price_modifier' => 0.80, 'production_days_modifier' => 1],
                            ['value_key' => 'leather', 'label' => ['ca' => 'Pell sintètica','es' => 'Piel sintética',  'en' => 'Synthetic leather'], 'price_modifier' => 1.00],
                            ['value_key' => 'rubber',  'label' => ['ca' => 'Goma PVC',       'es' => 'Goma PVC',        'en' => 'PVC rubber'],   'price_modifier' => 0.50],
                            ['value_key' => 'wood',    'label' => ['ca' => 'Fusta',          'es' => 'Madera',          'en' => 'Wood'],         'price_modifier' => 1.20, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'shape',
                        'label'      => ['ca' => 'Forma', 'es' => 'Forma', 'en' => 'Shape'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'rectangle', 'label' => ['ca' => 'Rectangle', 'es' => 'Rectángulo', 'en' => 'Rectangle'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'round',     'label' => ['ca' => 'Rodó',       'es' => 'Redondo',     'en' => 'Round'],     'price_modifier' => 0.10],
                            ['value_key' => 'custom',    'label' => ['ca' => 'Tall a mida','es' => 'Troquelado a medida','en' => 'Custom die-cut'], 'price_modifier' => 0.50, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'sides',
                        'label'      => ['ca' => 'Cares impreses', 'es' => 'Caras impresas', 'en' => 'Printed sides'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'single', 'label' => ['ca' => 'Una cara',   'es' => 'Una cara',   'en' => 'Single side'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'double', 'label' => ['ca' => 'Dues cares', 'es' => 'Dos caras',  'en' => 'Double side'], 'price_modifier' => 0.30],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: ID cards
            // ═══════════════════════════════════════════════════════

            'credencials' => [
                'tiers'   => $bulk,
                'options' => [
                    [
                        'key'        => 'card_material',
                        'label'      => ['ca' => 'Material de la targeta', 'es' => 'Material de la tarjeta', 'en' => 'Card material'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'pvc_rigid',      'label' => ['ca' => 'PVC rígid',          'es' => 'PVC rígido',           'en' => 'Rigid PVC'],            'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'pvc_frosted',    'label' => ['ca' => 'PVC glacejat',       'es' => 'PVC esmerilado',       'en' => 'Frosted PVC'],          'price_modifier' => 0.15],
                            ['value_key' => 'paper_laminated','label' => ['ca' => 'Cartolina plastificada', 'es' => 'Cartulina plastificada', 'en' => 'Laminated cardstock'], 'price_modifier' => -0.30],
                        ],
                    ],
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'credit', 'label' => ['ca' => 'Format targeta crèdit (85×55 mm)', 'es' => 'Formato tarjeta crédito (85×55 mm)', 'en' => 'Credit card size (85×55 mm)'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'badge',  'label' => ['ca' => 'Badge gran (90×120 mm)',            'es' => 'Badge grande (90×120 mm)',           'en' => 'Large badge (90×120 mm)'],     'price_modifier' => 0.25],
                        ],
                    ],
                    [
                        'key'        => 'sides',
                        'label'      => ['ca' => 'Cares impreses', 'es' => 'Caras impresas', 'en' => 'Printed sides'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'single', 'label' => ['ca' => 'Una cara',   'es' => 'Una cara',  'en' => 'Single side'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'double', 'label' => ['ca' => 'Dues cares', 'es' => 'Dos caras', 'en' => 'Double side'], 'price_modifier' => 0.20],
                        ],
                    ],
                    [
                        'key'         => 'accessory',
                        'label'       => ['ca' => 'Accessori', 'es' => 'Accesorio', 'en' => 'Accessory'],
                        'input_type'  => 'select',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'none',           'label' => ['ca' => 'Sense accessori',     'es' => 'Sin accesorio',        'en' => 'No accessory'],        'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'lanyard_plain', 'label' => ['ca' => 'Cordó llis',          'es' => 'Cordón liso',          'en' => 'Plain lanyard'],       'price_modifier' => 0.80],
                            ['value_key' => 'lanyard_print', 'label' => ['ca' => 'Cordó imprès',        'es' => 'Cordón impreso',        'en' => 'Printed lanyard'],     'price_modifier' => 1.50, 'production_days_modifier' => 1],
                            ['value_key' => 'clip',          'label' => ['ca' => 'Pinça retràctil',      'es' => 'Pinza retráctil',       'en' => 'Retractable clip'],    'price_modifier' => 1.20],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Wall decor
            // ═══════════════════════════════════════════════════════

            'impressio-metacrilat' => [
                'tiers'   => $low,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => '30x40',  'label' => ['ca' => '30×40 cm',  'es' => '30×40 cm',  'en' => '30×40 cm'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '40x60',  'label' => ['ca' => '40×60 cm',  'es' => '40×60 cm',  'en' => '40×60 cm'],  'price_modifier' => 15.00],
                            ['value_key' => '60x90',  'label' => ['ca' => '60×90 cm',  'es' => '60×90 cm',  'en' => '60×90 cm'],  'price_modifier' => 35.00, 'production_days_modifier' => 1],
                            ['value_key' => '90x120', 'label' => ['ca' => '90×120 cm', 'es' => '90×120 cm', 'en' => '90×120 cm'], 'price_modifier' => 70.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'thickness',
                        'label'      => ['ca' => 'Gruix', 'es' => 'Grosor', 'en' => 'Thickness'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => '5mm',  'label' => ['ca' => '5 mm',  'es' => '5 mm',  'en' => '5 mm'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '8mm',  'label' => ['ca' => '8 mm',  'es' => '8 mm',  'en' => '8 mm'],  'price_modifier' => 8.00],
                            ['value_key' => '10mm', 'label' => ['ca' => '10 mm', 'es' => '10 mm', 'en' => '10 mm'], 'price_modifier' => 15.00],
                        ],
                    ],
                    [
                        'key'        => 'finish',
                        'label'      => ['ca' => 'Acabat superficial', 'es' => 'Acabado superficial', 'en' => 'Surface finish'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'glossy', 'label' => ['ca' => 'Brillant', 'es' => 'Brillante', 'en' => 'Glossy'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'matte',  'label' => ['ca' => 'Mat',       'es' => 'Mate',       'en' => 'Matte'],  'price_modifier' => 2.00],
                        ],
                    ],
                    [
                        'key'         => 'hanging',
                        'label'       => ['ca' => 'Sistema de muntatge', 'es' => 'Sistema de montaje', 'en' => 'Hanging system'],
                        'input_type'  => 'select',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'floating', 'label' => ['ca' => 'Separadors flotants',  'es' => 'Separadores flotantes',  'en' => 'Floating mounts'],  'price_modifier' => 4.00, 'is_default' => true],
                            ['value_key' => 'hooks',    'label' => ['ca' => 'Ganxos mural',          'es' => 'Ganchos mural',          'en' => 'Wall hooks'],        'price_modifier' => 2.00],
                            ['value_key' => 'none',     'label' => ['ca' => 'Cap',                    'es' => 'Ninguno',                 'en' => 'None'],              'price_modifier' => 0],
                        ],
                    ],
                ],
            ],

            'impressio-canvas' => [
                'tiers'   => $low,
                'options' => [
                    [
                        'key'        => 'size',
                        'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => '30x40',  'label' => ['ca' => '30×40 cm',  'es' => '30×40 cm',  'en' => '30×40 cm'],  'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '40x60',  'label' => ['ca' => '40×60 cm',  'es' => '40×60 cm',  'en' => '40×60 cm'],  'price_modifier' => 10.00],
                            ['value_key' => '60x90',  'label' => ['ca' => '60×90 cm',  'es' => '60×90 cm',  'en' => '60×90 cm'],  'price_modifier' => 25.00, 'production_days_modifier' => 1],
                            ['value_key' => '90x120', 'label' => ['ca' => '90×120 cm', 'es' => '90×120 cm', 'en' => '90×120 cm'], 'price_modifier' => 50.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'canvas_type',
                        'label'      => ['ca' => 'Tipus de tela', 'es' => 'Tipo de tela', 'en' => 'Canvas type'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'poly_cotton', 'label' => ['ca' => 'Poli-cotó',     'es' => 'Poli-algodón',   'en' => 'Poly-cotton blend'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'pure_cotton', 'label' => ['ca' => 'Cotó pur',       'es' => 'Algodón puro',    'en' => 'Pure cotton'],       'price_modifier' => 4.00],
                            ['value_key' => 'premium',     'label' => ['ca' => 'Premium gruixut','es' => 'Premium grueso',  'en' => 'Heavy premium'],     'price_modifier' => 8.00, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'frame_depth',
                        'label'      => ['ca' => 'Bastidor', 'es' => 'Bastidor', 'en' => 'Stretcher frame'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => '2cm',   'label' => ['ca' => 'Bastidor 2 cm',          'es' => 'Bastidor 2 cm',          'en' => '2 cm frame'],          'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => '4cm',   'label' => ['ca' => 'Bastidor 4 cm',          'es' => 'Bastidor 4 cm',          'en' => '4 cm frame'],          'price_modifier' => 4.00],
                            ['value_key' => 'rolled','label' => ['ca' => 'Només imprès (sense bastidor)','es' => 'Sólo impreso (sin bastidor)','en' => 'Rolled print only'], 'price_modifier' => -6.00, 'production_days_modifier' => -1],
                        ],
                    ],
                    [
                        'key'         => 'finish',
                        'label'       => ['ca' => 'Protecció', 'es' => 'Protección', 'en' => 'Protective finish'],
                        'input_type'  => 'radio',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'matte',   'label' => ['ca' => 'Vernís mat',       'es' => 'Barniz mate',      'en' => 'Matte varnish'],    'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'glossy',  'label' => ['ca' => 'Vernís brillant',  'es' => 'Barniz brillante', 'en' => 'Gloss varnish'],    'price_modifier' => 2.00],
                            ['value_key' => 'none',    'label' => ['ca' => 'Sense vernís',     'es' => 'Sin barniz',        'en' => 'No varnish'],       'price_modifier' => -1.50],
                        ],
                    ],
                ],
            ],

            // ═══════════════════════════════════════════════════════
            // FAMILY: Special-finish services
            // ═══════════════════════════════════════════════════════

            'gofrat-relleu' => [
                'tiers'   => $small,
                'options' => [
                    [
                        'key'        => 'emboss_type',
                        'label'      => ['ca' => 'Tipus de relleu', 'es' => 'Tipo de relieve', 'en' => 'Emboss type'],
                        'input_type' => 'radio',
                        'values'     => [
                            ['value_key' => 'raised',   'label' => ['ca' => 'Gofrat en alt (relleu)',  'es' => 'Gofrado en alto (relieve)', 'en' => 'Embossed (raised)'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'sunken',   'label' => ['ca' => 'Gofrat en baix (contrarelleu)','es' => 'Gofrado en bajo (contrarrelieve)','en' => 'Debossed (sunken)'], 'price_modifier' => 0],
                            ['value_key' => 'combined', 'label' => ['ca' => 'Combinat',                  'es' => 'Combinado',                   'en' => 'Combined'],         'price_modifier' => 3.00, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'area_size',
                        'label'      => ['ca' => 'Àrea del segell', 'es' => 'Área del sello', 'en' => 'Stamp area'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 's',  'label' => ['ca' => 'Fins a 3×3 cm',   'es' => 'Hasta 3×3 cm',   'en' => 'Up to 3×3 cm'],   'price_modifier' => -2.00],
                            ['value_key' => 'm',  'label' => ['ca' => 'Fins a 5×5 cm',   'es' => 'Hasta 5×5 cm',   'en' => 'Up to 5×5 cm'],   'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'l',  'label' => ['ca' => 'Fins a 10×10 cm', 'es' => 'Hasta 10×10 cm', 'en' => 'Up to 10×10 cm'], 'price_modifier' => 5.00, 'production_days_modifier' => 1],
                            ['value_key' => 'xl', 'label' => ['ca' => 'Fins a 15×15 cm', 'es' => 'Hasta 15×15 cm', 'en' => 'Up to 15×15 cm'], 'price_modifier' => 10.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'base_material',
                        'label'      => ['ca' => 'Suport base', 'es' => 'Soporte base', 'en' => 'Base substrate'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'cardstock',    'label' => ['ca' => 'Cartolina 300+ gr', 'es' => 'Cartulina 300+ gr', 'en' => '300+ gsm cardstock'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'premium_paper','label' => ['ca' => 'Paper premium',      'es' => 'Papel premium',      'en' => 'Premium paper'],      'price_modifier' => 2.00],
                            ['value_key' => 'leatherette',  'label' => ['ca' => 'Polipell',            'es' => 'Polipiel',            'en' => 'Leatherette'],        'price_modifier' => 5.00, 'production_days_modifier' => 1],
                        ],
                    ],
                ],
            ],

            'estampacio-foil' => [
                'tiers'   => $small,
                'options' => [
                    [
                        'key'        => 'foil_color',
                        'label'      => ['ca' => 'Color del foil', 'es' => 'Color del foil', 'en' => 'Foil colour'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'gold',         'label' => ['ca' => 'Or',             'es' => 'Oro',             'en' => 'Gold'],         'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'silver',       'label' => ['ca' => 'Plata',           'es' => 'Plata',           'en' => 'Silver'],       'price_modifier' => 0],
                            ['value_key' => 'rose_gold',    'label' => ['ca' => 'Or rosa',         'es' => 'Oro rosa',         'en' => 'Rose gold'],    'price_modifier' => 1.00],
                            ['value_key' => 'copper',       'label' => ['ca' => 'Coure',            'es' => 'Cobre',            'en' => 'Copper'],       'price_modifier' => 1.00],
                            ['value_key' => 'holographic',  'label' => ['ca' => 'Hologràfic',      'es' => 'Holográfico',      'en' => 'Holographic'],  'price_modifier' => 3.00, 'production_days_modifier' => 1],
                            ['value_key' => 'custom',       'label' => ['ca' => 'Color a mida',    'es' => 'Color a medida',   'en' => 'Custom colour'], 'price_modifier' => 5.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'area_size',
                        'label'      => ['ca' => 'Àrea d\'estampació', 'es' => 'Área de estampación', 'en' => 'Stamp area'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 's',  'label' => ['ca' => 'Fins a 3×3 cm',   'es' => 'Hasta 3×3 cm',   'en' => 'Up to 3×3 cm'],   'price_modifier' => -3.00],
                            ['value_key' => 'm',  'label' => ['ca' => 'Fins a 5×5 cm',   'es' => 'Hasta 5×5 cm',   'en' => 'Up to 5×5 cm'],   'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'l',  'label' => ['ca' => 'Fins a 10×10 cm', 'es' => 'Hasta 10×10 cm', 'en' => 'Up to 10×10 cm'], 'price_modifier' => 6.00, 'production_days_modifier' => 1],
                            ['value_key' => 'xl', 'label' => ['ca' => 'Fins a 15×15 cm', 'es' => 'Hasta 15×15 cm', 'en' => 'Up to 15×15 cm'], 'price_modifier' => 12.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'        => 'base_material',
                        'label'      => ['ca' => 'Suport base', 'es' => 'Soporte base', 'en' => 'Base substrate'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'cardstock',    'label' => ['ca' => 'Cartolina',         'es' => 'Cartulina',          'en' => 'Cardstock'],          'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'premium_paper','label' => ['ca' => 'Paper premium',      'es' => 'Papel premium',       'en' => 'Premium paper'],       'price_modifier' => 2.00],
                            ['value_key' => 'leatherette',  'label' => ['ca' => 'Polipell',            'es' => 'Polipiel',             'en' => 'Leatherette'],         'price_modifier' => 5.00, 'production_days_modifier' => 1],
                        ],
                    ],
                ],
            ],

            'vernis-uv' => [
                'tiers'   => $small,
                'options' => [
                    [
                        'key'        => 'uv_type',
                        'label'      => ['ca' => 'Tipus de vernís', 'es' => 'Tipo de barniz', 'en' => 'UV type'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'gloss',      'label' => ['ca' => 'Brillant',            'es' => 'Brillante',         'en' => 'Glossy'],              'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'matte',      'label' => ['ca' => 'Mat texturat',        'es' => 'Mate texturizado',  'en' => 'Matte texture'],       'price_modifier' => 1.00],
                            ['value_key' => 'raised_3d',  'label' => ['ca' => '3D en relleu',         'es' => '3D en relieve',      'en' => '3D raised UV'],        'price_modifier' => 4.00, 'production_days_modifier' => 1],
                            ['value_key' => 'glitter',    'label' => ['ca' => 'Amb purpurina',        'es' => 'Con purpurina',      'en' => 'Glitter UV'],          'price_modifier' => 5.00, 'production_days_modifier' => 1],
                        ],
                    ],
                    [
                        'key'        => 'area_size',
                        'label'      => ['ca' => 'Àrea d\'aplicació', 'es' => 'Área de aplicación', 'en' => 'Application area'],
                        'input_type' => 'select',
                        'values'     => [
                            ['value_key' => 'spot_small',   'label' => ['ca' => 'Punts petits (fins 10 cm²)',  'es' => 'Puntos pequeños (hasta 10 cm²)', 'en' => 'Small spots (up to 10 cm²)'], 'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'spot_medium',  'label' => ['ca' => 'Àrea mitjana (fins 50 cm²)',  'es' => 'Área media (hasta 50 cm²)',      'en' => 'Medium area (up to 50 cm²)'], 'price_modifier' => 3.00],
                            ['value_key' => 'spot_large',   'label' => ['ca' => 'Àrea gran (fins 150 cm²)',     'es' => 'Área grande (hasta 150 cm²)',     'en' => 'Large area (up to 150 cm²)'], 'price_modifier' => 7.00, 'production_days_modifier' => 1],
                            ['value_key' => 'full_coverage','label' => ['ca' => 'Cobertura total',              'es' => 'Cobertura total',                  'en' => 'Full coverage'],              'price_modifier' => 15.00, 'production_days_modifier' => 2],
                        ],
                    ],
                    [
                        'key'         => 'base_finish',
                        'label'       => ['ca' => 'Acabat base', 'es' => 'Acabado base', 'en' => 'Base finish'],
                        'input_type'  => 'radio',
                        'is_required' => false,
                        'values'      => [
                            ['value_key' => 'none',        'label' => ['ca' => 'Cap',                'es' => 'Ninguno',          'en' => 'None'],              'price_modifier' => 0, 'is_default' => true],
                            ['value_key' => 'matte_lam',   'label' => ['ca' => 'Amb plastificat mat','es' => 'Con plastif. mate','en' => 'With matte laminate'], 'price_modifier' => 2.00, 'production_days_modifier' => 1],
                            ['value_key' => 'gloss_lam',   'label' => ['ca' => 'Amb plastificat brillant','es' => 'Con plastif. brillo','en' => 'With gloss laminate'], 'price_modifier' => 2.00, 'production_days_modifier' => 1],
                        ],
                    ],
                ],
            ],
        ];
    }
};
