<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryScheduleController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\Admin\FooterLinkController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\PromoBannerController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\VoucherRuleController;
use App\Http\Controllers\Admin\SuspendedCustomerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NavLinkController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\PageMetaController;
use App\Http\Controllers\Admin\UiTextController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/reports/finance', [FinancialReportController::class, 'index'])->name('reports.finance');
        Route::get('/reports/finance/download/{format}', [FinancialReportController::class, 'download'])
            ->whereIn('format', ['xlsx', 'pdf'])
            ->middleware('throttle:20,1')
            ->name('reports.finance.download');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('account', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [AccountController::class, 'update'])->name('account.update');
        Route::put('account/password', [AccountController::class, 'updatePassword'])->name('account.password');

        // update 1: riwayat stok
        Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
        Route::resource('products', ProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('brands', BrandController::class)->except(['show']);
        Route::resource('reviews', ReviewController::class)->except(['show']);
        Route::resource('faqs', FaqController::class)->except(['show']);
        Route::resource('footer-links', FooterLinkController::class)->except(['show']);

        Route::resource('hero-slides', HeroSlideController::class)->except(['show']);
        Route::resource('promo-banners', PromoBannerController::class)->except(['show']);

        Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
        Route::resource('promo-codes', PromoCodeController::class)->except(['show']);
        Route::resource('discounts', DiscountController::class)->except(['show']);
        Route::resource('flash-sales', DiscountController::class)->except(['show'])->parameters(['flash-sales' => 'discount']);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);
        Route::resource('voucher-rules', VoucherRuleController::class)->except(['show'])->parameters(['voucher-rules' => 'voucherRule']);
        Route::get('delivery-schedule', [DeliveryScheduleController::class, 'index'])->name('delivery-schedule.index');
        Route::get('orders/{order}/invoice', [InvoiceController::class, 'show'])->name('orders.invoice');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::get('content', [PageContentController::class, 'index'])->name('content.index');
        Route::put('content', [PageContentController::class, 'update'])->name('content.update');
        Route::post('content/scan', [PageContentController::class, 'scan'])->name('content.scan');

        Route::get('settings', [SiteSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');

        Route::get('ui-texts', [UiTextController::class, 'index'])->name('ui-texts.index');
        Route::put('ui-texts', [UiTextController::class, 'update'])->name('ui-texts.update');
        Route::resource('nav-links', NavLinkController::class)->except(['show']);
        Route::resource('page-meta', PageMetaController::class)->except(['show'])->parameters(['page-meta' => 'pageMeta']);
        Route::get('newsletter-subscribers/export', [NewsletterSubscriberController::class, 'export'])->name('newsletter-subscribers.export');
        Route::resource('newsletter-subscribers', NewsletterSubscriberController::class)->only(['index', 'destroy']);

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::patch('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

        Route::resource('suspended-customers', SuspendedCustomerController::class)->only(['index', 'store', 'destroy']);

        Route::middleware('super_admin')->group(function () {
            Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
            Route::resource('admin-users', \App\Http\Controllers\Admin\AdminUserController::class)
                ->parameters(['admin-users' => 'adminUser'])
                ->except(['show']);
            Route::patch('admin-users/{adminUser}/status', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleStatus'])->name('admin-users.status');
        });
    });

});
