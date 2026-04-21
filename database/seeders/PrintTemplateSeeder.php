<?php

namespace Database\Seeders;

use App\Models\PrintTemplate;
use Illuminate\Database\Seeder;

class PrintTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // ── Business Cards ─────────────────────────────────────
        $bc = PrintTemplate::create([
            'slug'                 => 'targetes-visita',
            'name'                 => ['ca' => 'Targetes de Visita', 'es' => 'Tarjetas de Visita', 'en' => 'Business Cards'],
            'description'          => [
                'ca' => 'Targetes de visita professionals d\'alta qualitat, disponibles en diverses mides, papers i acabats.',
                'es' => 'Tarjetas de visita profesionales de alta calidad, disponibles en varios tamaños, papeles y acabados.',
                'en' => 'High-quality professional business cards available in various sizes, papers and finishes.',
            ],
            'icon'                 => '💼',
            'base_price'           => 0.0250,
            'vat_rate'             => 21.00,
            'base_production_days' => 3,
            'sort_order'           => 1,
            'is_active'            => true,
        ]);

        // Options
        $optSize = $bc->options()->create([
            'key'        => 'size',
            'label'      => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
            'input_type' => 'select',
            'is_required'=> true,
            'sort_order' => 1,
        ]);
        $optSize->values()->createMany([
            ['value_key' => '85x55',  'label' => ['ca' => '85×55 mm (estàndard)', 'es' => '85×55 mm (estándar)', 'en' => '85×55 mm (standard)'],  'price_modifier' => 0,      'price_modifier_type' => 'flat',    'production_days_modifier' => 0, 'is_default' => true,  'is_active' => true, 'sort_order' => 1],
            ['value_key' => '90x55',  'label' => ['ca' => '90×55 mm (europeu)',   'es' => '90×55 mm (europeo)',   'en' => '90×55 mm (european)'],   'price_modifier' => 0.0020, 'price_modifier_type' => 'flat',    'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 2],
            ['value_key' => '85x54',  'label' => ['ca' => '85×54 mm (USA)',       'es' => '85×54 mm (USA)',       'en' => '85×54 mm (USA)'],         'price_modifier' => 0,      'price_modifier_type' => 'flat',    'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
            ['value_key' => 'square', 'label' => ['ca' => '60×60 mm (quadrada)',  'es' => '60×60 mm (cuadrada)',  'en' => '60×60 mm (square)'],      'price_modifier' => 5,      'price_modifier_type' => 'percent', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 4],
        ]);

        $optPaper = $bc->options()->create([
            'key'        => 'paper_type',
            'label'      => ['ca' => 'Tipus de paper', 'es' => 'Tipo de papel', 'en' => 'Paper type'],
            'input_type' => 'select',
            'is_required'=> true,
            'sort_order' => 2,
        ]);
        $optPaper->values()->createMany([
            ['value_key' => 'coated_silk',  'label' => ['ca' => 'Couché Satinat',  'es' => 'Couché Satinado',  'en' => 'Silk Coated'],    'price_modifier' => 0,      'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => true,  'is_active' => true, 'sort_order' => 1],
            ['value_key' => 'coated_gloss', 'label' => ['ca' => 'Couché Brillant', 'es' => 'Couché Brillante', 'en' => 'Gloss Coated'],   'price_modifier' => 0,      'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 2],
            ['value_key' => 'uncoated',     'label' => ['ca' => 'Offset (mat)',     'es' => 'Offset (mate)',     'en' => 'Uncoated Offset'],'price_modifier' => -0.0020,'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
            ['value_key' => 'recycled',     'label' => ['ca' => 'Reciclat',         'es' => 'Reciclado',         'en' => 'Recycled'],        'price_modifier' => 0.0050, 'price_modifier_type' => 'flat', 'production_days_modifier' => 1, 'is_default' => false, 'is_active' => true, 'sort_order' => 4],
            ['value_key' => 'cotton',       'label' => ['ca' => 'Cotó 600gr',       'es' => 'Algodón 600gr',     'en' => 'Cotton 600gsm'],   'price_modifier' => 15,     'price_modifier_type' => 'percent', 'production_days_modifier' => 1, 'is_default' => false, 'is_active' => true, 'sort_order' => 5],
        ]);

        $optWeight = $bc->options()->create([
            'key'        => 'paper_weight',
            'label'      => ['ca' => 'Gramatge', 'es' => 'Gramaje', 'en' => 'Paper weight'],
            'input_type' => 'radio',
            'is_required'=> true,
            'sort_order' => 3,
        ]);
        $optWeight->values()->createMany([
            ['value_key' => '300gr', 'label' => ['ca' => '300 gr/m²', 'es' => '300 gr/m²', 'en' => '300 gsm'], 'price_modifier' => 0,      'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => true,  'is_active' => true, 'sort_order' => 1],
            ['value_key' => '350gr', 'label' => ['ca' => '350 gr/m²', 'es' => '350 gr/m²', 'en' => '350 gsm'], 'price_modifier' => 0.0030, 'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 2],
            ['value_key' => '400gr', 'label' => ['ca' => '400 gr/m²', 'es' => '400 gr/m²', 'en' => '400 gsm'], 'price_modifier' => 0.0080, 'price_modifier_type' => 'flat', 'production_days_modifier' => 1, 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
        ]);

        $optColor = $bc->options()->create([
            'key'        => 'color_mode',
            'label'      => ['ca' => 'Color',  'es' => 'Color',  'en' => 'Color'],
            'input_type' => 'radio',
            'is_required'=> true,
            'sort_order' => 4,
        ]);
        $optColor->values()->createMany([
            ['value_key' => '4_0',   'label' => ['ca' => '4+0 (color una cara)',    'es' => '4+0 (color una cara)',     'en' => '4+0 (colour one side)'],    'price_modifier' => 0,     'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 1],
            ['value_key' => '4_4',   'label' => ['ca' => '4+4 (color dues cares)',  'es' => '4+4 (color dos caras)',    'en' => '4+4 (colour both sides)'],  'price_modifier' => 0.0050,'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => true,  'is_active' => true, 'sort_order' => 2],
            ['value_key' => '1_0',   'label' => ['ca' => '1+0 (B/N una cara)',      'es' => '1+0 (B/N una cara)',       'en' => '1+0 (B&W one side)'],       'price_modifier' => -0.0050,'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
            ['value_key' => '1_1',   'label' => ['ca' => '1+1 (B/N dues cares)',    'es' => '1+1 (B/N dos caras)',      'en' => '1+1 (B&W both sides)'],     'price_modifier' => -0.0030,'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 4],
        ]);

        $optFinish = $bc->options()->create([
            'key'        => 'finishing',
            'label'      => ['ca' => 'Acabat', 'es' => 'Acabado', 'en' => 'Finishing'],
            'input_type' => 'select',
            'is_required'=> false,
            'sort_order' => 5,
        ]);
        $optFinish->values()->createMany([
            ['value_key' => 'none',          'label' => ['ca' => 'Sense acabat',         'es' => 'Sin acabado',           'en' => 'No finishing'],         'price_modifier' => 0,    'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => true,  'is_active' => true, 'sort_order' => 1],
            ['value_key' => 'matte_laminate','label' => ['ca' => 'Plastificat mat',       'es' => 'Plastificado mate',     'en' => 'Matte laminate'],       'price_modifier' => 8,    'price_modifier_type' => 'percent', 'production_days_modifier' => 1, 'is_default' => false, 'is_active' => true, 'sort_order' => 2],
            ['value_key' => 'gloss_laminate','label' => ['ca' => 'Plastificat brillant',  'es' => 'Plastificado brillo',   'en' => 'Gloss laminate'],       'price_modifier' => 8,    'price_modifier_type' => 'percent', 'production_days_modifier' => 1, 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
            ['value_key' => 'soft_touch',    'label' => ['ca' => 'Soft Touch',            'es' => 'Soft Touch',            'en' => 'Soft Touch'],           'price_modifier' => 15,   'price_modifier_type' => 'percent', 'production_days_modifier' => 2, 'is_default' => false, 'is_active' => true, 'sort_order' => 4],
            ['value_key' => 'spot_uv',       'label' => ['ca' => 'Vernís selectiu UV',    'es' => 'Barniz selectivo UV',   'en' => 'Spot UV varnish'],      'price_modifier' => 20,   'price_modifier_type' => 'percent', 'production_days_modifier' => 2, 'is_default' => false, 'is_active' => true, 'sort_order' => 5],
            ['value_key' => 'foil_gold',     'label' => ['ca' => 'Hot Stamping or daurat','es' => 'Hot Stamping oro',      'en' => 'Gold foil stamping'],   'price_modifier' => 30,   'price_modifier_type' => 'percent', 'production_days_modifier' => 3, 'is_default' => false, 'is_active' => true, 'sort_order' => 6],
            ['value_key' => 'rounded_corners','label'=> ['ca' => 'Cantonades arrodonides', 'es' => 'Esquinas redondeadas', 'en' => 'Rounded corners'],      'price_modifier' => 5,    'price_modifier_type' => 'percent', 'production_days_modifier' => 1, 'is_default' => false, 'is_active' => true, 'sort_order' => 7],
        ]);

        // Quantity tiers
        $bc->quantityTiers()->createMany([
            ['min_quantity' => 100,  'discount_percent' => 0,    'label' => ['ca' => '100 unitats',   'es' => '100 unidades',   'en' => '100 units'],   'is_active' => true],
            ['min_quantity' => 250,  'discount_percent' => 5,    'label' => ['ca' => '250 unitats',   'es' => '250 unidades',   'en' => '250 units'],   'is_active' => true],
            ['min_quantity' => 500,  'discount_percent' => 10,   'label' => ['ca' => '500 unitats',   'es' => '500 unidades',   'en' => '500 units'],   'is_active' => true],
            ['min_quantity' => 1000, 'discount_percent' => 15,   'label' => ['ca' => '1.000 unitats', 'es' => '1.000 unidades', 'en' => '1,000 units'], 'is_active' => true],
            ['min_quantity' => 2500, 'discount_percent' => 20,   'label' => ['ca' => '2.500 unitats', 'es' => '2.500 unidades', 'en' => '2,500 units'], 'is_active' => true],
            ['min_quantity' => 5000, 'discount_percent' => 25,   'label' => ['ca' => '5.000 unitats', 'es' => '5.000 unidades', 'en' => '5,000 units'], 'is_active' => true],
        ]);

        // Compatibility rules
        $bc->compatibilityRules()->createMany([
            // Soft touch and spot UV are incompatible
            [
                'rule_type'            => 'incompatible',
                'condition_option_key' => 'finishing',
                'condition_value_key'  => 'soft_touch',
                'target_option_key'    => 'finishing',
                'target_value_key'     => 'spot_uv',
                'message'              => [
                    'ca' => 'Soft Touch i Vernís UV no es poden combinar en el mateix treball.',
                    'es' => 'Soft Touch y Barniz UV no se pueden combinar en el mismo trabajo.',
                    'en' => 'Soft Touch and Spot UV cannot be combined on the same job.',
                ],
            ],
            // Cotton paper requires no lamination
            [
                'rule_type'            => 'incompatible',
                'condition_option_key' => 'paper_type',
                'condition_value_key'  => 'cotton',
                'target_option_key'    => 'finishing',
                'target_value_key'     => 'matte_laminate',
                'message'              => [
                    'ca' => 'El paper de cotó no admet plastificat. Escull un altre acabat.',
                    'es' => 'El papel de algodón no admite plastificado. Elige otro acabado.',
                    'en' => 'Cotton paper does not support lamination. Choose a different finish.',
                ],
            ],
            [
                'rule_type'            => 'incompatible',
                'condition_option_key' => 'paper_type',
                'condition_value_key'  => 'cotton',
                'target_option_key'    => 'finishing',
                'target_value_key'     => 'gloss_laminate',
                'message'              => [
                    'ca' => 'El paper de cotó no admet plastificat. Escull un altre acabat.',
                    'es' => 'El papel de algodón no admite plastificado. Elige otro acabado.',
                    'en' => 'Cotton paper does not support lamination. Choose a different finish.',
                ],
            ],
            // Hot stamping with recycled paper warning
            [
                'rule_type'            => 'warning',
                'condition_option_key' => 'paper_type',
                'condition_value_key'  => 'recycled',
                'target_option_key'    => 'finishing',
                'target_value_key'     => 'foil_gold',
                'message'              => [
                    'ca' => 'El Hot Stamping sobre paper reciclat pot tenir resultats menys brillants. Recomanem fer una prova primer.',
                    'es' => 'El Hot Stamping sobre papel reciclado puede tener resultados menos brillantes. Recomendamos hacer una prueba primero.',
                    'en' => 'Gold foil stamping on recycled paper may yield less vibrant results. We recommend a test print first.',
                ],
            ],
        ]);

        // ── DIN-A5 Flyers ──────────────────────────────────────
        $fly = PrintTemplate::create([
            'slug'                 => 'fullets-flyers',
            'name'                 => ['ca' => 'Fullets / Flyers', 'es' => 'Folletos / Flyers', 'en' => 'Flyers / Leaflets'],
            'description'          => [
                'ca' => 'Fullets publicitaris en diverses mides i papers, ideals per a campanyes de màrqueting.',
                'es' => 'Folletos publicitarios en varios tamaños y papeles, ideales para campañas de marketing.',
                'en' => 'Advertising flyers in various sizes and papers, ideal for marketing campaigns.',
            ],
            'icon'                 => '📄',
            'base_price'           => 0.0080,
            'vat_rate'             => 21.00,
            'base_production_days' => 2,
            'sort_order'           => 2,
            'is_active'            => true,
        ]);

        $optFlySize = $fly->options()->create([
            'key' => 'size', 'label' => ['ca' => 'Mida', 'es' => 'Tamaño', 'en' => 'Size'],
            'input_type' => 'select', 'is_required' => true, 'sort_order' => 1,
        ]);
        $optFlySize->values()->createMany([
            ['value_key' => 'a6',   'label' => ['ca' => 'A6 (105×148mm)', 'es' => 'A6 (105×148mm)', 'en' => 'A6 (105×148mm)'], 'price_modifier' => -0.0020, 'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 1],
            ['value_key' => 'a5',   'label' => ['ca' => 'A5 (148×210mm)', 'es' => 'A5 (148×210mm)', 'en' => 'A5 (148×210mm)'], 'price_modifier' => 0,       'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => true,  'is_active' => true, 'sort_order' => 2],
            ['value_key' => 'a4',   'label' => ['ca' => 'A4 (210×297mm)', 'es' => 'A4 (210×297mm)', 'en' => 'A4 (210×297mm)'], 'price_modifier' => 0.0060,  'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
            ['value_key' => 'dl',   'label' => ['ca' => 'DL (99×210mm)',  'es' => 'DL (99×210mm)',  'en' => 'DL (99×210mm)'],  'price_modifier' => -0.0010, 'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 4],
        ]);

        $optFlyPaper = $fly->options()->create([
            'key' => 'paper_weight', 'label' => ['ca' => 'Gramatge', 'es' => 'Gramaje', 'en' => 'Paper weight'],
            'input_type' => 'radio', 'is_required' => true, 'sort_order' => 2,
        ]);
        $optFlyPaper->values()->createMany([
            ['value_key' => '90gr',  'label' => ['ca' => '90 gr/m²',  'es' => '90 gr/m²',  'en' => '90 gsm'],  'price_modifier' => -0.0010, 'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 1],
            ['value_key' => '135gr', 'label' => ['ca' => '135 gr/m²', 'es' => '135 gr/m²', 'en' => '135 gsm'], 'price_modifier' => 0,       'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => true,  'is_active' => true, 'sort_order' => 2],
            ['value_key' => '170gr', 'label' => ['ca' => '170 gr/m²', 'es' => '170 gr/m²', 'en' => '170 gsm'], 'price_modifier' => 0.0020,  'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
        ]);

        $optFlyColor = $fly->options()->create([
            'key' => 'color_mode', 'label' => ['ca' => 'Color', 'es' => 'Color', 'en' => 'Color'],
            'input_type' => 'radio', 'is_required' => true, 'sort_order' => 3,
        ]);
        $optFlyColor->values()->createMany([
            ['value_key' => '4_0', 'label' => ['ca' => '4+0 (color una cara)',   'es' => '4+0 (color una cara)',    'en' => '4+0 (colour one side)'],  'price_modifier' => 0,      'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => true,  'is_active' => true, 'sort_order' => 1],
            ['value_key' => '4_4', 'label' => ['ca' => '4+4 (color dues cares)', 'es' => '4+4 (color dos caras)',   'en' => '4+4 (colour both sides)'], 'price_modifier' => 0.0030, 'price_modifier_type' => 'flat', 'production_days_modifier' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 2],
        ]);

        $fly->quantityTiers()->createMany([
            ['min_quantity' => 100,  'discount_percent' => 0,  'label' => ['ca' => '100 unitats',   'es' => '100 unidades',   'en' => '100 units'],   'is_active' => true],
            ['min_quantity' => 500,  'discount_percent' => 8,  'label' => ['ca' => '500 unitats',   'es' => '500 unidades',   'en' => '500 units'],   'is_active' => true],
            ['min_quantity' => 1000, 'discount_percent' => 12, 'label' => ['ca' => '1.000 unitats', 'es' => '1.000 unidades', 'en' => '1,000 units'], 'is_active' => true],
            ['min_quantity' => 5000, 'discount_percent' => 18, 'label' => ['ca' => '5.000 unitats', 'es' => '5.000 unidades', 'en' => '5,000 units'], 'is_active' => true],
        ]);
    }
}
