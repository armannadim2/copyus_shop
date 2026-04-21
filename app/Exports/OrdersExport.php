<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrdersExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    public function __construct(
        protected string $from,
        protected string $to,
        protected string $status = '',
    ) {}

    public function title(): string
    {
        return "Orders {$this->from} to {$this->to}";
    }

    public function collection()
    {
        return Order::with('user')
            ->whereBetween('placed_at', [
                Carbon::parse($this->from)->startOfDay(),
                Carbon::parse($this->to)->endOfDay(),
            ])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderByDesc('placed_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            '# Order',
            'Date',
            'Client',
            'Company',
            'Email',
            'Status',
            'Subtotal (€)',
            'VAT (€)',
            'Total (€)',
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->placed_at?->format('d/m/Y H:i'),
            $order->user?->name ?? '—',
            $order->user?->company_name ?? '—',
            $order->user?->email ?? '—',
            strtoupper($order->status),
            number_format($order->subtotal, 2),
            number_format($order->vat_amount, 2),
            number_format($order->total, 2),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row — dark background, white bold text
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1A1A2E'],
                ],
            ],
        ];
    }
}
