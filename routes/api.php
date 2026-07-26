<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\InstagramLinkController;
use App\Http\Controllers\Api\CancellationApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\CancellationController;


// ─── HELPER ROUTES (For Live Server Management) ──────────────────────────────
Route::get('/clear-all-cache', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('storage:link');
    return response()->json(['message' => 'All caches cleared and storage linked!']);
});


Route::get('/cancellation-rules', [CancellationApiController::class, 'index']);
Route::get('/cancellation-rules/calculate', [CancellationApiController::class, 'calculate']);

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ['--force' => true]);
    return response()->json(['message' => 'Migrations executed successfully!', 'output' => Artisan::output()]);
});

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);


Route::get('/create-storage-link', function () {
    try {
        Artisan::call('storage:link');

        return response()->json([
            'success' => true,
            'message' => 'Storage link created successfully',
            'link' => public_path('storage')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
});

// Public Coupon API
Route::get('coupons', [\App\Http\Controllers\Api\CouponController::class, 'index']);
Route::post('coupons/apply', [\App\Http\Controllers\Api\CouponController::class, 'apply']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/v1/instagram-links', [InstagramLinkController::class, 'index']);

    Route::post('/customer/booking/cancel', [CancellationController::class, 'cancelBooking']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ─── Chat API ─────────────────────────────────────────────────────────────
    Route::prefix('chat')->group(function () {
        // List all conversations (users you've chatted with)
        Route::get('/conversations', [\App\Http\Controllers\Api\ChatController::class, 'conversations']);

        // Get all messages with a specific user
        Route::get('/messages/{userId}', [\App\Http\Controllers\Api\ChatController::class, 'getMessages']);

        // Send a message
        Route::post('/send', [\App\Http\Controllers\Api\ChatController::class, 'sendMessage']);

        // Broadcast typing indicator
        Route::post('/typing', [\App\Http\Controllers\Api\ChatController::class, 'typing']);

        // Total unread message count
        Route::get('/unread-count', [\App\Http\Controllers\Api\ChatController::class, 'unreadCount']);

        // Delete own message
        Route::delete('/messages/{messageId}', [\App\Http\Controllers\Api\ChatController::class, 'deleteMessage']);
    });
});

// Public Resource API Routes
Route::get('/settings', [\App\Http\Controllers\Api\SettingController::class, 'index']);
Route::get('/pages', [\App\Http\Controllers\Api\PageController::class, 'index']);
Route::get('/pages/{slug}', [\App\Http\Controllers\Api\PageController::class, 'show']);

// Blog API Routes
Route::get('/blogs', [\App\Http\Controllers\Api\BlogController::class, 'index']);
Route::get('/blogs/{slug}', [\App\Http\Controllers\Api\BlogController::class, 'show']);

// Discovery API Routes
Route::get('/discovery/best-rates', [\App\Http\Controllers\Api\DiscoveryController::class, 'bestRates']);
Route::get('/discovery/for-you', [\App\Http\Controllers\Api\DiscoveryController::class, 'propertiesForYou']);
Route::get('/discovery/menu-destinations', [\App\Http\Controllers\Api\DiscoveryController::class, 'menuDestinations']);
Route::get('/discovery/destination-properties', [\App\Http\Controllers\Api\DiscoveryController::class, 'destinationProperties']);
Route::get('/discovery/menu-categories', [\App\Http\Controllers\Api\DiscoveryController::class, 'menuCategories']);
Route::get('/discovery/category-properties', [\App\Http\Controllers\Api\DiscoveryController::class, 'categoryProperties']);
Route::get('/properties', [\App\Http\Controllers\Api\PropertyController::class, 'index']);
Route::get('/properties/{slug}', [\App\Http\Controllers\Api\PropertyController::class, 'show']);
Route::get('/properties/{slug}/related', [\App\Http\Controllers\Api\PropertyController::class, 'related']);
Route::get('/properties/{slug}/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'index']);

// Booked dates by property ID — returns each date with customer info for hover tooltips
Route::get('/properties/{id}/booked-dates', function ($id) {
    $bookings = \App\Models\Booking::with('customer')
        ->where('property_id', $id)
        ->where('status', '!=', 'cancelled')
        ->get();

    $dateMap = []; // date => [{ name, booking_number, check_in, check_out, status }]

    foreach ($bookings as $bk) {
        $checkIn = \Carbon\Carbon::parse($bk->check_in);
        $checkOut = \Carbon\Carbon::parse($bk->check_out);
        $period = \Carbon\CarbonPeriod::create($checkIn, $checkOut);
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            if (!isset($dateMap[$d])) $dateMap[$d] = [];
            
            $name = $bk->customer->name ?? 'Guest';
            $bookingNumber = $bk->booking_number ?? 'N/A';

            $dateMap[$d][] = [
                'name'           => $name,
                'booking_number' => $bookingNumber,
                'check_in'       => $checkIn->format('d M Y'),
                'check_out'      => $checkOut->format('d M Y'),
                'status'         => $bk->status,
            ];
        }
    }

    return response()->json([
        'status'       => 'success',
        'booked_dates' => array_keys($dateMap),
        'date_info'    => $dateMap,
    ]);
});
Route::get('collections', [\App\Http\Controllers\Api\CollectionController::class, 'index']);
Route::get('vendor-registration/setup', [\App\Http\Controllers\Api\VendorRegisterController::class, 'setup']);
Route::post('vendor-registration/submit', [\App\Http\Controllers\Api\VendorRegisterController::class, 'submit']);

// Bookings route moved inside auth:sanctum middleware (see /customer/bookings below)

// Contact API Route
Route::post('/contact', [\App\Http\Controllers\Api\ContactController::class, 'store']);

// Coupon API Routes
Route::get('/coupons', [\App\Http\Controllers\Api\CouponController::class, 'index']);
Route::post('/coupons/validate', [\App\Http\Controllers\Api\CouponController::class, 'validateCoupon']);


// ─── Customer Auth ────────────────────────────────────────────────────────────
Route::prefix('customer')->group(function () {
    // Public — no auth needed
    Route::post('/send-otp', [\App\Http\Controllers\Api\CustomerAuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [\App\Http\Controllers\Api\CustomerAuthController::class, 'verifyOtp']);
    Route::post('/google', [\App\Http\Controllers\Api\CustomerAuthController::class, 'googleLogin']);

    // Protected — requires Bearer token
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Api\CustomerAuthController::class, 'profile']);
        Route::put('/profile', [\App\Http\Controllers\Api\CustomerAuthController::class, 'updateProfile']);
        Route::post('/logout', [\App\Http\Controllers\Api\CustomerAuthController::class, 'logout']);

        // Bookings

        Route::get('/bookings', [\App\Http\Controllers\Api\BookingController::class, 'index']);
        Route::post('/bookings', [\App\Http\Controllers\Api\BookingController::class, 'store']);
        Route::post('/booking-final', [\App\Http\Controllers\Api\BookingController::class, 'bookingfinal']);
        Route::get('/bookings/{id}', [\App\Http\Controllers\Api\BookingController::class, 'show']);
        // Coupons
        Route::get('/coupons', [\App\Http\Controllers\Api\CouponController::class, 'userCoupons']);

        // Wishlist
        Route::get('/wishlist', [\App\Http\Controllers\Api\WishlistController::class, 'index']);
        Route::post('/wishlist', [\App\Http\Controllers\Api\WishlistController::class, 'toggle']);
        Route::delete('/wishlist/{id}', [\App\Http\Controllers\Api\WishlistController::class, 'destroy']);
        Route::get('/wishlist/check/{property_id}', [\App\Http\Controllers\Api\WishlistController::class, 'check']);

        // Reviews
        Route::post('/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'store']);
        Route::put('/reviews/{id}', [\App\Http\Controllers\Api\ReviewController::class, 'update']);
        Route::delete('/reviews/{id}', [\App\Http\Controllers\Api\ReviewController::class, 'destroy']);
    });
});
