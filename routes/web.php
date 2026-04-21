<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\QuotationController;
use App\Http\Controllers\Shop\InvoiceController;
use App\Http\Controllers\Shop\PromoCodeController;
use App\Http\Controllers\Shop\WishlistController;
use App\Http\Controllers\Shop\SavedAddressController;
use App\Http\Controllers\Shop\ReviewController;
use App\Http\Controllers\Shop\SearchController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminQuotationController;
use App\Http\Controllers\Admin\AdminAiController;
use App\Http\Controllers\Admin\AdminPromoCodeController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminPrintTemplateController;
use App\Http\Controllers\Admin\AdminPrintJobController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Shop\PrintJobController;
use App\Http\Controllers\Shop\CompanyController;
use App\Http\Controllers\Shop\SavedPrintConfigController;
use App\Http\Controllers\Shop\TicketController;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminTicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes — No auth required
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search
Route::get('/search',             SearchController::class)->name('search');
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');

// Products — PUBLIC (guests see products but not prices)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{slug}', [ProductController::class, 'category'])->name('products.category');

// Print — PUBLIC (guests browse & configure; prices + add-to-cart require auth)
Route::prefix('impressio')->name('print.')->group(function () {
    Route::get('/', [PrintJobController::class, 'index'])->name('index');
    Route::get('/{template:slug}', [PrintJobController::class, 'builder'])->name('builder');
    Route::post('/{template:slug}/calculate', [PrintJobController::class, 'calculate'])->name('calculate');
});

// Locale switcher
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['ca', 'es', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Auth Routes — Guests only
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Password reset
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Pending & Rejected — accessible while logged in
Route::get('/pending', [AuthController::class, 'pending'])->name('pending');
Route::get('/rejected', [AuthController::class, 'rejected'])->name('rejected');

// Company invitations — public (no auth required to view, auth required to accept)
Route::get('/company/invitation/{token}', [CompanyController::class, 'showInvitation'])->name('company.invitation.show');
Route::post('/company/invitation/{token}/accept', [CompanyController::class, 'acceptInvitation'])->name('company.invitation.accept')->middleware('auth');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'approved'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Promo codes
    Route::post('/promo/apply',  [PromoCodeController::class, 'apply'])->name('promo.apply');
    Route::post('/promo/remove', [PromoCodeController::class, 'remove'])->name('promo.remove');

    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/{id}', [CartController::class, 'update'])->name('update');
        Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/place', [OrderController::class, 'place'])->name('place');
        Route::post('/{orderNumber}/reorder', [OrderController::class, 'reorder'])->name('reorder');
        Route::post('/{orderNumber}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{orderNumber}/payment-reference', [OrderController::class, 'submitPaymentReference'])->name('payment-reference');
        Route::get('/{orderNumber}', [OrderController::class, 'show'])->name('show');
    });

    // Wishlist
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/{productId}/toggle', [WishlistController::class, 'toggle'])->name('toggle');
        Route::delete('/{productId}', [WishlistController::class, 'destroy'])->name('destroy');
    });

    // Saved Addresses
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [SavedAddressController::class, 'index'])->name('index');
        Route::post('/', [SavedAddressController::class, 'store'])->name('store');
        Route::put('/{id}', [SavedAddressController::class, 'update'])->name('update');
        Route::patch('/{id}/default', [SavedAddressController::class, 'setDefault'])->name('default');
        Route::delete('/{id}', [SavedAddressController::class, 'destroy'])->name('destroy');
    });

    // Quotations
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/basket', [QuotationController::class, 'basket'])->name('basket');
        Route::post('/add', [QuotationController::class, 'add'])->name('add');
        Route::patch('/{id}', [QuotationController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuotationController::class, 'remove'])->name('remove');
        Route::post('/submit', [QuotationController::class, 'submit'])->name('submit');
        Route::get('/{quoteNumber}', [QuotationController::class, 'show'])->name('show');
        Route::patch('/{quoteNumber}/accept', [QuotationController::class, 'accept'])->name('accept');
    });

    // Reviews
    Route::post('/products/{slug}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{id}',          [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Print builder — auth-only actions
    Route::prefix('impressio')->name('print.')->group(function () {
        Route::get('/els-meus-treballs', [PrintJobController::class, 'myJobs'])->name('my-jobs');
        Route::post('/{template:slug}/add-to-cart', [PrintJobController::class, 'addToCart'])->name('add-to-cart');
        Route::post('/jobs/{job}/artwork', [PrintJobController::class, 'uploadArtwork'])->name('jobs.artwork');
        Route::delete('/jobs/{job}/artwork', [PrintJobController::class, 'deleteArtwork'])->name('jobs.artwork.delete');
        Route::delete('/jobs/{job}', [PrintJobController::class, 'cancel'])->name('jobs.cancel');
        Route::patch('/jobs/{job}/notes', [PrintJobController::class, 'updateNotes'])->name('jobs.notes');
        Route::patch('/jobs/{job}/received', [PrintJobController::class, 'confirmReceived'])->name('jobs.received');
        Route::post('/jobs/{job}/reorder', [PrintJobController::class, 'reorder'])->name('jobs.reorder');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{id}/download', [InvoiceController::class, 'download'])->name('download');
    });

    // Company management
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::patch('/', [CompanyController::class, 'update'])->name('update');
        Route::post('/invite', [CompanyController::class, 'invite'])->name('invite');
        Route::delete('/members/{member}', [CompanyController::class, 'removeMember'])->name('members.remove');
        Route::patch('/members/{member}/role', [CompanyController::class, 'updateMemberRole'])->name('members.role');
        Route::delete('/leave', [CompanyController::class, 'leave'])->name('leave');
    });

    // Saved print configurations
    Route::prefix('impressio/configuracions')->name('print.configs.')->group(function () {
        Route::get('/', [SavedPrintConfigController::class, 'index'])->name('index');
        Route::post('/', [SavedPrintConfigController::class, 'store'])->name('store');
        Route::delete('/{config}', [SavedPrintConfigController::class, 'destroy'])->name('destroy');
        Route::post('/{config}/add-to-cart', [SavedPrintConfigController::class, 'addToCart'])->name('add-to-cart');
    });

    // Support tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [TicketController::class, 'reply'])->name('reply');
        Route::patch('/{ticket}/close', [TicketController::class, 'close'])->name('close');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/pending', [AdminUserController::class, 'pending'])->name('pending');
        Route::get('/{id}', [AdminUserController::class, 'show'])->name('show');
        Route::patch('/{id}/approve', [AdminUserController::class, 'approve'])->name('approve');
        Route::patch('/{id}/reject', [AdminUserController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
    });

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle', [AdminProductController::class, 'toggle'])->name('toggle');
        Route::delete('/images/{imageId}', [AdminProductController::class, 'destroyImage'])->name('images.destroy');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{orderNumber}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{orderNumber}/status', [AdminOrderController::class, 'updateStatus'])->name('status');
        Route::patch('/{orderNumber}/tracking', [AdminOrderController::class, 'updateTracking'])->name('tracking');
        Route::patch('/{orderNumber}/paid', [AdminOrderController::class, 'markPaid'])->name('paid');
    });

    // Quotations
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [AdminQuotationController::class, 'index'])->name('index');
        Route::get('/{quoteNumber}', [AdminQuotationController::class, 'show'])->name('show');
        Route::patch('/{quoteNumber}/price', [AdminQuotationController::class, 'setPrice'])->name('price');
        Route::patch('/{quoteNumber}/status', [AdminQuotationController::class, 'updateStatus'])->name('status');
    });

    // AI helpers
    Route::post('/ai/generate-product-content', [AdminAiController::class, 'generateProductContent'])
        ->name('ai.product.generate');

    // Promo code management
    Route::prefix('promo-codes')->name('promo-codes.')->group(function () {
        Route::get('/',          [AdminPromoCodeController::class, 'index'])->name('index');
        Route::get('/create',    [AdminPromoCodeController::class, 'create'])->name('create');
        Route::post('/',         [AdminPromoCodeController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminPromoCodeController::class, 'edit'])->name('edit');
        Route::put('/{id}',      [AdminPromoCodeController::class, 'update'])->name('update');
        Route::delete('/{id}',   [AdminPromoCodeController::class, 'destroy'])->name('destroy');
    });

    // Review moderation
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/',              [AdminReviewController::class, 'index'])->name('index');
        Route::patch('/{id}/approve',[AdminReviewController::class, 'approve'])->name('approve');
        Route::patch('/{id}/reject', [AdminReviewController::class, 'reject'])->name('reject');
        Route::delete('/{id}',       [AdminReviewController::class, 'destroy'])->name('destroy');
    });

    // Print templates & jobs
    Route::prefix('print')->name('print.')->group(function () {
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [AdminPrintTemplateController::class, 'index'])->name('index');
            Route::get('/create', [AdminPrintTemplateController::class, 'create'])->name('create');
            Route::post('/', [AdminPrintTemplateController::class, 'store'])->name('store');
            Route::get('/{template}/edit', [AdminPrintTemplateController::class, 'edit'])->name('edit');
            Route::put('/{template}', [AdminPrintTemplateController::class, 'update'])->name('update');
            Route::delete('/{template}', [AdminPrintTemplateController::class, 'destroy'])->name('destroy');
            Route::patch('/{template}/toggle', [AdminPrintTemplateController::class, 'toggle'])->name('toggle');
        });
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', [AdminPrintJobController::class, 'index'])->name('index');
            Route::post('/bulk-status', [AdminPrintJobController::class, 'bulkUpdateStatus'])->name('bulk-status');
            Route::get('/{job}', [AdminPrintJobController::class, 'show'])->name('show');
            Route::patch('/{job}/status', [AdminPrintJobController::class, 'updateStatus'])->name('status');
            Route::patch('/{job}/delivery', [AdminPrintJobController::class, 'setDelivery'])->name('delivery');
            Route::post('/{job}/artwork', [AdminPrintJobController::class, 'uploadArtwork'])->name('artwork');
        });
    });

    // Companies
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [AdminCompanyController::class, 'index'])->name('index');
        Route::get('/{company}', [AdminCompanyController::class, 'show'])->name('show');
        Route::patch('/{company}', [AdminCompanyController::class, 'update'])->name('update');
    });

    // Admin support tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])->name('index');
        Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('reply');
        Route::patch('/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('status');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::patch('/{id}/read', [AdminNotificationController::class, 'markRead'])->name('read');
        Route::patch('/read-all', [AdminNotificationController::class, 'markAllRead'])->name('read-all');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
        Route::get('/revenue', [AdminReportController::class, 'revenue'])->name('revenue');
        Route::get('/products', [AdminReportController::class, 'products'])->name('products');
        Route::get('/clients', [AdminReportController::class, 'clients'])->name('clients');
        Route::get('/print-jobs', [AdminReportController::class, 'printJobs'])->name('print-jobs');
        Route::get('/export/orders', [AdminReportController::class, 'exportOrders'])->name('export.orders');
        Route::get('/export/clients', [AdminReportController::class, 'exportClients'])->name('export.clients');
        Route::get('/export/print-jobs', [AdminReportController::class, 'exportPrintJobs'])->name('export.print-jobs');
    });
});
