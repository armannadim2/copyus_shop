<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('shop.invoices.index', compact('invoices'));
    }

    public function download(int $id)
    {
        $invoice = Invoice::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['order.items'])
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('factura-' . $invoice->invoice_number . '.pdf');
    }
}
