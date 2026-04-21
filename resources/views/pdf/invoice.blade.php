<!DOCTYPE html>
<html lang="{{ $invoice->locale ?? 'ca' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Factura #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #ffffff;
            padding: 40px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 3px solid #E63946;
        }

        .company-logo {
            font-size: 28px;
            font-weight: 900;
            color: #E63946;
            letter-spacing: -1px;
        }

        .company-details {
            font-size: 10px;
            color: #6b7280;
            text-align: right;
            line-height: 1.6;
        }

        .company-details strong {
            color: #1a1a1a;
            font-size: 11px;
        }

        /* ── Invoice Title Block ── */
        .invoice-title-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 32px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: 900;
            color: #1a1a1a;
            letter-spacing: -1px;
        }

        .invoice-number {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }

        .invoice-number span {
            color: #E63946;
            font-weight: 700;
        }

        .invoice-meta {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            line-height: 1.8;
        }

        .invoice-meta strong {
            color: #1a1a1a;
        }

        .badge-issued {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-paid {
            background: #dcfce7;
            color: #16a34a;
        }

        /* ── Addresses ── */
        .addresses {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
        }

        .address-block {
            flex: 1;
            background: #f9fafb;
            border-radius: 10px;
            padding: 16px 20px;
        }

        .address-block h3 {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin-bottom: 10px;
        }

        .address-block p {
            font-size: 10px;
            color: #374151;
            line-height: 1.7;
        }

        .address-block .company-name-billing {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .items-table thead tr {
            background: #1a1a1a;
            color: #ffffff;
        }

        .items-table thead th {
            padding: 10px 14px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: left;
        }

        .items-table thead th.text-right {
            text-align: right;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .items-table tbody td {
            padding: 10px 14px;
            font-size: 10px;
            color: #374151;
            vertical-align: top;
        }

        .items-table tbody td.text-right {
            text-align: right;
        }

        .items-table tbody td.product-name {
            font-weight: 600;
            color: #1a1a1a;
        }

        .items-table tbody td.product-sku {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .print-badge {
            display: inline-block;
            background: #fff3e0;
            color: #e65100;
            font-size: 8px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 10px;
            margin-top: 3px;
            letter-spacing: 0.3px;
        }

        .config-list {
            font-size: 9px;
            color: #6b7280;
            margin-top: 4px;
            line-height: 1.6;
        }

        .config-list span {
            color: #374151;
        }

        /* ── Totals ── */
        .totals-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 32px;
        }

        .totals-table {
            width: 280px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 10px;
            color: #6b7280;
            border-bottom: 1px solid #f3f4f6;
        }

        .totals-row.total-final {
            padding: 12px 16px;
            background: #E63946;
            border-radius: 8px;
            margin-top: 8px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            border-bottom: none;
        }

        .totals-row.total-final span {
            color: #ffffff;
        }

        /* ── Notes ── */
        .notes-section {
            background: #f9fafb;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 32px;
        }

        .notes-section h3 {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .notes-section p {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* ── Footer ── */
        .footer {
            border-top: 2px solid #f3f4f6;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-brand {
            font-size: 16px;
            font-weight: 900;
            color: #E63946;
        }

        .footer-info {
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
            line-height: 1.6;
        }

        .footer-page {
            font-size: 9px;
            color: #9ca3af;
            text-align: right;
        }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <div class="header">
        <div>
            <div class="company-logo">COPYUS</div>
            <p style="font-size:10px; color:#9ca3af; margin-top:4px;">
                Material d'oficina i papereria
            </p>
        </div>
        <div class="company-details">
            <strong>{{ $invoice->company_details['name'] ?? 'Copyus S.L.' }}</strong><br>
            {{ $invoice->company_details['address'] ?? '' }}<br>
            CIF: {{ $invoice->company_details['cif'] ?? '' }}<br>
            {{ $invoice->company_details['email'] ?? '' }}<br>
            {{ $invoice->company_details['phone'] ?? '' }}
        </div>
    </div>

    {{-- ── Invoice Title Block ── --}}
    <div class="invoice-title-block">
        <div>
            <div class="invoice-title">FACTURA</div>
            <div class="invoice-number">
                Nº <span>#{{ $invoice->invoice_number }}</span>
            </div>
        </div>
        <div class="invoice-meta">
            <strong>Data d'emissió:</strong>
            {{ \Carbon\Carbon::parse($invoice->issued_at)->format('d/m/Y') }}<br>
            <strong>Venciment:</strong>
            {{ \Carbon\Carbon::parse($invoice->due_at)->format('d/m/Y') }}<br>
            <strong>Comanda:</strong>
            #{{ $invoice->order->order_number }}<br>
            @php
                $badgeClass = $invoice->status === 'paid' ? 'badge-issued badge-paid' : 'badge-issued';
            @endphp
            <span class="{{ $badgeClass }}">{{ ucfirst($invoice->status) }}</span>
        </div>
    </div>

    {{-- ── Addresses ── --}}
    <div class="addresses">

        {{-- Seller --}}
        <div class="address-block">
            <h3>Venedor</h3>
            <p class="company-name-billing">
                {{ $invoice->company_details['name'] ?? 'Copyus S.L.' }}
            </p>
            <p>
                CIF: {{ $invoice->company_details['cif'] ?? '' }}<br>
                {{ $invoice->company_details['address'] ?? '' }}<br>
                {{ $invoice->company_details['email'] ?? '' }}<br>
                {{ $invoice->company_details['phone'] ?? '' }}
            </p>
        </div>

        {{-- Buyer --}}
        <div class="address-block">
            <h3>Client</h3>
            <p class="company-name-billing">
                {{ $invoice->billing_address['company_name'] ?? '—' }}
            </p>
            <p>
                CIF: {{ $invoice->billing_address['cif'] ?? '—' }}<br>
                {{ $invoice->billing_address['address'] ?? '' }}<br>
                {{ $invoice->billing_address['city'] ?? '' }},
                {{ $invoice->billing_address['postal_code'] ?? '' }}<br>
                {{ $invoice->billing_address['country'] ?? '' }}
            </p>
        </div>

    </div>

    {{-- ── Items Table ── --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:40%">Producte</th>
                <th>Referència</th>
                <th class="text-right">Quantitat</th>
                <th class="text-right">Preu unit.</th>
                <th class="text-right">IVA %</th>
                <th class="text-right">IVA €</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->items as $item)
                @php
                    $isPrintJob     = ($item->product_snapshot['type'] ?? '') === 'print_job';
                    $configLabels   = $isPrintJob ? ($item->product_snapshot['configuration_labels'] ?? []) : [];
                    $productionDays = $isPrintJob ? ($item->product_snapshot['production_days'] ?? null) : null;
                @endphp
                <tr>
                    <td>
                        <div class="product-name">
                            {{ $item->product_snapshot['name']
                                ?? $item->product?->getTranslation('name', $invoice->locale ?? 'ca')
                                ?? '—' }}
                        </div>
                        @if($isPrintJob)
                            <div class="print-badge">Impressió a mida</div>
                        @endif
                        @if(!empty($configLabels))
                            <div class="config-list">
                                @foreach($configLabels as $label => $value)
                                    {{ $label }}: <span>{{ $value }}</span>@if(!$loop->last) · @endif
                                @endforeach
                            </div>
                        @endif
                        @if($productionDays)
                            <div class="config-list">
                                Producció estimada: <span>{{ $productionDays }} dies</span>
                            </div>
                        @endif
                        @if(!$isPrintJob)
                            <div class="product-sku">
                                {{ $item->product_snapshot['sku'] ?? '' }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $item->product_snapshot['sku'] ?? '—' }}</td>
                    <td class="text-right">
                        {{ number_format($item->quantity, 0, ',', '.') }}
                        {{ $item->product_snapshot['unit'] ?? '' }}
                    </td>
                    <td class="text-right">
                        {{ number_format($item->unit_price, 4, ',', '.') }} €
                    </td>
                    <td class="text-right">
                        {{ number_format($item->vat_rate, 0) }}%
                    </td>
                    <td class="text-right">
                        {{ number_format($item->vat_amount, 2, ',', '.') }} €
                    </td>
                    <td class="text-right">
                        <strong>{{ number_format($item->total, 2, ',', '.') }} €</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Totals ── --}}
    @php
        // Group VAT by rate for accurate breakdown
        $vatByRate = $invoice->order->items
            ->groupBy(fn($i) => number_format((float) $i->vat_rate, 0))
            ->map(fn($group, $rate) => [
                'rate'   => (float) $rate,
                'amount' => $group->sum(fn($i) => (float) $i->vat_amount),
            ])
            ->sortBy('rate')
            ->values();

        $promoDiscount = (float) ($invoice->order->discount_amount ?? 0);
    @endphp
    <div class="totals-wrapper">
        <div class="totals-table">
            <div class="totals-row">
                <span>Base imposable</span>
                <span>{{ number_format($invoice->subtotal, 2, ',', '.') }} €</span>
            </div>
            @foreach($vatByRate as $vatRow)
                <div class="totals-row">
                    <span>IVA ({{ number_format($vatRow['rate'], 0) }}%)</span>
                    <span>{{ number_format($vatRow['amount'], 2, ',', '.') }} €</span>
                </div>
            @endforeach
            @if($promoDiscount > 0)
                <div class="totals-row">
                    <span>Descompte ({{ $invoice->order->promo_code }})</span>
                    <span>−{{ number_format($promoDiscount, 2, ',', '.') }} €</span>
                </div>
            @endif
            <div class="totals-row total-final">
                <span>TOTAL</span>
                <span>{{ number_format($invoice->total, 2, ',', '.') }} €</span>
            </div>
        </div>
    </div>

    {{-- ── Notes ── --}}
    @if($invoice->order->notes)
        <div class="notes-section">
            <h3>Notes de la comanda</h3>
            <p>{{ $invoice->order->notes }}</p>
        </div>
    @endif

    {{-- ── Legal Notes ── --}}
    <div class="notes-section">
        <h3>Informació legal</h3>
        <p>
            Factura emesa d'acord amb la Llei 37/1992 de l'IVA.
            El pagament s'ha de realitzar en el termini indicat.
            En cas de discrepàncies, preguem contactar amb
            {{ $invoice->company_details['email'] ?? 'info@copyus.es' }}.
        </p>
    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="footer-brand">COPYUS</div>
        <div class="footer-info">
            Gràcies per la teva confiança<br>
            {{ $invoice->company_details['email'] ?? 'info@copyus.es' }}
            · {{ $invoice->company_details['phone'] ?? '' }}
        </div>
        <div class="footer-page">
            Pàgina 1 de 1<br>
            Generat el {{ now()->format('d/m/Y') }}
        </div>
    </div>

</body>
</html>
