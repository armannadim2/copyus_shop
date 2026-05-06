<?php

$dir = __DIR__ . '/resources/views/admin';

$files = array_merge(
    glob($dir . '/*/index.blade.php'),
    glob($dir . '/*/*/index.blade.php')
);

$entityMap = [
    'contact_messages' => 'ContactMessage',
    'orders' => 'Order',
    'quotations' => 'Quotation',
    'companies' => 'Company',
    'print/jobs' => 'PrintJob',
    'print/templates' => 'PrintTemplate',
    'quote_requests' => 'QuoteRequest',
    'reviews' => 'ProductReview',
    'users' => 'User',
    'products' => 'Product',
    'tickets' => 'Ticket',
    'promo_codes' => 'PromoCode',
    'categories' => 'Category',
];

foreach ($files as $file) {
    if (strpos($file, 'reports/index.blade.php') !== false) continue;
    
    $content = file_get_contents($file);
    if (strpos($content, 'admin.bulk-action') !== false) {
        echo "Skipping (already modified): $file\n";
        continue;
    }

    $relativePath = str_replace([$dir . '/', '\\'], ['', '/'], $file);
    $folder = dirname($relativePath);
    $entityType = $entityMap[$folder] ?? null;

    if (!$entityType) {
        echo "Unknown entity for folder $folder: $file\n";
        continue;
    }

    // Find loop variable
    preg_match('/@(foreach|forelse)\s*\(\$[^ ]+\s+as\s+\$([a-zA-Z0-9_]+)\)/', $content, $matches);
    $loopVar = $matches[2] ?? 'item';

    // Replace table wrapper with bulk action wrapper
    $tableWrapperPattern = '/(<div class="bg-white rounded-2xl shadow-sm overflow-hidden">)/';
    $bulkActionHeader = <<<HTML
<form method="POST" action="{{ route('admin.bulk-action') }}" x-data="{ selected: [], selectAll: false }">
    @csrf
    <input type="hidden" name="entity_type" value="$entityType">
    
    <div class="flex items-center gap-3 mb-4" x-show="selected.length > 0" x-cloak>
        <span class="font-outfit text-sm text-gray-600">
            <span x-text="selected.length"></span> seleccionats
        </span>
        <select name="action" class="border border-gray-200 rounded-xl px-4 py-2 font-outfit text-sm focus:outline-none focus:ring-2 focus:ring-primary" required>
            <option value="">Selecciona acció...</option>
            <option value="delete">Eliminar</option>
        </select>
        <button type="submit" class="bg-secondary text-white font-outfit text-sm px-5 py-2 rounded-xl hover:bg-secondary/90 transition-colors" onclick="return confirm('N\'estàs segur de voler aplicar aquesta acció en bloc?')">
            Aplicar
        </button>
    </div>

    $1
HTML;

    $content = preg_replace($tableWrapperPattern, $bulkActionHeader, $content, 1, $countWrapper);
    
    if ($countWrapper == 0) {
        echo "Could not find table wrapper in $file\n";
        continue;
    }

    // We also need to close the form at the end of the table wrapper.
    // The table wrapper ends right before `<div class="mt-4">{{` usually.
    // So let's insert `</form>` before that or at the end of the content section.
    // A safer way is to replace `</table>\s*</div>` with `</table>\n    </div>\n</form>`.
    $content = preg_replace('/(<\/table>\s*<\/div>)/', "$1\n</form>", $content, 1, $countClose);

    if ($countClose == 0) {
         echo "Could not find table close in $file\n";
    }

    // Add table header checkbox
    $thPattern = '/(<thead[^>]*>\s*<tr>)/';
    $thCheckbox = <<<HTML
$1
                    <th class="px-6 py-3 w-12">
                        <input type="checkbox" x-model="selectAll" x-on:change="selected = selectAll ? Array.from(\$el.closest('table').querySelectorAll('tbody input[type=checkbox]')).map(cb => cb.value) : []" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                    </th>
HTML;
    $content = preg_replace($thPattern, $thCheckbox, $content, 1, $countTh);
    
    // Add row checkbox
    // Find first `<tr ...>` inside tbody or right after `@foreach`
    // Actually, look for `class="hover:bg-gray-50 transition-colors">` or similar
    $trPattern = '/(<tr[^>]*>\s*<td[^>]*>)/';
    // But we might match header tr, so let's match `@forelse(...)` or `@foreach(...)` and then the next `<tr...>` and insert `<td>...</td>` inside it.
    
    $loopPattern = '/(@(?:foreach|forelse)\s*\([^)]+\)(?:\s*@php[\s\S]*?@endphp)?\s*<tr[^>]*>)/';
    $tdCheckbox = <<<HTML
$1
                        <td class="px-6 py-4">
                            <input type="checkbox" name="ids[]" value="{{ \$$loopVar->id }}" x-model="selected" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                        </td>
HTML;
    $content = preg_replace($loopPattern, $tdCheckbox, $content, 1, $countTr);

    if ($countTr == 0) {
        echo "Could not find row loop in $file\n";
    }

    // Special case for `@empty` where there's a `<td colspan="X">`. We should increment colspan by 1.
    $emptyPattern = '/(@empty\s*<tr[^>]*>\s*<td\s+colspan=")(\d+)(")/';
    $content = preg_replace_callback($emptyPattern, function($matches) {
        return $matches[1] . ((int)$matches[2] + 1) . $matches[3];
    }, $content);

    file_put_contents($file, $content);
    echo "Modified: $file\n";
}
