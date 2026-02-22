<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ======================================================================
// TEST ROUTE - للتأكد من أن API يعمل
// ======================================================================
Route::get('/test', function() {
    return response()->json([
        'success' => true,
        'message' => '🏠 Zain Real Estate API is working!',
        'version' => '1.0.0',
        'endpoints' => [
            '/api/v1/test',
            '/api/v1/properties',
            '/api/v1/categories',
            '/api/v1/locations',
            '/api/v1/amenities',
            '/api/v1/auth/login',
            '/api/v1/auth/register',
            '/api/v1/user/profile'
        ],
        'timestamp' => now()->toDateTimeString()
    ]);
});

// ======================================================================
// API V1 - جميع المسارات تحت الإصدار الأول
// ======================================================================
Route::prefix('v1')->group(function () {
    
    // ------------------------------------------------------------------
    // المسارات العامة (لا تحتاج مصادقة)
    // ------------------------------------------------------------------
    
    // 1. المصادقة العامة
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
        Route::get('/verify-email/{id}/{hash}', 'verifyEmail')->name('verification.verify');
        Route::post('/resend-verification', 'resendVerificationEmail')->middleware('auth:sanctum');
    });
    
    // 2. العقارات (عامة)
    Route::controller(PropertyController::class)->prefix('properties')->group(function () {
        Route::get('/', 'index');
        Route::get('/featured', 'featured');
        Route::get('/search', 'search');
        Route::get('/{property:slug}', 'show');
        Route::get('/category/{category:slug}', 'byCategory');
        Route::get('/location/{location}', 'byLocation');
    });
    
    // 3. التصنيفات (عامة)
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/', 'index');
        Route::get('/tree', 'tree');
        Route::get('/{category:slug}', 'show');
        Route::get('/{category:slug}/properties', 'properties');
    });
    
    // 4. المواقع (عامة)
    Route::controller(LocationController::class)->prefix('locations')->group(function () {
        Route::get('/', 'index');
        Route::get('/cities', 'cities');
        Route::get('/ismailia', 'ismailia');
        Route::get('/{location}', 'show');
        Route::get('/{location}/properties', 'properties');
    });
    
    // 5. المزايا (عامة)
    Route::controller(AmenityController::class)->prefix('amenities')->group(function () {
        Route::get('/', 'index');
        Route::get('/category/{category}', 'byCategory');
        Route::get('/{amenity}', 'show');
    });
    
    // 6. التقييمات (عامة - عرض فقط)
    Route::controller(ReviewController::class)->prefix('reviews')->group(function () {
        Route::get('/', 'index');
        Route::get('/property/{property}', 'propertyReviews');
        Route::get('/{review}', 'show');
    });
    
    // 7. الإعدادات العامة
    Route::get('/settings/public', [SettingController::class, 'public']);
    
    // 8. التواصل (عام - إرسال رسالة)
    Route::post('/contacts', [ContactController::class, 'store']);
    
    // 9. TEST ROUTE داخل v1
    Route::get('/test', function() {
        return response()->json([
            'success' => true,
            'message' => '✅ API V1 is working!',
            'version' => '1.0.0'
        ]);
    });
    
    // ------------------------------------------------------------------
    // المسارات المحمية (تحتاج مصادقة)
    // ------------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        
        // 1. الملف الشخصي
        Route::controller(UserController::class)->prefix('user')->group(function () {
            Route::get('/', 'profile');
            Route::put('/', 'updateProfile');
            Route::post('/avatar', 'updateAvatar');
            Route::get('/bookings', 'userBookings');
            Route::get('/favorites', 'favorites');
        });
        
        // 2. تسجيل الخروج
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        // 3. العقارات (خاصة - للمالكين)
        Route::apiResource('properties', PropertyController::class)
            ->except(['index', 'show'])
            ->parameters(['properties' => 'property:slug']);
        
        // 4. الحجوزات
        Route::controller(BookingController::class)->prefix('bookings')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{booking}', 'show');
            Route::put('/{booking}', 'update');
            Route::delete('/{booking}', 'destroy');
            Route::post('/{booking}/cancel', 'cancel');
            Route::post('/{booking}/confirm', 'confirm')->middleware('role:admin,agent');
            Route::get('/user/bookings', 'userBookings');
        });
        
        // 5. المدفوعات
        Route::controller(PaymentController::class)->prefix('payments')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{payment}', 'show');
            Route::post('/{payment}/process', 'process');
        });
        
        // 6. التقييمات (خاصة - إضافة تقييم)
        Route::controller(ReviewController::class)->prefix('reviews')->group(function () {
            Route::post('/', 'store');
            Route::put('/{review}', 'update');
            Route::delete('/{review}', 'destroy');
            Route::post('/{review}/helpful', 'markHelpful');
        });
        
        // 7. المفضلة
        Route::controller(FavoriteController::class)->prefix('favorites')->group(function () {
            Route::get('/', 'index');
            Route::post('/{property}', 'toggle');
            Route::delete('/{property}', 'remove');
            Route::get('/check/{property}', 'check');
        });
        
        // ------------------------------------------------------------------
        // مسارات المشرفين (Admin Only)
        // ------------------------------------------------------------------
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            
            // 1. لوحة التحكم
            Route::controller(DashboardController::class)->group(function () {
                Route::get('/dashboard', 'index');
                Route::get('/statistics', 'statistics');
                Route::get('/recent-activities', 'recentActivities');
                Route::get('/chart-data', 'chartData');
                Route::get('/reports/{type}', 'reports');
            });
            
            // 2. إدارة المستخدمين
            Route::apiResource('users', UserController::class);
            
            // 3. إدارة التصنيفات (كاملة)
            Route::apiResource('categories', CategoryController::class)
                ->parameters(['categories' => 'category:slug']);
            
            // 4. إدارة المواقع
            Route::apiResource('locations', LocationController::class);
            
            // 5. إدارة المزايا
            Route::apiResource('amenities', AmenityController::class);
            
            // 6. إدارة جهات الاتصال
            Route::controller(ContactController::class)->prefix('contacts')->group(function () {
                Route::get('/', 'index');
                Route::get('/{contact}', 'show');
                Route::put('/{contact}', 'update');
                Route::delete('/{contact}', 'destroy');
                Route::post('/{contact}/reply', 'reply');
                Route::post('/{contact}/close', 'close');
            });
            
            // 7. إدارة الإعدادات
            Route::apiResource('settings', SettingController::class);
            Route::get('/settings/group/{group}', [SettingController::class, 'byGroup']);
            
            // 8. الموافقة على التقييمات
            Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve']);
            
            // 9. الموافقة على العقارات
            Route::post('/properties/{property}/approve', [PropertyController::class, 'approve']);
        });
    });
});

// ======================================================================
// مسارات إضافية خارج v1 (للتوافق مع الإصدارات السابقة)
// ======================================================================

// مسارات المصادقة
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// مسارات بسيطة
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{property:slug}', [PropertyController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/amenities', [AmenityController::class, 'index']);

// Route للاختبار بدون v1
Route::get('/ping', function() {
    return response()->json([
        'success' => true,
        'message' => 'pong',
        'timestamp' => now()->toDateTimeString()
    ]);
});


// ========== مسارات الذكاء الاصطناعي ==========
Route::prefix('v1')->group(function () {
    Route::get('/recommendations/personalized', [App\Http\Controllers\Api\RecommendationController::class, 'personalized'])
        ->middleware('auth:sanctum');
    
    Route::get('/recommendations/trending', [App\Http\Controllers\Api\RecommendationController::class, 'trending']);
    
    Route::get('/recommendations/similar/{property}', [App\Http\Controllers\Api\RecommendationController::class, 'similar']);
    
    Route::get('/recommendations/budget', [App\Http\Controllers\Api\RecommendationController::class, 'byBudget']);
    
    Route::get('/predictions/prices', [App\Http\Controllers\Api\RecommendationController::class, 'predictPrices']);
    
    Route::get('/predictions/demand', [App\Http\Controllers\Api\RecommendationController::class, 'predictDemand']);
});

// ========== مسارات الذكاء الاصطناعي ==========
Route::prefix('v1')->group(function () {
    Route::get('/recommendations/personalized', [App\Http\Controllers\Api\RecommendationController::class, 'personalized'])
        ->middleware('auth:sanctum');
    
    Route::get('/recommendations/trending', [App\Http\Controllers\Api\RecommendationController::class, 'trending']);
    
    Route::get('/recommendations/similar/{property}', [App\Http\Controllers\Api\RecommendationController::class, 'similar']);
    
    Route::get('/recommendations/budget', [App\Http\Controllers\Api\RecommendationController::class, 'byBudget']);
    
    Route::get('/predictions/prices', [App\Http\Controllers\Api\RecommendationController::class, 'predictPrices']);
    
    Route::get('/predictions/demand', [App\Http\Controllers\Api\RecommendationController::class, 'predictDemand']);
});
