<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceCheckController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'add'])->middleware('throttle:30,1')->name('cart.add');
Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart-promo', [CartController::class, 'applyPromo'])->middleware('throttle:10,1')->name('cart.promo.apply');
Route::delete('/cart-promo', [CartController::class, 'removePromo'])->name('cart.promo.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/rates', [CheckoutController::class, 'rates'])->middleware('throttle:20,1')->name('checkout.rates');
Route::get('/checkout/delivery-slots', [CheckoutController::class, 'deliverySlots'])->middleware('throttle:60,1')->name('checkout.delivery-slots');
Route::post('/checkout/postal-lookup', [CheckoutController::class, 'postalLookup'])->middleware('throttle:20,1')->name('checkout.postal-lookup');
Route::post('/checkout/variant', [CheckoutController::class, 'changeVariant'])->middleware('throttle:30,1')->name('checkout.variant');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::post('/checkout/confirmation/{order}/payment-proof', [CheckoutController::class, 'uploadProof'])->middleware('throttle:10,1')->name('checkout.payment-proof');

Route::get('/invoice/{order}', [InvoiceController::class, 'show'])->middleware(['signed', 'throttle:30,1'])->name('invoices.show');
Route::get('/invoice-check', [InvoiceCheckController::class, 'index'])->name('invoice-check.index');
Route::post('/invoice-check', [InvoiceCheckController::class, 'lookup'])->middleware('throttle:15,1')->name('invoice-check.lookup');

Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('track-order.index');
Route::post('/track-order', [OrderTrackingController::class, 'lookup'])->middleware('throttle:15,1')->name('track-order.lookup');
Route::post('/track-order/{order}/payment-proof', [OrderTrackingController::class, 'uploadProof'])->middleware('throttle:10,1')->name('track-order.payment-proof');
Route::post('/track-order/{order}/reorder', [OrderTrackingController::class, 'reorder'])->middleware('throttle:10,1')->name('track-order.reorder');


Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
Route::post('/compare/{product}', [CompareController::class, 'toggle'])->middleware('throttle:60,1')->name('compare.toggle');
Route::delete('/compare/{product}', [CompareController::class, 'remove'])->name('compare.remove');
Route::delete('/compare', [CompareController::class, 'clear'])->name('compare.clear');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
Route::delete('/wishlist/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:5,1')->name('newsletter.store');

Route::get('/help', [HelpCenterController::class, 'index'])->name('help.index');
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy.index');
