<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishListController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\AdminController;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'productDetails'])->name('shop.product.details');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}', [CartController::class, 'increaseCartQuantity'])->name('cart.increase.quantity');
Route::put('/cart/decrease-quantity/{rowId}', [CartController::class, 'decreaseCartQuantity'])->name('cart.decrease.quantity');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'emptyCart'])->name('cart.clear');

Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}', [WishlistController::class, 'removeItem'])->name('wishlist.item.remove');
Route::delete('wishlist/clear', [WishlistController::class, 'emptyWishlist'])->name('wishlist.item.clear');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'moveToCart'])->name('wishlist.move.to.cart');

Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/add', [AdminController::class, 'brandAdd'])->name('admin.brand.add');
    Route::post('/admin/brand/store', [AdminController::class, 'brandStore'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}', [AdminController::class, 'brandEdit'])->name('admin.brand.edit');
    Route::put('/admin/brand/update', [AdminController::class, 'brandUpdate'])->name('admin.brand.update');
    Route::delete('/admin/brand/destroy/{id}', [AdminController::class, 'brandDelete'])->name('admin.brand.delete');

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/add', [AdminController::class, 'categoryAdd'])->name('admin.category.add');
    Route::post('/admin/category/store', [AdminController::class, 'categoryStore'])->name('admin.category.store');
    Route::get('/admin/category/edit/{id}', [AdminController::class, 'categoryEdit'])->name('admin.category.edit');
    Route::put('/admin/category/update', [AdminController::class, 'categoryUpdate'])->name('admin.category.update');
    Route::delete('/admin/category/destroy/{id}', [AdminController::class, 'categoryDelete'])->name('admin.category.delete');

    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/add', [AdminController::class, 'productAdd'])->name('admin.product.add');
    Route::post('/admin/product/store', [AdminController::class, 'productStore'])->name('admin.product.store');
    Route::get('/admin/product/edit/{id}', [AdminController::class, 'productEdit'])->name('admin.product.edit');
    Route::put('/admin/product/update', [AdminController::class, 'productUpdate'])->name('admin.product.update');
    Route::delete('/admin/product/destroy/{id}', [AdminController::class, 'productDelete'])->name('admin.product.delete');

    Route::get('/admin/coupon', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add', [AdminController::class, 'couponAdd'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store', [AdminController::class, 'couponStore'])->name('admin.coupon.store');
});
