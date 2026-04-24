<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ($this->templates() as $tpl) {
            DB::table('print_templates')->updateOrInsert(
                ['slug' => $tpl['slug']],
                [
                    'name'                 => json_encode($tpl['name'], JSON_UNESCAPED_UNICODE),
                    'description'          => json_encode($tpl['description'], JSON_UNESCAPED_UNICODE),
                    'icon'                 => $tpl['icon'],
                    'base_price'           => $tpl['base_price'],
                    'vat_rate'             => 21.00,
                    'base_production_days' => $tpl['days'],
                    'sort_order'           => $tpl['sort_order'],
                    'is_active'            => true,
                    'updated_at'           => $now,
                    'created_at'           => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        $slugs = array_column($this->templates(), 'slug');
        DB::table('print_templates')->whereIn('slug', $slugs)->delete();
    }

    /**
     * @return list<array{
     *     slug:string, name:array<string,string>, description:array<string,string>,
     *     icon:string, base_price:float, days:int, sort_order:int
     * }>
     */
    private function templates(): array
    {
        return [
            // ── Marketing & Stationery ──────────────────────────────────────
            [
                'slug' => 'fulletons',
                'name' => ['ca' => 'Fulletons (díptic, tríptic)', 'es' => 'Folletos (díptico, tríptico)', 'en' => 'Brochures (bi-fold, tri-fold)'],
                'description' => [
                    'ca' => 'Fulletons plegats en díptic o tríptic en diversos papers i acabats.',
                    'es' => 'Folletos plegados en díptico o tríptico en varios papeles y acabados.',
                    'en' => 'Bi-fold or tri-fold brochures in various papers and finishes.',
                ],
                'icon' => '📄', 'base_price' => 0.8500, 'days' => 4, 'sort_order' => 100,
            ],
            [
                'slug' => 'posters',
                'name' => ['ca' => 'Pòsters', 'es' => 'Pósteres', 'en' => 'Posters'],
                'description' => [
                    'ca' => 'Pòsters d\'alta qualitat en mides A3, A2, A1 i A0.',
                    'es' => 'Pósteres de alta calidad en tamaños A3, A2, A1 y A0.',
                    'en' => 'High-quality posters in A3, A2, A1 and A0 sizes.',
                ],
                'icon' => '🖼️', 'base_price' => 5.0000, 'days' => 3, 'sort_order' => 110,
            ],
            [
                'slug' => 'paper-carta',
                'name' => ['ca' => 'Paper de carta', 'es' => 'Papel de carta', 'en' => 'Letterheads'],
                'description' => [
                    'ca' => 'Paper de carta corporatiu amb la teva marca, A4 en diferents gramatges.',
                    'es' => 'Papel de carta corporativo con tu marca, A4 en diferentes gramajes.',
                    'en' => 'Corporate letterheads with your branding, A4 in various weights.',
                ],
                'icon' => '✉️', 'base_price' => 0.0500, 'days' => 4, 'sort_order' => 120,
            ],
            [
                'slug' => 'sobres-personalitzats',
                'name' => ['ca' => 'Sobres personalitzats', 'es' => 'Sobres personalizados', 'en' => 'Custom envelopes'],
                'description' => [
                    'ca' => 'Sobres impresos personalitzats en mides DL, C5 i C4.',
                    'es' => 'Sobres impresos personalizados en tamaños DL, C5 y C4.',
                    'en' => 'Custom-printed envelopes in DL, C5 and C4 sizes.',
                ],
                'icon' => '📩', 'base_price' => 0.1500, 'days' => 5, 'sort_order' => 130,
            ],
            [
                'slug' => 'carpetes-presentacio',
                'name' => ['ca' => 'Carpetes de presentació', 'es' => 'Carpetas de presentación', 'en' => 'Presentation folders'],
                'description' => [
                    'ca' => 'Carpetes troquelades amb butxaca per a documents A4.',
                    'es' => 'Carpetas troqueladas con bolsillo para documentos A4.',
                    'en' => 'Die-cut folders with pocket for A4 documents.',
                ],
                'icon' => '📁', 'base_price' => 1.2000, 'days' => 6, 'sort_order' => 140,
            ],

            // ── Large Format ────────────────────────────────────────────────
            [
                'slug' => 'lones-banners',
                'name' => ['ca' => 'Lones (vinil, roll-up, malla)', 'es' => 'Lonas (vinilo, roll-up, malla)', 'en' => 'Banners (vinyl, roll-up, mesh)'],
                'description' => [
                    'ca' => 'Lones publicitàries en vinil, roll-up o malla per interior i exterior.',
                    'es' => 'Lonas publicitarias en vinilo, roll-up o malla para interior y exterior.',
                    'en' => 'Advertising banners in vinyl, roll-up or mesh for indoor and outdoor use.',
                ],
                'icon' => '🎌', 'base_price' => 25.0000, 'days' => 4, 'sort_order' => 150,
            ],
            [
                'slug' => 'adhesius-etiquetes',
                'name' => ['ca' => 'Adhesius i etiquetes', 'es' => 'Pegatinas y etiquetas', 'en' => 'Stickers & labels'],
                'description' => [
                    'ca' => 'Adhesius i etiquetes troquelades en formes i acabats personalitzats.',
                    'es' => 'Pegatinas y etiquetas troqueladas en formas y acabados personalizados.',
                    'en' => 'Die-cut stickers and labels in custom shapes and finishes.',
                ],
                'icon' => '🏷️', 'base_price' => 0.0500, 'days' => 3, 'sort_order' => 160,
            ],

            // ── Cards & Mailings ────────────────────────────────────────────
            [
                'slug' => 'postals',
                'name' => ['ca' => 'Postals', 'es' => 'Postales', 'en' => 'Postcards'],
                'description' => [
                    'ca' => 'Postals impreses a doble cara en cartolina mat o brillant.',
                    'es' => 'Postales impresas a doble cara en cartulina mate o brillante.',
                    'en' => 'Double-sided postcards in matte or gloss card stock.',
                ],
                'icon' => '📮', 'base_price' => 0.1000, 'days' => 3, 'sort_order' => 170,
            ],

            // ── Editorial ───────────────────────────────────────────────────
            [
                'slug' => 'catalegs-revistes',
                'name' => ['ca' => 'Catàlegs i revistes', 'es' => 'Catálogos y revistas', 'en' => 'Catalogs & magazines'],
                'description' => [
                    'ca' => 'Catàlegs i revistes enquadernats amb cosit, grapat o llom encolat.',
                    'es' => 'Catálogos y revistas encuadernados con cosido, grapado o lomo encolado.',
                    'en' => 'Catalogs and magazines bound with stitching, stapling or perfect binding.',
                ],
                'icon' => '📚', 'base_price' => 4.5000, 'days' => 7, 'sort_order' => 180,
            ],
            [
                'slug' => 'cartes-menu',
                'name' => ['ca' => 'Cartes i menús', 'es' => 'Cartas y menús', 'en' => 'Menus'],
                'description' => [
                    'ca' => 'Cartes i menús per restaurants amb laminació opcional resistent a l\'aigua.',
                    'es' => 'Cartas y menús para restaurantes con laminado opcional resistente al agua.',
                    'en' => 'Restaurant menus with optional water-resistant lamination.',
                ],
                'icon' => '🍽️', 'base_price' => 1.5000, 'days' => 5, 'sort_order' => 190,
            ],

            // ── Apparel ─────────────────────────────────────────────────────
            [
                'slug' => 'samarretes-dessuadores',
                'name' => ['ca' => 'Samarretes i dessuadores', 'es' => 'Camisetas y sudaderas', 'en' => 'T-shirts & hoodies'],
                'description' => [
                    'ca' => 'Samarretes i dessuadores impreses amb serigrafia o DTF.',
                    'es' => 'Camisetas y sudaderas impresas con serigrafía o DTF.',
                    'en' => 'T-shirts and hoodies printed with screen printing or DTF.',
                ],
                'icon' => '👕', 'base_price' => 8.0000, 'days' => 7, 'sort_order' => 200,
            ],
            [
                'slug' => 'gorres-barrets',
                'name' => ['ca' => 'Gorres i barrets', 'es' => 'Gorras y sombreros', 'en' => 'Caps & hats'],
                'description' => [
                    'ca' => 'Gorres personalitzades amb brodat o impressió.',
                    'es' => 'Gorras personalizadas con bordado o impresión.',
                    'en' => 'Custom caps with embroidery or print.',
                ],
                'icon' => '🧢', 'base_price' => 6.0000, 'days' => 7, 'sort_order' => 210,
            ],
            [
                'slug' => 'bosses-tote',
                'name' => ['ca' => 'Bosses tote', 'es' => 'Bolsas tote', 'en' => 'Tote bags'],
                'description' => [
                    'ca' => 'Bosses tote de cotó orgànic amb impressió personalitzada.',
                    'es' => 'Bolsas tote de algodón orgánico con impresión personalizada.',
                    'en' => 'Organic cotton tote bags with custom printing.',
                ],
                'icon' => '🛍️', 'base_price' => 3.5000, 'days' => 6, 'sort_order' => 220,
            ],

            // ── Promotional ─────────────────────────────────────────────────
            [
                'slug' => 'tasses',
                'name' => ['ca' => 'Tasses', 'es' => 'Tazas', 'en' => 'Mugs'],
                'description' => [
                    'ca' => 'Tasses de ceràmica personalitzades amb impressió full color.',
                    'es' => 'Tazas de cerámica personalizadas con impresión a todo color.',
                    'en' => 'Custom ceramic mugs with full-colour printing.',
                ],
                'icon' => '☕', 'base_price' => 5.0000, 'days' => 5, 'sort_order' => 230,
            ],
            [
                'slug' => 'fundes-mobil',
                'name' => ['ca' => 'Fundes de mòbil', 'es' => 'Fundas de móvil', 'en' => 'Phone cases'],
                'description' => [
                    'ca' => 'Fundes personalitzades per als principals models de telèfons.',
                    'es' => 'Fundas personalizadas para los principales modelos de teléfonos.',
                    'en' => 'Custom cases for major phone models.',
                ],
                'icon' => '📱', 'base_price' => 9.5000, 'days' => 6, 'sort_order' => 240,
            ],
            [
                'slug' => 'alfombretes',
                'name' => ['ca' => 'Alfombretes (mousepads)', 'es' => 'Alfombrillas (mousepads)', 'en' => 'Mousepads'],
                'description' => [
                    'ca' => 'Alfombretes per ratolí amb impressió personalitzada.',
                    'es' => 'Alfombrillas para ratón con impresión personalizada.',
                    'en' => 'Mousepads with custom printing.',
                ],
                'icon' => '🖱️', 'base_price' => 4.0000, 'days' => 5, 'sort_order' => 250,
            ],
            [
                'slug' => 'clauers',
                'name' => ['ca' => 'Clauers', 'es' => 'Llaveros', 'en' => 'Keychains'],
                'description' => [
                    'ca' => 'Clauers personalitzats en metacrilat, metall o pell sintètica.',
                    'es' => 'Llaveros personalizados en metacrilato, metal o piel sintética.',
                    'en' => 'Custom keychains in acrylic, metal or synthetic leather.',
                ],
                'icon' => '🔑', 'base_price' => 1.5000, 'days' => 5, 'sort_order' => 260,
            ],

            // ── Stationery & Office ─────────────────────────────────────────
            [
                'slug' => 'llibretes-agendes',
                'name' => ['ca' => 'Llibretes i agendes', 'es' => 'Libretas y agendas', 'en' => 'Notebooks & diaries'],
                'description' => [
                    'ca' => 'Llibretes i agendes amb tapa dura o tova personalitzades.',
                    'es' => 'Libretas y agendas con tapa dura o blanda personalizadas.',
                    'en' => 'Custom notebooks and diaries with hard or soft covers.',
                ],
                'icon' => '📓', 'base_price' => 6.0000, 'days' => 8, 'sort_order' => 270,
            ],
            [
                'slug' => 'calendaris',
                'name' => ['ca' => 'Calendaris (paret i sobretaula)', 'es' => 'Calendarios (pared y sobremesa)', 'en' => 'Calendars (wall & desk)'],
                'description' => [
                    'ca' => 'Calendaris de paret i sobretaula amb fotografies o disseny corporatiu.',
                    'es' => 'Calendarios de pared y sobremesa con fotografías o diseño corporativo.',
                    'en' => 'Wall and desk calendars with photos or corporate design.',
                ],
                'icon' => '📅', 'base_price' => 4.5000, 'days' => 6, 'sort_order' => 280,
            ],
            [
                'slug' => 'credencials',
                'name' => ['ca' => 'Credencials i targetes ID', 'es' => 'Credenciales y tarjetas ID', 'en' => 'ID cards & badges'],
                'description' => [
                    'ca' => 'Targetes d\'identificació en PVC i credencials amb cordó.',
                    'es' => 'Tarjetas de identificación en PVC y credenciales con cordón.',
                    'en' => 'PVC ID cards and lanyard badges.',
                ],
                'icon' => '🆔', 'base_price' => 0.8000, 'days' => 5, 'sort_order' => 290,
            ],
            [
                'slug' => 'talonaris-factures',
                'name' => ['ca' => 'Talonaris de factures', 'es' => 'Talonarios de facturas', 'en' => 'Invoice books'],
                'description' => [
                    'ca' => 'Talonaris numerats en autocopiatiu (NCR) per a factures i albarans.',
                    'es' => 'Talonarios numerados en autocopiativo (NCR) para facturas y albaranes.',
                    'en' => 'Numbered carbonless (NCR) invoice and delivery-note books.',
                ],
                'icon' => '🧾', 'base_price' => 8.0000, 'days' => 7, 'sort_order' => 300,
            ],

            // ── Events & Personal ───────────────────────────────────────────
            [
                'slug' => 'invitacions-casament',
                'name' => ['ca' => 'Invitacions de casament', 'es' => 'Invitaciones de boda', 'en' => 'Wedding invitations'],
                'description' => [
                    'ca' => 'Invitacions de casament amb papers premium i acabats especials.',
                    'es' => 'Invitaciones de boda con papeles premium y acabados especiales.',
                    'en' => 'Wedding invitations with premium papers and special finishes.',
                ],
                'icon' => '💍', 'base_price' => 1.5000, 'days' => 8, 'sort_order' => 310,
            ],
            [
                'slug' => 'felicitacions-aniversari',
                'name' => ['ca' => 'Felicitacions d\'aniversari', 'es' => 'Felicitaciones de cumpleaños', 'en' => 'Birthday cards'],
                'description' => [
                    'ca' => 'Targetes d\'aniversari personalitzades amb sobre.',
                    'es' => 'Tarjetas de cumpleaños personalizadas con sobre.',
                    'en' => 'Custom birthday cards with envelope.',
                ],
                'icon' => '🎂', 'base_price' => 0.8000, 'days' => 4, 'sort_order' => 320,
            ],
            [
                'slug' => 'entrades-esdeveniments',
                'name' => ['ca' => 'Entrades per esdeveniments', 'es' => 'Entradas para eventos', 'en' => 'Event tickets'],
                'description' => [
                    'ca' => 'Entrades numerades amb perforació i mesures de seguretat opcionals.',
                    'es' => 'Entradas numeradas con perforación y medidas de seguridad opcionales.',
                    'en' => 'Numbered tickets with perforation and optional security features.',
                ],
                'icon' => '🎫', 'base_price' => 0.1000, 'days' => 5, 'sort_order' => 330,
            ],
            [
                'slug' => 'certificats-diplomes',
                'name' => ['ca' => 'Certificats i diplomes', 'es' => 'Certificados y diplomas', 'en' => 'Certificates & diplomas'],
                'description' => [
                    'ca' => 'Certificats i diplomes en paper de qualitat amb acabats premium.',
                    'es' => 'Certificados y diplomas en papel de calidad con acabados premium.',
                    'en' => 'Certificates and diplomas on quality paper with premium finishes.',
                ],
                'icon' => '🎓', 'base_price' => 0.5000, 'days' => 4, 'sort_order' => 340,
            ],
            [
                'slug' => 'fotografies-albums',
                'name' => ['ca' => 'Fotografies i àlbums', 'es' => 'Fotografías y álbumes', 'en' => 'Photo prints & albums'],
                'description' => [
                    'ca' => 'Impressions fotogràfiques i àlbums enquadernats amb tapa dura.',
                    'es' => 'Impresiones fotográficas y álbumes encuadernados con tapa dura.',
                    'en' => 'Photo prints and hardcover-bound photo albums.',
                ],
                'icon' => '📸', 'base_price' => 2.5000, 'days' => 7, 'sort_order' => 350,
            ],

            // ── Special Finishes ────────────────────────────────────────────
            [
                'slug' => 'gofrat-relleu',
                'name' => ['ca' => 'Gofrat i relleu', 'es' => 'Embossing y debossing', 'en' => 'Embossing & debossing'],
                'description' => [
                    'ca' => 'Acabat de gofrat (relleu en alt) o relleu (en baix) per a un toc tàctil.',
                    'es' => 'Acabado de gofrado (relieve alto) o debossing (relieve bajo) para un toque táctil.',
                    'en' => 'Embossing (raised) or debossing (recessed) finish for a tactile touch.',
                ],
                'icon' => '✨', 'base_price' => 12.0000, 'days' => 8, 'sort_order' => 360,
            ],
            [
                'slug' => 'estampacio-foil',
                'name' => ['ca' => 'Estampació amb foil', 'es' => 'Estampación con foil', 'en' => 'Foil stamping'],
                'description' => [
                    'ca' => 'Estampació amb làmina metàl·lica (or, plata, coure) per acabats luxosos.',
                    'es' => 'Estampación con lámina metálica (oro, plata, cobre) para acabados lujosos.',
                    'en' => 'Metallic foil stamping (gold, silver, copper) for luxurious finishes.',
                ],
                'icon' => '🥇', 'base_price' => 15.0000, 'days' => 8, 'sort_order' => 370,
            ],
            [
                'slug' => 'vernis-uv',
                'name' => ['ca' => 'Vernís UV localitzat', 'es' => 'Barniz UV localizado', 'en' => 'UV spot printing'],
                'description' => [
                    'ca' => 'Vernís UV brillant aplicat en zones específiques per ressaltar elements.',
                    'es' => 'Barniz UV brillante aplicado en zonas específicas para resaltar elementos.',
                    'en' => 'Glossy UV varnish applied to specific areas to highlight elements.',
                ],
                'icon' => '💎', 'base_price' => 8.0000, 'days' => 7, 'sort_order' => 380,
            ],

            // ── Wall & Decor ────────────────────────────────────────────────
            [
                'slug' => 'impressio-metacrilat',
                'name' => ['ca' => 'Impressió en metacrilat', 'es' => 'Impresión en metacrilato', 'en' => 'Acrylic prints'],
                'description' => [
                    'ca' => 'Impressions sobre metacrilat per a un acabat modern i lluminós.',
                    'es' => 'Impresiones sobre metacrilato para un acabado moderno y luminoso.',
                    'en' => 'Prints on acrylic for a modern, glossy finish.',
                ],
                'icon' => '🪟', 'base_price' => 18.0000, 'days' => 8, 'sort_order' => 390,
            ],
            [
                'slug' => 'impressio-canvas',
                'name' => ['ca' => 'Impressió en canvas', 'es' => 'Impresión en canvas', 'en' => 'Canvas prints'],
                'description' => [
                    'ca' => 'Impressions en tela canvas muntades sobre bastidor de fusta.',
                    'es' => 'Impresiones en tela canvas montadas sobre bastidor de madera.',
                    'en' => 'Canvas prints mounted on wooden stretcher frame.',
                ],
                'icon' => '🖼️', 'base_price' => 14.0000, 'days' => 7, 'sort_order' => 400,
            ],
        ];
    }
};
