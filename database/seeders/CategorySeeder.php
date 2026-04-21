<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Helper to build a translatable JSON field.
     * Supports: English (en), Spanish/Castellano (es), Catalan (ca)
     */
    private function t(string $en, string $es, string $ca): string
    {
        return json_encode([
            'en' => $en,
            'es' => $es,
            'ca' => $ca,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Helper to build a slug from the English name.
     */
    private function slug(string $en): string
    {
        return Str::slug($en);
    }

    public function run(): void
    {
        $now = now();

        // ─────────────────────────────────────────────
        // PARENT CATEGORIES (parent_id = null)
        // ─────────────────────────────────────────────
        $parents = [
            [
                'name'        => $this->t('Writing & Correction', 'Escritura y Corrección', 'Escriptura i Correcció'),
                'description' => $this->t(
                    'Pens, pencils, markers, correctors and all writing instruments.',
                    'Bolígrafos, lápices, rotuladores, correctores y todos los instrumentos de escritura.',
                    'Bolígrafs, llapis, retoladors, correctors i tots els instruments d\'escriptura.'
                ),
                'slug'        => $this->slug('Writing and Correction'),
                'is_active'   => true,
                'sort_order'  => 1,
                'image'       => 'categories/writing-correction.jpg',
                'parent_id'   => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => $this->t('Paper & Notebooks', 'Papel y Cuadernos', 'Paper i Quaderns'),
                'description' => $this->t(
                    'Notebooks, notepads, printer paper, envelopes, planners and diaries.',
                    'Cuadernos, blocs, papel de impresora, sobres, agendas y dietarios.',
                    'Quaderns, blocs, paper d\'impressora, sobres, agendes i dietaris.'
                ),
                'slug'        => $this->slug('Paper and Notebooks'),
                'is_active'   => true,
                'sort_order'  => 2,
                'image'       => 'categories/paper-notebooks.jpg',
                'parent_id'   => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => $this->t('Filing & Organization', 'Archivo y Clasificación', 'Arxiu i Classificació'),
                'description' => $this->t(
                    'Binders, folders, dividers, archive boxes and organizers.',
                    'Archivadores, carpetas, separadores, cajas de archivo y organizadores.',
                    'Arxivadors, carpetes, separadors, caixes d\'arxiu i organitzadors.'
                ),
                'slug'        => $this->slug('Filing and Organization'),
                'is_active'   => true,
                'sort_order'  => 3,
                'image'       => 'categories/filing-organization.jpg',
                'parent_id'   => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => $this->t('Desk Accessories', 'Accesorios de Escritorio', 'Complements d\'Escriptori'),
                'description' => $this->t(
                    'Staplers, tape, scissors, desk organizers, clips and pins.',
                    'Grapadoras, celo, tijeras, organizadores de escritorio, clips y chinchetas.',
                    'Grapadores, zel, tisores, organitzadors de sobretaula, clips i xinxetes.'
                ),
                'slug'        => $this->slug('Desk Accessories'),
                'is_active'   => true,
                'sort_order'  => 4,
                'image'       => 'categories/desk-accessories.jpg',
                'parent_id'   => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => $this->t('School & Education', 'Material Escolar y Educación', 'Material Escolar i Educació'),
                'description' => $this->t(
                    'Backpacks, geometry sets, art materials, educational games and school boards.',
                    'Mochilas, geometría, material de manualidades, juegos educativos y pizarras.',
                    'Motxilles, geometria, materials de manualitats, jocs educatius i pissarres.'
                ),
                'slug'        => $this->slug('School and Education'),
                'is_active'   => true,
                'sort_order'  => 5,
                'image'       => 'categories/school-education.jpg',
                'parent_id'   => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => $this->t('Corporate & Machines', 'Empresa y Maquinaria', 'Empresa i Maquinària'),
                'description' => $this->t(
                    'Calculators, label makers, shredders, laminators, ink cartridges and stamps.',
                    'Calculadoras, etiquetadoras, destructoras, plastificadoras, cartuchos y sellos.',
                    'Calculadores, etiquetadores, destructores, plastificadores, cartutxos i segells.'
                ),
                'slug'        => $this->slug('Corporate and Machines'),
                'is_active'   => true,
                'sort_order'  => 6,
                'image'       => 'categories/corporate-machines.jpg',
                'parent_id'   => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        // Insert parents and collect their IDs keyed by slug
        $parentIds = [];
        foreach ($parents as $parent) {
            $id = DB::table('categories')->insertGetId($parent);
            $parentIds[$parent['slug']] = $id;
        }

        // ─────────────────────────────────────────────
        // SUB-CATEGORIES
        // ─────────────────────────────────────────────
        $subCategories = [

            // ── Writing & Correction ──────────────────
            [
                'parent_id'   => $parentIds['writing-and-correction'],
                'name'        => $this->t('Ballpoint, Gel & Rollerball Pens', 'Bolígrafos Bola, Gel y Roller', 'Bolígrafs Bola, Gel i Roller'),
                'description' => $this->t('All types of pens for everyday writing.', 'Todos los tipos de bolígrafos para escritura diaria.', 'Tots els tipus de bolígrafs per a l\'escriptura diària.'),
                'slug'        => $this->slug('Ballpoint Gel Rollerball Pens'),
                'sort_order'  => 1,
            ],
            [
                'parent_id'   => $parentIds['writing-and-correction'],
                'name'        => $this->t('Pencils & Mechanical Pencils', 'Lápices y Portaminas', 'Llapis i Portamines'),
                'description' => $this->t('Graphite pencils and mechanical pencils for precision.', 'Lápices de grafito y portaminas para precisión.', 'Llapis de grafit i portamines per a precisió.'),
                'slug'        => $this->slug('Pencils and Mechanical Pencils'),
                'sort_order'  => 2,
            ],
            [
                'parent_id'   => $parentIds['writing-and-correction'],
                'name'        => $this->t('Highlighters & Markers', 'Subrayadores y Rotuladores', 'Subratlladors i Retoladors'),
                'description' => $this->t('Fluorescent highlighters and permanent markers.', 'Subrayadores fluorescentes y rotuladores permanentes.', 'Subratlladors fluorescents i retoladors permanents.'),
                'slug'        => $this->slug('Highlighters and Markers'),
                'sort_order'  => 3,
            ],
            [
                'parent_id'   => $parentIds['writing-and-correction'],
                'name'        => $this->t('Fountain Pens & Ink', 'Plumas Estilográficas y Tinta', 'Plomes Estilogràfiques i Tinta'),
                'description' => $this->t('Premium fountain pens and ink refills.', 'Plumas estilográficas premium y recargas de tinta.', 'Plomes estilogràfiques premium i recarregues de tinta.'),
                'slug'        => $this->slug('Fountain Pens and Ink'),
                'sort_order'  => 4,
            ],
            [
                'parent_id'   => $parentIds['writing-and-correction'],
                'name'        => $this->t('Correction Tape & Liquid', 'Correctores Cinta y Líquido', 'Correctors Cinta i Líquid'),
                'description' => $this->t('Correction fluids and tapes for clean fixes.', 'Correctores líquidos y de cinta para correcciones limpias.', 'Correctors líquids i de cinta per a esmenes netes.'),
                'slug'        => $this->slug('Correction Tape and Liquid'),
                'sort_order'  => 5,
            ],
            [
                'parent_id'   => $parentIds['writing-and-correction'],
                'name'        => $this->t('Erasers & Sharpeners', 'Gomas y Sacapuntas', 'Gomes i Maquinetes'),
                'description' => $this->t('Rubber erasers and manual or electric sharpeners.', 'Gomas de borrar y sacapuntas manuales o eléctricos.', 'Gomes d\'esborrar i maquinetes manuals o elèctriques.'),
                'slug'        => $this->slug('Erasers and Sharpeners'),
                'sort_order'  => 6,
            ],

            // ── Paper & Notebooks ─────────────────────
            [
                'parent_id'   => $parentIds['paper-and-notebooks'],
                'name'        => $this->t('Notebooks (Spiral & Hardcover)', 'Cuadernos (Espiral y Tapa Dura)', 'Quaderns (Espiral i Tapa Dura)'),
                'description' => $this->t('Wide variety of notebooks for school and office.', 'Amplia variedad de cuadernos para escuela y oficina.', 'Àmplia varietat de quaderns per a escola i oficina.'),
                'slug'        => $this->slug('Notebooks Spiral Hardcover'),
                'sort_order'  => 1,
            ],
            [
                'parent_id'   => $parentIds['paper-and-notebooks'],
                'name'        => $this->t('Notepads & Sticky Notes', 'Blocs de Notas y Post-it', 'Blocs de Notes i Post-it'),
                'description' => $this->t('Sticky notes, memo pads and tear-off notepads.', 'Post-it, blocs de notas y blocs arrancables.', 'Post-it, blocs de notes i blocs arrencables.'),
                'slug'        => $this->slug('Notepads and Sticky Notes'),
                'sort_order'  => 2,
            ],
            [
                'parent_id'   => $parentIds['paper-and-notebooks'],
                'name'        => $this->t('Printer & Photocopy Paper', 'Papel para Impresora y Fotocopias', 'Paper per a Impressora i Fotocòpies'),
                'description' => $this->t('A4, A3 and letter size paper for all printers.', 'Papel A4, A3 y carta para todo tipo de impresoras.', 'Paper A4, A3 i carta per a tot tipus d\'impressores.'),
                'slug'        => $this->slug('Printer and Photocopy Paper'),
                'sort_order'  => 3,
            ],
            [
                'parent_id'   => $parentIds['paper-and-notebooks'],
                'name'        => $this->t('Envelopes & Mailing', 'Sobres y Envíos', 'Sobres i Enviaments'),
                'description' => $this->t('Standard and padded envelopes for mailing.', 'Sobres estándar y acolchados para envíos.', 'Sobres estàndard i acolxats per a enviaments.'),
                'slug'        => $this->slug('Envelopes and Mailing'),
                'sort_order'  => 4,
            ],
            [
                'parent_id'   => $parentIds['paper-and-notebooks'],
                'name'        => $this->t('Planners & Diaries', 'Agendas y Dietarios', 'Agendes i Dietaris'),
                'description' => $this->t('Daily, weekly and monthly planners and diaries.', 'Agendas y dietarios diarios, semanales y mensuales.', 'Agendes i dietaris diaris, setmanals i mensuals.'),
                'slug'        => $this->slug('Planners and Diaries'),
                'sort_order'  => 5,
            ],
            [
                'parent_id'   => $parentIds['paper-and-notebooks'],
                'name'        => $this->t('Speciality Paper (Photo, Cardboard)', 'Papel Especial (Foto, Cartulina)', 'Paper Especial (Foto, Cartolina)'),
                'description' => $this->t('Photo paper, cardstock and creative paper types.', 'Papel fotográfico, cartulinas y tipos de papel creativo.', 'Paper fotogràfic, cartolina i tipus de paper creatiu.'),
                'slug'        => $this->slug('Speciality Paper Photo Cardboard'),
                'sort_order'  => 6,
            ],

            // ── Filing & Organization ─────────────────
            [
                'parent_id'   => $parentIds['filing-and-organization'],
                'name'        => $this->t('Lever Arch Files & Binders', 'Archivadores y Carpetas de Anillas', 'Arxivadors i Carpetes d\'Anelles'),
                'description' => $this->t('Classic binders and lever arch files for documents.', 'Archivadores clásicos y carpetas de anillas para documentos.', 'Arxivadors clàssics i carpetes d\'anelles per a documents.'),
                'slug'        => $this->slug('Lever Arch Files and Binders'),
                'sort_order'  => 1,
            ],
            [
                'parent_id'   => $parentIds['filing-and-organization'],
                'name'        => $this->t('Plastic Wallets & Folders', 'Fundas de Plástico y Dossieres', 'Fundes de Plàstic i Dossiers'),
                'description' => $this->t('Transparent plastic wallets and presentation folders.', 'Fundas de plástico transparentes y carpetas de presentación.', 'Fundes de plàstic transparents i carpetes de presentació.'),
                'slug'        => $this->slug('Plastic Wallets and Folders'),
                'sort_order'  => 2,
            ],
            [
                'parent_id'   => $parentIds['filing-and-organization'],
                'name'        => $this->t('Dividers & Index Tabs', 'Separadores e Índices', 'Separadors i Índexs'),
                'description' => $this->t('Paper and plastic dividers and index tabs.', 'Separadores de papel y plástico e índices con pestañas.', 'Separadors de paper i plàstic i índexs amb pestanyes.'),
                'slug'        => $this->slug('Dividers and Index Tabs'),
                'sort_order'  => 3,
            ],
            [
                'parent_id'   => $parentIds['filing-and-organization'],
                'name'        => $this->t('Card Holders & Desk Organizers', 'Tarjeteros y Organizadores', 'Tarjeters i Organitzadors'),
                'description' => $this->t('Business card holders and desktop organizers.', 'Tarjeteros de visita y organizadores de escritorio.', 'Tarjeters de visita i organitzadors de sobretaula.'),
                'slug'        => $this->slug('Card Holders and Desk Organizers'),
                'sort_order'  => 4,
            ],
            [
                'parent_id'   => $parentIds['filing-and-organization'],
                'name'        => $this->t('Archive Boxes', 'Cajas de Archivo', 'Caixes d\'Arxiu'),
                'description' => $this->t('Cardboard and plastic archive storage boxes.', 'Cajas de archivo de cartón y plástico.', 'Caixes d\'arxiu de cartró i plàstic.'),
                'slug'        => $this->slug('Archive Boxes'),
                'sort_order'  => 5,
            ],

            // ── Desk Accessories ──────────────────────
            [
                'parent_id'   => $parentIds['desk-accessories'],
                'name'        => $this->t('Staplers & Hole Punches', 'Grapadoras y Perforadoras', 'Grapadores i Perforadors'),
                'description' => $this->t('Manual and electric staplers and hole punchers.', 'Grapadoras y perforadoras manuales y eléctricas.', 'Grapadores i perforadors manuals i elèctrics.'),
                'slug'        => $this->slug('Staplers and Hole Punches'),
                'sort_order'  => 1,
            ],
            [
                'parent_id'   => $parentIds['desk-accessories'],
                'name'        => $this->t('Adhesive Tape & Glue', 'Celo y Pegamentos', 'Zel i Pegaments'),
                'description' => $this->t('Scotch tape, glue sticks and liquid glue.', 'Celo, barras de pegamento y pegamento líquido.', 'Zel, barres de pegament i pegament líquid.'),
                'slug'        => $this->slug('Adhesive Tape and Glue'),
                'sort_order'  => 2,
            ],
            [
                'parent_id'   => $parentIds['desk-accessories'],
                'name'        => $this->t('Scissors & Cutters', 'Tijeras y Cúteres', 'Tisores i Cúters'),
                'description' => $this->t('Office scissors and precision cutters.', 'Tijeras de oficina y cúteres de precisión.', 'Tisores d\'oficina i cúters de precisió.'),
                'slug'        => $this->slug('Scissors and Cutters'),
                'sort_order'  => 3,
            ],
            [
                'parent_id'   => $parentIds['desk-accessories'],
                'name'        => $this->t('Clips, Pins & Rubber Bands', 'Clips, Chinchetas y Gomas Elásticas', 'Clips, Xinxetes i Gomes Elàstiques'),
                'description' => $this->t('Bulldog clips, drawing pins and rubber bands.', 'Clips mariposa, chinchetas y gomas elásticas.', 'Clips papallona, xinxetes i gomes elàstiques.'),
                'slug'        => $this->slug('Clips Pins and Rubber Bands'),
                'sort_order'  => 4,
            ],

            // ── School & Education ────────────────────
            [
                'parent_id'   => $parentIds['school-and-education'],
                'name'        => $this->t('Backpacks & Pencil Cases', 'Mochilas y Estuches', 'Motxilles i Estotjos'),
                'description' => $this->t('School backpacks and pencil cases for students.', 'Mochilas escolares y estuches para estudiantes.', 'Motxilles escolars i estotjos per a estudiants.'),
                'slug'        => $this->slug('Backpacks and Pencil Cases'),
                'sort_order'  => 1,
            ],
            [
                'parent_id'   => $parentIds['school-and-education'],
                'name'        => $this->t('Geometry & Technical Drawing', 'Geometría y Dibujo Técnico', 'Geometria i Dibuix Tècnic'),
                'description' => $this->t('Compasses, set squares, protractors and rulers.', 'Compases, escuadras, transportadores y reglas.', 'Compassos, escaires, transportadors i regles.'),
                'slug'        => $this->slug('Geometry and Technical Drawing'),
                'sort_order'  => 2,
            ],
            [
                'parent_id'   => $parentIds['school-and-education'],
                'name'        => $this->t('Art & Craft Materials', 'Material de Manualidades', 'Material de Manualitats'),
                'description' => $this->t('Paints, brushes, craft paper, modelling clay and more.', 'Pinturas, pinceles, papel de manualidades, plastilina y más.', 'Pintures, pinzells, paper de manualitats, plastilina i més.'),
                'slug'        => $this->slug('Art and Craft Materials'),
                'sort_order'  => 3,
            ],
            [
                'parent_id'   => $parentIds['school-and-education'],
                'name'        => $this->t('Educational Games & Puzzles', 'Juegos Educativos y Puzzles', 'Jocs Educatius i Trencaclosques'),
                'description' => $this->t('Games and puzzles to support learning and creativity.', 'Juegos y puzzles para apoyar el aprendizaje y la creatividad.', 'Jocs i trencaclosques per a donar suport a l\'aprenentatge.'),
                'slug'        => $this->slug('Educational Games and Puzzles'),
                'sort_order'  => 4,
            ],
            [
                'parent_id'   => $parentIds['school-and-education'],
                'name'        => $this->t('Whiteboards & Accessories', 'Pizarras y Accesorios', 'Pissarres i Complements'),
                'description' => $this->t('Whiteboards, chalkboards, markers and erasers.', 'Pizarras blancas y negras, rotuladores y borradores.', 'Pissarres blanques i negres, retoladors i esborres.'),
                'slug'        => $this->slug('Whiteboards and Accessories'),
                'sort_order'  => 5,
            ],

            // ── Corporate & Machines ──────────────────
            [
                'parent_id'   => $parentIds['corporate-and-machines'],
                'name'        => $this->t('Calculators', 'Calculadoras', 'Calculadores'),
                'description' => $this->t('Scientific, financial and basic calculators.', 'Calculadoras científicas, financieras y básicas.', 'Calculadores científiques, financeres i bàsiques.'),
                'slug'        => $this->slug('Calculators'),
                'sort_order'  => 1,
            ],
            [
                'parent_id'   => $parentIds['corporate-and-machines'],
                'name'        => $this->t('Label Makers & Labels', 'Etiquetadoras y Etiquetas', 'Etiquetadores i Etiquetes'),
                'description' => $this->t('Label makers, thermal labels and sticker rolls.', 'Etiquetadoras, etiquetas térmicas y rollos de pegatinas.', 'Etiquetadors, etiquetes tèrmiques i rotlles de pegatines.'),
                'slug'        => $this->slug('Label Makers and Labels'),
                'sort_order'  => 2,
            ],
            [
                'parent_id'   => $parentIds['corporate-and-machines'],
                'name'        => $this->t('Shredders & Laminators', 'Destructoras y Plastificadoras', 'Destructores i Plastificadores'),
                'description' => $this->t('Paper shredders and document laminators.', 'Destructoras de papel y plastificadoras de documentos.', 'Destructores de paper i plastificadores de documents.'),
                'slug'        => $this->slug('Shredders and Laminators'),
                'sort_order'  => 3,
            ],
            [
                'parent_id'   => $parentIds['corporate-and-machines'],
                'name'        => $this->t('Ink & Toner Cartridges', 'Cartuchos de Tinta y Tóner', 'Cartutxos de Tinta i Tòner'),
                'description' => $this->t('Original and compatible ink and toner cartridges.', 'Cartuchos de tinta y tóner originales y compatibles.', 'Cartutxos de tinta i tòner originals i compatibles.'),
                'slug'        => $this->slug('Ink and Toner Cartridges'),
                'sort_order'  => 4,
            ],
            [
                'parent_id'   => $parentIds['corporate-and-machines'],
                'name'        => $this->t('Stamps & Ink Pads', 'Sellos y Almohadillas', 'Segells i Tampons'),
                'description' => $this->t('Self-inking stamps and replacement ink pads.', 'Sellos automáticos y almohadillas de repuesto.', 'Segells automàtics i tampons de recanvi.'),
                'slug'        => $this->slug('Stamps and Ink Pads'),
                'sort_order'  => 5,
            ],
        ];

        // Insert all sub-categories
        foreach ($subCategories as $sub) {
            DB::table('categories')->insert(array_merge($sub, [
                'is_active'  => true,
                'image'      => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }
}
