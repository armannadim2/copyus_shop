<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\PrintJob;
use App\Models\PrintTemplate;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\PromoCode;
use App\Models\Quotation;
use App\Models\QuoteRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminBulkActionController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'module'  => 'required|string',
            'action'  => 'required|string',
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'integer',
            'payload' => 'nullable|array',
        ]);

        $module  = $request->input('module');
        $action  = $request->input('action');
        $ids     = $request->input('ids');
        $payload = $request->input('payload', []);

        $result = match ($module) {
            'users'            => $this->handleUsers($action, $ids, $payload),
            'categories'       => $this->handleCategories($action, $ids, $payload),
            'products'         => $this->handleProducts($action, $ids, $payload),
            'orders'           => $this->handleOrders($action, $ids, $payload),
            'quotations'       => $this->handleQuotations($action, $ids, $payload),
            'quote_requests'   => $this->handleQuoteRequests($action, $ids, $payload),
            'contact_messages' => $this->handleContactMessages($action, $ids, $payload),
            'companies'        => $this->handleCompanies($action, $ids, $payload),
            'print_templates'  => $this->handlePrintTemplates($action, $ids, $payload),
            'print_jobs'       => $this->handlePrintJobs($action, $ids, $payload),
            'promo_codes'      => $this->handlePromoCodes($action, $ids, $payload),
            'reviews'          => $this->handleReviews($action, $ids, $payload),
            'tickets'          => $this->handleTickets($action, $ids, $payload),
            default            => 'invalid_module',
        };

        if ($result === 'invalid_module' || $result === 'invalid_action') {
            return redirect()->back()->with('error', 'Acció no vàlida.');
        }

        return redirect()->back()->with('success', 'Acció en bloc aplicada correctament.');
    }

    // -------------------------------------------------------------------------

    private function handleUsers(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'approve':
                User::whereIn('id', $ids)->update(['role' => 'approved']);
                return 'ok';
            case 'reject':
                User::whereIn('id', $ids)->update(['role' => 'rejected']);
                return 'ok';
            case 'delete':
                User::whereIn('id', $ids)->delete();
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handleCategories(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'activate':
                Category::whereIn('id', $ids)->update(['is_active' => true]);
                return 'ok';
            case 'deactivate':
                Category::whereIn('id', $ids)->update(['is_active' => false]);
                return 'ok';
            case 'delete':
                Category::whereIn('id', $ids)->delete();
                return 'ok';
            case 'assign_parent':
                $parentId = ($payload['parent_id'] ?? '') !== '' ? (int) $payload['parent_id'] : null;
                Category::whereIn('id', $ids)->update(['parent_id' => $parentId]);
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handleProducts(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'activate':
                Product::whereIn('id', $ids)->update(['is_active' => true]);
                return 'ok';
            case 'deactivate':
                Product::whereIn('id', $ids)->update(['is_active' => false]);
                return 'ok';
            case 'delete':
                Product::whereIn('id', $ids)->delete();
                return 'ok';
            case 'featured':
                Product::whereIn('id', $ids)->update(['is_featured' => true]);
                return 'ok';
            case 'unfeatured':
                Product::whereIn('id', $ids)->update(['is_featured' => false]);
                return 'ok';
            case 'assign_category':
                $catId = ($payload['category_id'] ?? '') !== '' ? (int) $payload['category_id'] : null;
                Product::whereIn('id', $ids)->update(['category_id' => $catId]);
                return 'ok';
            case 'assign_tags':
                $tagIds = array_map('intval', $payload['tags'] ?? []);
                foreach (Product::whereIn('id', $ids)->get() as $product) {
                    $product->tags()->syncWithoutDetaching($tagIds);
                }
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handleOrders(string $action, array $ids, array $payload): string
    {
        $valid = ['processing', 'shipped', 'delivered', 'cancelled'];
        if ($action === 'set_status' && in_array($payload['status'] ?? '', $valid)) {
            Order::whereIn('id', $ids)->update(['status' => $payload['status']]);
            return 'ok';
        }
        return 'invalid_action';
    }

    private function handleQuotations(string $action, array $ids, array $payload): string
    {
        $valid = ['reviewing', 'quoted', 'accepted', 'rejected'];
        if ($action === 'set_status' && in_array($payload['status'] ?? '', $valid)) {
            Quotation::whereIn('id', $ids)->update(['status' => $payload['status']]);
            return 'ok';
        }
        if ($action === 'delete') {
            Quotation::whereIn('id', $ids)->delete();
            return 'ok';
        }
        return 'invalid_action';
    }

    private function handleQuoteRequests(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'mark_in_review':
                QuoteRequest::whereIn('id', $ids)->update(['status' => 'in_review']);
                return 'ok';
            case 'close':
                QuoteRequest::whereIn('id', $ids)->update(['status' => 'closed']);
                return 'ok';
            case 'delete':
                QuoteRequest::whereIn('id', $ids)->delete();
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handleContactMessages(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'mark_read':
                ContactMessage::whereIn('id', $ids)->update(['status' => 'read', 'read_at' => Carbon::now()]);
                return 'ok';
            case 'mark_unread':
                ContactMessage::whereIn('id', $ids)->update(['status' => 'new', 'read_at' => null]);
                return 'ok';
            case 'delete':
                ContactMessage::whereIn('id', $ids)->delete();
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handleCompanies(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'activate':
                Company::whereIn('id', $ids)->update(['is_active' => true]);
                return 'ok';
            case 'deactivate':
                Company::whereIn('id', $ids)->update(['is_active' => false]);
                return 'ok';
            case 'delete':
                Company::whereIn('id', $ids)->delete();
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handlePrintTemplates(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'activate':
                PrintTemplate::whereIn('id', $ids)->update(['is_active' => true]);
                return 'ok';
            case 'deactivate':
                PrintTemplate::whereIn('id', $ids)->update(['is_active' => false]);
                return 'ok';
            case 'delete':
                PrintTemplate::whereIn('id', $ids)->delete();
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handlePrintJobs(string $action, array $ids, array $payload): string
    {
        $valid = ['in_production', 'completed', 'cancelled'];
        if ($action === 'set_status' && in_array($payload['status'] ?? '', $valid)) {
            PrintJob::whereIn('id', $ids)->update(['status' => $payload['status']]);
            return 'ok';
        }
        return 'invalid_action';
    }

    private function handlePromoCodes(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'activate':
                PromoCode::whereIn('id', $ids)->update(['is_active' => true]);
                return 'ok';
            case 'deactivate':
                PromoCode::whereIn('id', $ids)->update(['is_active' => false]);
                return 'ok';
            case 'delete':
                PromoCode::whereIn('id', $ids)->delete();
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handleReviews(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'approve':
                ProductReview::whereIn('id', $ids)->update(['is_approved' => true, 'approved_at' => Carbon::now()]);
                return 'ok';
            case 'reject':
                ProductReview::whereIn('id', $ids)->update(['is_approved' => false]);
                return 'ok';
            case 'delete':
                ProductReview::whereIn('id', $ids)->delete();
                return 'ok';
        }
        return 'invalid_action';
    }

    private function handleTickets(string $action, array $ids, array $payload): string
    {
        switch ($action) {
            case 'close':
                Ticket::whereIn('id', $ids)->update(['status' => 'closed', 'resolved_at' => Carbon::now()]);
                return 'ok';
            case 'reopen':
                Ticket::whereIn('id', $ids)->update(['status' => 'open', 'resolved_at' => null]);
                return 'ok';
            case 'delete':
                Ticket::whereIn('id', $ids)->delete();
                return 'ok';
        }
        return 'invalid_action';
    }
}
