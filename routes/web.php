<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboard;
use App\Http\Controllers\Vendor\PropertyController as VendorProperty;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [\App\Http\Controllers\PageController::class, 'show'])->name('home');
Route::get('/{slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('page.show');

// Generic Logout for all web sessions
Route::post('/logout', function() {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Admin Authentication
Route::get('/admin/login', [AdminAuth::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AdminAuth::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuth::class, 'logout'])->name('admin.logout');

// Vendor Authentication
Route::get('/vendor/login', [\App\Http\Controllers\Vendor\AuthController::class, 'showLoginForm'])->name('vendor.login');
Route::get('/vendor/register', [\App\Http\Controllers\Vendor\VendorRegisterController::class, 'showRegisterForm'])->name('vendor.register');
Route::post('/vendor/register', [\App\Http\Controllers\Vendor\VendorRegisterController::class, 'register'])->name('vendor.register.post');
Route::post('/vendor/login', [\App\Http\Controllers\Vendor\AuthController::class, 'login'])->name('vendor.login.post');
Route::post('/vendor/logout', [\App\Http\Controllers\Vendor\AuthController::class, 'logout'])->name('vendor.logout');

Route::middleware(['admin'])->prefix('admin')->group(function () {

    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('admin.chat');
    
    Route::get('/chat/messages/{userId}', [App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::post('/chat/typing', [App\Http\Controllers\ChatController::class, 'typing']);
    Route::get('/chat/users', [App\Http\Controllers\ChatController::class, 'getUsers']);
    Route::get('/chat/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount']);

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
    Route::resource('vendors', \App\Http\Controllers\Admin\VendorController::class)->names('admin.vendors');
    Route::resource('properties', \App\Http\Controllers\Admin\PropertyController::class)->names('admin.properties');
    // Booking Management
    Route::get('bookings',                  [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('bookings/create',           [\App\Http\Controllers\Admin\BookingController::class, 'create'])->name('admin.bookings.create');
    Route::post('bookings',                 [\App\Http\Controllers\Admin\BookingController::class, 'store'])->name('admin.bookings.store');
    Route::get('bookings/{booking}',        [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('admin.bookings.show');
    Route::get('bookings/{booking}/edit',   [\App\Http\Controllers\Admin\BookingController::class, 'edit'])->name('admin.bookings.edit');
    Route::put('bookings/{booking}',        [\App\Http\Controllers\Admin\BookingController::class, 'update'])->name('admin.bookings.update');
    Route::post('bookings/{booking}/refund',[\App\Http\Controllers\Admin\BookingController::class, 'cancelAndRefund'])->name('admin.bookings.refund');
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->names('admin.pages');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('admin.categories');
    Route::resource('amenities', \App\Http\Controllers\Admin\AmenityController::class)->names('admin.amenities');
    Route::resource('states', \App\Http\Controllers\Admin\StateController::class)->names('admin.states');
    Route::resource('destinations', \App\Http\Controllers\Admin\DestinationController::class)->names('admin.destinations');
    Route::post('blogs/upload', [\App\Http\Controllers\Admin\BlogController::class, 'uploadImage'])->name('admin.blogs.upload');
    Route::resource('blogs', \App\Http\Controllers\Admin\BlogController::class)->names('admin.blogs');
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class)->names('admin.coupons');
    Route::resource('collections', \App\Http\Controllers\Admin\CollectionController::class)->names('admin.collections');
    Route::resource('withdrawals', \App\Http\Controllers\Admin\WithdrawalController::class)->only(['index', 'update'])->names('admin.withdrawals');
    Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class)->only(['index', 'show', 'destroy'])->names('admin.contacts');
    
    // Accounting
    Route::get('accounting', [\App\Http\Controllers\Admin\AccountingController::class, 'index'])->name('admin.accounting.index');
    Route::get('accounting/create', [\App\Http\Controllers\Admin\AccountingController::class, 'create'])->name('admin.accounting.create');
    Route::post('accounting', [\App\Http\Controllers\Admin\AccountingController::class, 'store'])->name('admin.accounting.store');
    Route::get('accounting/trial-balance', [\App\Http\Controllers\Admin\AccountingController::class, 'trialBalance'])->name('admin.accounting.trial_balance');
    Route::get('accounting/profit-loss', [\App\Http\Controllers\Admin\AccountingController::class, 'profitAndLoss'])->name('admin.accounting.profit_loss');
    // Route::get('accounting/{account}/ledger', [\App\Http\Controllers\Admin\AccountingController::class, 'ledger'])->name('admin.accounting.ledger');
    
    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');

    Route::delete('properties/image/{property}', [\App\Http\Controllers\Admin\PropertyController::class, 'deleteImage'])->name('admin.properties.image.delete');
    
    // Room Management
    Route::get('properties/{property}/rooms', [\App\Http\Controllers\Admin\RoomController::class, 'index'])->name('admin.properties.rooms.index');
    Route::get('properties/{property}/rooms/create', [\App\Http\Controllers\Admin\RoomController::class, 'create'])->name('admin.properties.rooms.create');
    Route::post('properties/{property}/rooms', [\App\Http\Controllers\Admin\RoomController::class, 'store'])->name('admin.properties.rooms.store');
    Route::get('rooms/{room}/edit', [\App\Http\Controllers\Admin\RoomController::class, 'edit'])->name('admin.rooms.edit');
    Route::put('rooms/{room}', [\App\Http\Controllers\Admin\RoomController::class, 'update'])->name('admin.rooms.update');
    Route::delete('rooms/{room}', [\App\Http\Controllers\Admin\RoomController::class, 'destroy'])->name('admin.rooms.destroy');
    Route::delete('rooms/image/{room}', [\App\Http\Controllers\Admin\RoomController::class, 'deleteImage'])->name('admin.rooms.image.delete');
});

// Vendor Routes
Route::middleware(['vendor'])->prefix('vendor')->group(function () {
      Route::get('/chat', [App\Http\Controllers\Vendor\ChatController::class, 'index'])->name('vendor.chat');
    Route::get('/chat/customers', [App\Http\Controllers\Vendor\ChatController::class, 'getCustomers']);
    Route::get('/chat/messages/{userId}', [App\Http\Controllers\Vendor\ChatController::class, 'getMessages']);
    Route::post('/chat/send', [App\Http\Controllers\Vendor\ChatController::class, 'sendMessage']);
    Route::post('/chat/typing', [App\Http\Controllers\Vendor\ChatController::class, 'typing']);
    Route::get('/chat/unread-count', [App\Http\Controllers\Vendor\ChatController::class, 'getUnreadCount']);
    
    Route::get('/dashboard', [VendorDashboard::class, 'index'])->name('vendor.dashboard');
    Route::get('/properties', [VendorProperty::class, 'index'])->name('vendor.properties');
    Route::get('/properties/create', [VendorProperty::class, 'create'])->name('vendor.properties.create');
    Route::post('/properties', [VendorProperty::class, 'store'])->name('vendor.properties.store');
    Route::get('/properties/{property}/edit', [VendorProperty::class, 'edit'])->name('vendor.properties.edit');
    Route::put('/properties/{property}', [VendorProperty::class, 'update'])->name('vendor.properties.update');
    Route::delete('/properties/image/{property}', [VendorProperty::class, 'deleteImage'])->name('vendor.properties.image.delete');
    
    // Vendor Coupons
    Route::resource('coupons', \App\Http\Controllers\Vendor\CouponController::class)->names('vendor.coupons');
    
    Route::get('/bookings', [\App\Http\Controllers\Vendor\BookingController::class, 'index'])->name('vendor.bookings.index');
    Route::get('/withdrawals', [\App\Http\Controllers\Vendor\WithdrawalController::class, 'index'])->name('vendor.withdrawals.index');
    Route::post('/withdrawals', [\App\Http\Controllers\Vendor\WithdrawalController::class, 'store'])->name('vendor.withdrawals.store');
    
    // Vendor Profile
    Route::get('/profile', [\App\Http\Controllers\Vendor\ProfileController::class, 'index'])->name('vendor.profile');
    Route::put('/profile', [\App\Http\Controllers\Vendor\ProfileController::class, 'update'])->name('vendor.profile.update');
});
