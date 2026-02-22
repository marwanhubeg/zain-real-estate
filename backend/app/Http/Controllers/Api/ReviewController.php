<?php

namespace App\Http\Controllers\Api;

use App\Models\Review;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'property']);

        // فلترة حسب العقار
        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // فلترة حسب التقييم
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // فلترة حسب الحالة
        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        // عرض التقييمات المعتمدة فقط للعامة
        if (!$request->user() || $request->user()->role !== 'admin') {
            $query->where('is_approved', true);
        }

        $reviews = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب التقييمات بنجاح',
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total()
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
            'booking_id' => 'nullable|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'comment_ar' => 'required|string',
            'comment_en' => 'nullable|string',
            'pros_ar' => 'nullable|string',
            'pros_en' => 'nullable|string',
            'cons_ar' => 'nullable|string',
            'cons_en' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من عدم تكرار التقييم
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('property_id', $request->property_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قيمت هذا العقار مسبقاً'
            ], 422);
        }

        $data = $request->except('images');
        $data['user_id'] = $request->user()->id;

        // معالجة الصور
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $images[] = $path;
            }
            $data['images'] = $images;
        }

        // التحقق من أن المستخدم حجز هذا العقار فعلاً
        $hasBooking = \App\Models\Booking::where('user_id', $request->user()->id)
            ->where('property_id', $request->property_id)
            ->where('status', 'completed')
            ->exists();

        if ($hasBooking) {
            $data['is_verified'] = true;
        }

        $review = Review::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة التقييم بنجاح، بانتظار المراجعة',
            'data' => new ReviewResource($review)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return response()->json([
            'success' => true,
            'message' => 'تم جلب التقييم بنجاح',
            'data' => new ReviewResource($review->load(['user', 'property']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        // التحقق من الصلاحية
        if ($review->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا التقييم'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|min:1|max:5',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'comment_ar' => 'sometimes|string',
            'comment_en' => 'nullable|string',
            'pros_ar' => 'nullable|string',
            'pros_en' => 'nullable|string',
            'cons_ar' => 'nullable|string',
            'cons_en' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->all());

        // إعادة التعيين إلى غير معتمد إذا عدل المستخدم
        if ($request->user()->role !== 'admin') {
            $review->update(['is_approved' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التقييم بنجاح',
            'data' => new ReviewResource($review)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        // التحقق من الصلاحية
        if ($review->user_id !== request()->user()->id && request()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذا التقييم'
            ], 403);
        }

        // حذف الصور
        if ($review->images) {
            foreach ($review->images as $image) {
                \Storage::disk('public')->delete($image);
            }
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التقييم بنجاح'
        ]);
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(Review $review)
    {
        $review->increment('helpful_count');

        return response()->json([
            'success' => true,
            'message' => 'شكراً على تقييمك',
            'data' => ['helpful_count' => $review->helpful_count]
        ]);
    }

    /**
     * Approve review (admin only)
     */
    public function approve(Request $request, Review $review)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك باعتماد التقييمات'
            ], 403);
        }

        $review->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $request->user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم اعتماد التقييم بنجاح',
            'data' => new ReviewResource($review)
        ]);
    }
}
