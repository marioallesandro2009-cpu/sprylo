<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FooterLinkController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\PromoBannerController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SuspendedCustomerController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('account', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [AccountController::class, 'update'])->name('account.update');
        Route::put('account/password', [AccountController::class, 'updatePassword'])->name('account.password');

        Route::resource('products', ProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('brands', BrandController::class)->except(['show']);
        Route::resource('reviews', ReviewController::class)->except(['show']);
        Route::resource('faqs', FaqController::class)->except(['show']);
        Route::resource('page-meta', \App\Http\Controllers\Admin\PageMetaController::class)->parameters(['page-meta' => 'pageMeta'])->only(['index', 'edit', 'update']);
        Route::get('newsletter-subscribers/export', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'export'])->name('newsletter-subscribers.export');
        Route::resource('newsletter-subscribers', \App\Http\Controllers\Admin\NewsletterSubscriberController::class)->only(['index', 'destroy']);
        Route::resource('footer-links', FooterLinkController::class)->except(['show']);

        Route::resource('hero-slides', HeroSlideController::class)->except(['show']);
        Route::resource('promo-banners', PromoBannerController::class)->except(['show']);

        Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
        Route::resource('promo-codes', PromoCodeController::class)->except(['show']);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::get('content', [PageContentController::class, 'index'])->name('content.index');
        Route::put('content', [PageContentController::class, 'update'])->name('content.update');
        Route::post('content/scan', [PageContentController::class, 'scan'])->name('content.scan');

        Route::get('settings', [SiteSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::patch('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

        Route::resource('suspended-customers', SuspendedCustomerController::class)->only(['index', 'store', 'destroy']);
    });

});
