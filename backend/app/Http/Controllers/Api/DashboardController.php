<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index(Request $request)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة لوحة التحكم'
            ], 403);
        }

        $stats = [
            'users' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'agents' => User::where('role', 'agent')->count(),
                'clients' => User::where('role', 'user')->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            ],
            'properties' => [
                'total' => Property::count(),
                'for_sale' => Property::where('type', 'sale')->count(),
                'for_rent' => Property::where('type', 'rent')->count(),
                'available' => Property::where('status', 'available')->count(),
                'pending' => Property::where('status', 'pending')->count(),
                'sold' => Property::where('status', 'sold')->count(),
                'rented' => Property::where('status', 'rented')->count(),
                'featured' => Property::where('is_featured', true)->count(),
                'new_this_month' => Property::whereMonth('created_at', now()->month)->count(),
            ],
            'bookings' => [
                'total' => Booking::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
                'completed' => Booking::where('status', 'completed')->count(),
                'today' => Booking::whereDate('booking_date', today())->count(),
            ],
            'reviews' => [
                'total' => Review::count(),
                'approved' => Review::where('is_approved', true)->count(),
                'pending' => Review::where('is_approved', false)->count(),
                'average_rating' => Review::avg('rating'),
            ],
            'payments' => [
                'total' => Payment::count(),
                'completed' => Payment::where('status', 'completed')->count(),
                'pending' => Payment::where('status', 'pending')->count(),
                'failed' => Payment::where('status', 'failed')->count(),
                'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'تم جلب إحصائيات لوحة التحكم بنجاح',
            'data' => $stats
        ]);
    }

    /**
     * Get recent activities
     */
    public function recentActivities(Request $request)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة النشاطات'
            ], 403);
        }

        $recentUsers = User::latest()->take(5)->get()->map(function ($user) {
            return [
                'type' => 'user',
                'action' => 'مستخدم جديد',
                'description' => "تم إضافة المستخدم {$user->name}",
                'user' => $user->name,
                'time' => $user->created_at->diffForHumans(),
                'created_at' => $user->created_at,
            ];
        });

        $recentProperties = Property::with('user')->latest()->take(5)->get()->map(function ($property) {
            return [
                'type' => 'property',
                'action' => 'عقار جديد',
                'description' => "تم إضافة عقار {$property->title} بواسطة {$property->user?->name}",
                'user' => $property->user?->name,
                'time' => $property->created_at->diffForHumans(),
                'created_at' => $property->created_at,
            ];
        });

        $recentBookings = Booking::with(['user', 'property'])->latest()->take(5)->get()->map(function ($booking) {
            return [
                'type' => 'booking',
                'action' => 'حجز جديد',
                'description' => "حجز {$booking->property?->title} بواسطة {$booking->user?->name}",
                'user' => $booking->user?->name,
                'time' => $booking->created_at->diffForHumans(),
                'created_at' => $booking->created_at,
            ];
        });

        $activities = $recentUsers
            ->concat($recentProperties)
            ->concat($recentBookings)
            ->sortByDesc('created_at')
            ->values()
            ->take(10);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب آخر النشاطات بنجاح',
            'data' => $activities
        ]);
    }

    /**
     * Get chart data
     */
    public function chartData(Request $request)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة البيانات'
            ], 403);
        }

        $months = [];
        $usersData = [];
        $propertiesData = [];

        // آخر 12 شهر
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('Y-m');
            
            $usersData[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $propertiesData[] = Property::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'months' => $months,
                'users' => $usersData,
                'properties' => $propertiesData,
            ]
        ]);
    }

    /**
     * Get reports
     */
    public function reports(Request $request, $type)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة التقارير'
            ], 403);
        }

        switch ($type) {
            case 'properties':
                $data = Property::select('type', DB::raw('count(*) as total'))
                    ->groupBy('type')
                    ->get();
                break;

            case 'users':
                $data = User::select('role', DB::raw('count(*) as total'))
                    ->groupBy('role')
                    ->get();
                break;

            case 'bookings':
                $data = Booking::select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->get();
                break;

            case 'revenue':
                $data = Payment::select(
                    DB::raw('strftime("%Y-%m", created_at) as month'),
                    DB::raw('sum(amount) as total')
                )
                    ->where('status', 'completed')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'نوع التقرير غير صالح'
                ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جلب التقرير بنجاح',
            'data' => $data
        ]);
    }
}
