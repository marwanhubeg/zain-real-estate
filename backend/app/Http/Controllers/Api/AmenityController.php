<?php

namespace App\Http\Controllers\Api;

use App\Models\Amenity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AmenityResource;
use Illuminate\Support\Facades\Validator;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Amenity::query();

        // فلترة حسب التصنيف
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // فلترة حسب الحالة
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $amenities = $query->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المزايا بنجاح',
            'data' => AmenityResource::collection($amenities)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'category' => 'nullable|in:interior,exterior,security,utilities',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $amenity = Amenity::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الميزة بنجاح',
            'data' => new AmenityResource($amenity)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Amenity $amenity)
    {
        return response()->json([
            'success' => true,
            'message' => 'تم جلب الميزة بنجاح',
            'data' => new AmenityResource($amenity->load('properties'))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Amenity $amenity)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'category' => 'nullable|in:interior,exterior,security,utilities',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $amenity->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الميزة بنجاح',
            'data' => new AmenityResource($amenity)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amenity $amenity)
    {
        // التحقق من عدم ارتباطها بعقارات
        if ($amenity->properties()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الميزة لارتباطها بعقارات'
            ], 422);
        }

        $amenity->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الميزة بنجاح'
        ]);
    }

    /**
     * Get amenities by category
     */
    public function byCategory($category)
    {
        $amenities = Amenity::where('category', $category)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المزايا بنجاح',
            'data' => AmenityResource::collection($amenities)
        ]);
    }
}
