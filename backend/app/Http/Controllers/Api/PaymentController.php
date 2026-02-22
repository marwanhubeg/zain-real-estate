<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'booking', 'property']);

        // فلترة حسب المستخدم
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الطريقة
        if ($request->has('method')) {
            $query->where('method', $request->method);
        }

        // للمستخدم العادي، يعرض مدفوعاته فقط
        if ($request->user()->role === 'user') {
            $query->where('user_id', $request->user()->id);
        }

        $payments = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المدفوعات بنجاح',
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,bank_transfer,wallet',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::find($request->booking_id);

        // التحقق من أن الحجز يخص المستخدم
        if ($booking->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بدفع هذا الحجز'
            ], 403);
        }

        // التحقق من عدم وجود دفعة سابقة لهذا الحجز
        if (Payment::where('booking_id', $request->booking_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الحجز مدفوع مسبقاً'
            ], 422);
        }

        $data = $request->all();
        $data['user_id'] = $booking->user_id;
        $data['property_id'] = $booking->property_id;
        $data['payment_number'] = 'PAY-' . strtoupper(uniqid());
        $data['status'] = 'pending';

        $payment = Payment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء عملية الدفع بنجاح',
            'data' => new PaymentResource($payment->load(['booking', 'property']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        // التحقق من الصلاحية
        if ($payment->user_id !== request()->user()->id && request()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة هذه الدفعة'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الدفعة بنجاح',
            'data' => new PaymentResource($payment->load(['booking', 'property', 'user']))
        ]);
    }

    /**
     * Process payment (simulate)
     */
    public function process(Request $request, Payment $payment)
    {
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتنفيذ هذه الدفعة'
            ], 403);
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'هذه الدفعة تمت معالجتها مسبقاً'
            ], 422);
        }

        // محاكاة معالجة الدفع
        $success = rand(0, 10) > 1; // 90% نجاح

        if ($success) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            ]);

            // تحديث حالة الحجز
            $payment->booking->update(['status' => 'confirmed']);

            $message = 'تمت عملية الدفع بنجاح';
        } else {
            $payment->update([
                'status' => 'failed',
                'notes' => 'فشلت عملية الدفع، يرجى المحاولة مرة أخرى'
            ]);

            $message = 'فشلت عملية الدفع';
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => new PaymentResource($payment)
        ]);
    }
}
