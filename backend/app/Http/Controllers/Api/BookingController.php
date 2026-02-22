<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'property', 'agent']);

        // فلترة حسب المستخدم
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب التاريخ
        if ($request->has('from_date')) {
            $query->whereDate('booking_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('booking_date', '<=', $request->to_date);
        }

        // للمستخدم العادي، يعرض حجوزاته فقط
        if ($request->user()->role === 'user') {
            $query->where('user_id', $request->user()->id);
        }

        // للوكيل، يعرض حجوزاته الموكلة له
        if ($request->user()->role === 'agent') {
            $query->where('agent_id', $request->user()->id);
        }

        $bookings = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الحجوزات بنجاح',
            'data' => BookingResource::collection($bookings),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'type' => 'required|in:visit,rent,buy',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'duration_minutes' => 'sometimes|integer|min:30',
            'number_of_people' => 'sometimes|integer|min:1|max:10',
            'notes' => 'nullable|string',
            'special_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من توفر العقار
        $property = Property::find($request->property_id);
        if ($property->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'العقار غير متاح للحجز'
            ], 422);
        }

        // التحقق من عدم وجود حجز مكرر
        $existingBooking = Booking::where('property_id', $request->property_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الموعد محجوز مسبقاً'
            ], 422);
        }

        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['agent_id'] = $property->user_id; // صاحب العقار
        $data['status'] = 'pending';
        $data['booking_number'] = 'BOK-' . strtoupper(uniqid());

        $booking = Booking::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحجز بنجاح',
            'data' => new BookingResource($booking->load(['user', 'property', 'agent']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        // التحقق من الصلاحية
        if ($this->cannotAccess($booking, request()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة هذا الحجز'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الحجز بنجاح',
            'data' => new BookingResource($booking->load(['user', 'property', 'agent', 'payment']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        // التحقق من الصلاحية
        if ($this->cannotAccess($booking, $request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا الحجز'
            ], 403);
        }

        // لا يمكن تعديل الحجوزات المؤكدة أو الملغية
        if (in_array($booking->status, ['confirmed', 'cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل حجز بحالة ' . $booking->status_text
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'booking_date' => 'sometimes|date|after_or_equal:today',
            'booking_time' => 'sometimes',
            'duration_minutes' => 'sometimes|integer|min:30',
            'number_of_people' => 'sometimes|integer|min:1|max:10',
            'notes' => 'nullable|string',
            'special_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحجز بنجاح',
            'data' => new BookingResource($booking)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        // التحقق من الصلاحية
        if ($this->cannotAccess($booking, request()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذا الحجز'
            ], 403);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الحجز بنجاح'
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        // التحقق من الصلاحية
        if ($this->cannotAccess($booking, $request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإلغاء هذا الحجز'
            ], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'الحجز ملغي بالفعل'
            ], 422);
        }

        if (in_array($booking->status, ['completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء حجز مكتمل'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى ذكر سبب الإلغاء',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
            'cancelled_by' => $request->user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح',
            'data' => new BookingResource($booking)
        ]);
    }

    /**
     * Confirm booking (for agent/admin)
     */
    public function confirm(Request $request, Booking $booking)
    {
        if (!in_array($request->user()->role, ['admin', 'agent'])) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتأكيد الحجوزات'
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'يمكن تأكيد الحجوزات المعلقة فقط'
            ], 422);
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => $request->user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تأكيد الحجز بنجاح',
            'data' => new BookingResource($booking)
        ]);
    }

    /**
     * Get user bookings
     */
    public function userBookings(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with(['property', 'agent'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب حجوزاتك بنجاح',
            'data' => BookingResource::collection($bookings),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total()
            ]
        ]);
    }

    /**
     * Check if user cannot access booking
     */
    private function cannotAccess($booking, $user)
    {
        return !in_array($user->role, ['admin']) 
            && $booking->user_id !== $user->id 
            && $booking->agent_id !== $user->id;
    }
}
