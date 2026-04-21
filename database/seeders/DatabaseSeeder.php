<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ─────────────────────────────────────────
        User::create([
            'name'         => 'Admin Copyus',
            'company_name' => 'Copyus S.L.',
            'cif'          => 'A00000000',
            'email'        => 'admin@copyus.es',
            'phone'        => '+34 900 000 000',
            'address'      => 'Carrer de Copyus, 1',
            'city'         => 'Barcelona',
            'postal_code'  => '08001',
            'country'      => 'ES',
            'locale'       => 'ca',
            'role'         => 'admin',
            'approved_at'  => now(),
            'password'     => Hash::make('password'),
        ]);

        // ── Demo B2B User ──────────────────────────────────────
        User::create([
            'name'         => 'Joan Garcia',
            'company_name' => 'Empresa Demo S.L.',
            'cif'          => 'B12345678',
            'email'        => 'demo@empresa.com',
            'phone'        => '+34 600 000 001',
            'address'      => 'Carrer de Gràcia, 45',
            'city'         => 'Barcelona',
            'postal_code'  => '08012',
            'country'      => 'ES',
            'locale'       => 'ca',
            'role'         => 'approved',
            'approved_at'  => now(),
            'password'     => Hash::make('password'),
        ]);

        // ── Categories ─────────────────────────────────────────
        /*$categories = [
            [
                'name'        => ['ca' => 'Bolígrafs i Llapis', 'es' => 'Bolígrafos y Lápices', 'en' => 'Pens & Pencils'],
                'description' => ['ca' => 'Tot tipus de bolígrafs, llapis i instruments d\'escriptura', 'es' => 'Todo tipo de bolígrafos, lápices e instrumentos de escritura', 'en' => 'All types of pens, pencils and writing instruments'],
                'slug'        => 'boligrafs-llapis',
                'sort_order'  => 1,
            ],
            [
                'name'        => ['ca' => 'Paper i Quaderns', 'es' => 'Papel y Cuadernos', 'en' => 'Paper & Notebooks'],
                'description' => ['ca' => 'Paper d\'impressió, quaderns i blocs de notes', 'es' => 'Papel de impresión, cuadernos y blocs de notas', 'en' => 'Printing paper, notebooks and notepads'],
                'slug'        => 'paper-quaderns',
                'sort_order'  => 2,
            ],
            [
                'name'        => ['ca' => 'Arxivadors i Carpetes', 'es' => 'Archivadores y Carpetas', 'en' => 'Binders & Folders'],
                'description' => ['ca' => 'Arxivadors, carpetes i material d\'arxiu', 'es' => 'Archivadores, carpetas y material de archivo', 'en' => 'Binders, folders and filing supplies'],
                'slug'        => 'arxivadors-carpetes',
                'sort_order'  => 3,
            ],
            [
                'name'        => ['ca' => 'Material d\'Oficina', 'es' => 'Material de Oficina', 'en' => 'Office Supplies'],
                'description' => ['ca' => 'Tisores, cinta adhesiva, grapes i més', 'es' => 'Tijeras, cinta adhesiva, grapadoras y más', 'en' => 'Scissors, tape, staplers and more'],
                'slug'        => 'material-oficina',
                'sort_order'  => 4,
            ],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name'        => $cat['name'],
                'description' => $cat['description'],
                'slug'        => $cat['slug'],
                'sort_order'  => $cat['sort_order'],
                'is_active'   => true,
            ]);
        }*/

        $this->call(CategorySeeder::class);

        // ── Sample Products ────────────────────────────────────
        $cat1 = Category::where('slug', 'writing-and-correction')->first();
        $cat2 = Category::where('slug', 'paper-and-notebooks')->first();
        $cat3 = Category::where('slug', 'filing-and-organization')->first();
        $cat4 = Category::where('slug', 'desk-accessories')->first();

        $products = [
            // Pens & Pencils
            [
                'category_id'        => $cat1->id,
                'name'               => ['ca' => 'Bolígraf BIC Cristal Negre', 'es' => 'Bolígrafo BIC Cristal Negro', 'en' => 'BIC Cristal Ballpoint Pen Black'],
                'short_description'  => ['ca' => 'Bolígraf clàssic d\'escriptura suau', 'es' => 'Bolígrafo clásico de escritura suave', 'en' => 'Classic smooth writing ballpoint pen'],
                'description'        => ['ca' => 'El bolígraf BIC Cristal és el més venut del món. Punta de 1.0mm per una escriptura clara i constant.', 'es' => 'El bolígrafo BIC Cristal es el más vendido del mundo. Punta de 1.0mm para una escritura clara y constante.', 'en' => 'The BIC Cristal is the world\'s best-selling ballpoint pen. 1.0mm tip for clear and consistent writing.'],
                'sku'                => 'BIC-CR-NEG-001',
                'slug'               => 'boligraf-bic-cristal-negre',
                'price'              => 0.35,
                'vat_rate'           => 21.00,
                'stock'              => 5000,
                'min_order_quantity' => 50,
                'unit'               => 'unitat',
                'brand'              => 'BIC',
                'is_featured'        => true,
            ],
            [
                'category_id'        => $cat1->id,
                'name'               => ['ca' => 'Llapis Staedtler HB (Caixa 12)', 'es' => 'Lápiz Staedtler HB (Caja 12)', 'en' => 'Staedtler HB Pencil (Box of 12)'],
                'short_description'  => ['ca' => 'Llapis de grau HB per a ús general', 'es' => 'Lápiz de grado HB para uso general', 'en' => 'HB grade pencil for general use'],
                'description'        => ['ca' => 'Llapis Staedtler Noris HB. Fusta de qualitat, mina resistent. Caixa de 12 unitats.', 'es' => 'Lápiz Staedtler Noris HB. Madera de calidad, mina resistente. Caja de 12 unidades.', 'en' => 'Staedtler Noris HB pencil. Quality wood, durable lead. Box of 12 units.'],
                'sku'                => 'STA-HB-12-001',
                'slug'               => 'llapis-staedtler-hb-caixa-12',
                'price'              => 3.20,
                'vat_rate'           => 21.00,
                'stock'              => 1200,
                'min_order_quantity' => 10,
                'unit'               => 'caixa',
                'brand'              => 'Staedtler',
                'is_featured'        => false,
            ],
            // Paper & Notebooks (continued)
            [
                'category_id'        => $cat2->id,
                'name'               => ['ca' => 'Paper A4 80gr (Resma 500 fulls)', 'es' => 'Papel A4 80gr (Resma 500 hojas)', 'en' => 'A4 80gsm Paper (Ream 500 sheets)'],
                'short_description'  => ['ca' => 'Paper blanc per a impressió i fotocòpia', 'es' => 'Papel blanco para impresión y fotocopia', 'en' => 'White paper for printing and photocopying'],
                'description'        => ['ca' => 'Resma de 500 fulls de paper A4 de 80gr/m². Blanc brillant, compatible amb tots els tipus d\'impressores i fotocopiadores.', 'es' => 'Resma de 500 hojas de papel A4 de 80gr/m². Blanco brillante, compatible con todo tipo de impresoras y fotocopiadoras.', 'en' => 'Ream of 500 A4 sheets 80gsm. Bright white, compatible with all types of printers and photocopiers.'],
                'sku'                => 'PAP-A4-80-500',
                'slug'               => 'paper-a4-80gr-resma-500',
                'price'              => 4.90,
                'vat_rate'           => 21.00,
                'stock'              => 3000,
                'min_order_quantity' => 5,
                'unit'               => 'resma',
                'brand'              => 'Navigator',
                'is_featured'        => true,
            ],
            [
                'category_id'        => $cat2->id,
                'name'               => ['ca' => 'Quadern A5 Tapa Dura Ratllat', 'es' => 'Cuaderno A5 Tapa Dura Rayado', 'en' => 'A5 Hardcover Lined Notebook'],
                'short_description'  => ['ca' => 'Quadern professional de tapa dura amb fulls ratllats', 'es' => 'Cuaderno profesional de tapa dura con hojas rayadas', 'en' => 'Professional hardcover notebook with lined pages'],
                'description'        => ['ca' => 'Quadern A5 de tapa dura, 192 pàgines de paper de 90gr ratllat. Ideal per a reunions i anotacions professionals.', 'es' => 'Cuaderno A5 de tapa dura, 192 páginas de papel de 90gr rayado. Ideal para reuniones y anotaciones profesionales.', 'en' => 'A5 hardcover notebook, 192 pages of 90gsm lined paper. Ideal for meetings and professional notes.'],
                'sku'                => 'QUA-A5-TD-192',
                'slug'               => 'quadern-a5-tapa-dura-ratllat',
                'price'              => 6.75,
                'vat_rate'           => 21.00,
                'stock'              => 800,
                'min_order_quantity' => 5,
                'unit'               => 'unitat',
                'brand'              => 'Leuchtturm',
                'is_featured'        => true,
            ],
            // Binders & Folders
            [
                'category_id'        => $cat3->id,
                'name'               => ['ca' => 'Arxivador A4 Llom 8cm Negre', 'es' => 'Archivador A4 Lomo 8cm Negro', 'en' => 'A4 Ring Binder 8cm Spine Black'],
                'short_description'  => ['ca' => 'Arxivador robust per a documents A4', 'es' => 'Archivador robusto para documentos A4', 'en' => 'Robust ring binder for A4 documents'],
                'description'        => ['ca' => 'Arxivador de cartró recobert de PVC, llom de 8cm, anelles de 40mm de diàmetre. Etiqueta de llom inclosa.', 'es' => 'Archivador de cartón recubierto de PVC, lomo de 8cm, anillas de 40mm de diámetro. Etiqueta de lomo incluida.', 'en' => 'PVC-coated cardboard binder, 8cm spine, 40mm diameter rings. Spine label included.'],
                'sku'                => 'ARX-A4-8CM-NEG',
                'slug'               => 'arxivador-a4-llom-8cm-negre',
                'price'              => 2.85,
                'vat_rate'           => 21.00,
                'stock'              => 2000,
                'min_order_quantity' => 10,
                'unit'               => 'unitat',
                'brand'              => 'Leitz',
                'is_featured'        => false,
            ],
            [
                'category_id'        => $cat3->id,
                'name'               => ['ca' => 'Carpeta Pressió A4 Transparent (Pack 10)', 'es' => 'Carpeta Presión A4 Transparente (Pack 10)', 'en' => 'A4 Transparent Clip Folder (Pack of 10)'],
                'short_description'  => ['ca' => 'Carpetes de pressió transparents per a documents A4', 'es' => 'Carpetas de presión transparentes para documentos A4', 'en' => 'Transparent clip folders for A4 documents'],
                'description'        => ['ca' => 'Pack de 10 carpetes de pressió transparents, format A4. Clip metàl·lic resistent. Capacitat per a 30 fulls.', 'es' => 'Pack de 10 carpetas de presión transparentes, formato A4. Clip metálico resistente. Capacidad para 30 hojas.', 'en' => 'Pack of 10 transparent clip folders, A4 format. Resistant metal clip. Capacity for 30 sheets.'],
                'sku'                => 'CAR-A4-PRES-10',
                'slug'               => 'carpeta-pressio-a4-transparent-pack-10',
                'price'              => 5.40,
                'vat_rate'           => 21.00,
                'stock'              => 1500,
                'min_order_quantity' => 5,
                'unit'               => 'pack',
                'brand'              => 'Esselte',
                'is_featured'        => false,
            ],
            // Office Supplies
            [
                'category_id'        => $cat4->id,
                'name'               => ['ca' => 'Grapa Rapid Nº 26/6 (Caixa 1000)', 'es' => 'Grapas Rapid Nº 26/6 (Caja 1000)', 'en' => 'Rapid Staples No. 26/6 (Box 1000)'],
                'short_description'  => ['ca' => 'Grapes estàndard per a grapadores d\'oficina', 'es' => 'Grapas estándar para grapadoras de oficina', 'en' => 'Standard staples for office staplers'],
                'description'        => ['ca' => 'Caixa de 1000 grapes Rapid nº 26/6 d\'acer galvanitzat. Compatible amb la majoria de grapadores estàndard.', 'es' => 'Caja de 1000 grapas Rapid nº 26/6 de acero galvanizado. Compatible con la mayoría de grapadoras estándar.', 'en' => 'Box of 1000 Rapid No. 26/6 galvanised steel staples. Compatible with most standard staplers.'],
                'sku'                => 'GRA-26-6-1000',
                'slug'               => 'grapes-rapid-26-6-caixa-1000',
                'price'              => 1.95,
                'vat_rate'           => 21.00,
                'stock'              => 4000,
                'min_order_quantity' => 20,
                'unit'               => 'caixa',
                'brand'              => 'Rapid',
                'is_featured'        => false,
            ],
            [
                'category_id'        => $cat4->id,
                'name'               => ['ca' => 'Tisores d\'Oficina 21cm Inoxidable', 'es' => 'Tijeras de Oficina 21cm Inoxidable', 'en' => 'Stainless Steel Office Scissors 21cm'],
                'short_description'  => ['ca' => 'Tisores professionals d\'acer inoxidable', 'es' => 'Tijeras profesionales de acero inoxidable', 'en' => 'Professional stainless steel scissors'],
                'description'        => ['ca' => 'Tisores d\'oficina de 21cm amb fulles d\'acer inoxidable i mànec ergonòmic. Tall precís i durador.', 'es' => 'Tijeras de oficina de 21cm con hojas de acero inoxidable y mango ergonómico. Corte preciso y duradero.', 'en' => 'Office scissors 21cm with stainless steel blades and ergonomic handle. Precise and durable cutting.'],
                'sku'                => 'TIS-21CM-INOX',
                'slug'               => 'tisores-oficina-21cm-inoxidable',
                'price'              => 4.50,
                'vat_rate'           => 21.00,
                'stock'              => 600,
                'min_order_quantity' => 5,
                'unit'               => 'unitat',
                'brand'              => 'Maped',
                'is_featured'        => false,
            ],
            [
                'category_id'        => $cat4->id,
                'name'               => ['ca' => 'Cinta Adhesiva Scotch 19mm x 33m (Pack 8)', 'es' => 'Cinta Adhesiva Scotch 19mm x 33m (Pack 8)', 'en' => 'Scotch Adhesive Tape 19mm x 33m (Pack of 8)'],
                'short_description'  => ['ca' => 'Cinta adhesiva transparent d\'ús universal', 'es' => 'Cinta adhesiva transparente de uso universal', 'en' => 'Clear universal adhesive tape'],
                'description'        => ['ca' => 'Pack de 8 rotlles de cinta adhesiva Scotch transparent de 19mm x 33m. Adhesiu acrílic d\'alta qualitat, transparent i resistent.', 'es' => 'Pack de 8 rollos de cinta adhesiva Scotch transparente de 19mm x 33m. Adhesivo acrílico de alta calidad, transparente y resistente.', 'en' => 'Pack of 8 Scotch transparent adhesive tape rolls 19mm x 33m. High quality acrylic adhesive, clear and durable.'],
                'sku'                => 'SCO-19-33-8PK',
                'slug'               => 'cinta-adhesiva-scotch-19mm-33m-pack-8',
                'price'              => 7.20,
                'vat_rate'           => 21.00,
                'stock'              => 900,
                'min_order_quantity' => 5,
                'unit'               => 'pack',
                'brand'              => 'Scotch',
                'is_featured'        => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create(array_merge($productData, ['is_active' => true]));
        }
        $this->call(PrintTemplateSeeder::class);
    }
}
