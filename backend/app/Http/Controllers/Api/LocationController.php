<?php

namespace App\Http\Controllers\Api;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Location::query();

        // بحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('city_ar', 'like', "%{$search}%")
                  ->orWhere('city_en', 'like', "%{$search}%")
                  ->orWhere('district_ar', 'like', "%{$search}%")
                  ->orWhere('district_en', 'like', "%{$search}%");
            });
        }

        // فلترة حسب المدينة
        if ($request->has('city')) {
            $query->where('city_ar', 'like', "%{$request->city}%")
                  ->orWhere('city_en', 'like', "%{$request->city}%");
        }

        $locations = $query->orderBy('sort_order')->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المواقع بنجاح',
            'data' => LocationResource::collection($locations),
            'meta' => [
                'current_page' => $locations->currentPage(),
                'last_page' => $locations->lastPage(),
                'per_page' => $locations->perPage(),
                'total' => $locations->total()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_ar' => 'required|string|max:255',
            'city_en' => 'nullable|string|max:255',
            'district_ar' => 'required|string|max:255',
            'district_en' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'zoom_level' => 'nullable|integer',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $location = Location::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الموقع بنجاح',
            'data' => new LocationResource($location)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        return response()->json([
            'success' => true,
            'message' => 'تم جلب الموقع بنجاح',
            'data' => new LocationResource($location->load('properties'))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'city_ar' => 'sometimes|required|string|max:255',
            'city_en' => 'nullable|string|max:255',
            'district_ar' => 'sometimes|required|string|max:255',
            'district_en' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'zoom_level' => 'nullable|integer',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $location->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الموقع بنجاح',
            'data' => new LocationResource($location)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        // التحقق من عدم وجود عقارات مرتبطة
        if ($location->properties()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الموقع لوجود عقارات مرتبطة به'
            ], 422);
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الموقع بنجاح'
        ]);
    }

    /**
     * Get locations in Ismailia
     */
    public function ismailia()
    {
        $locations = Location::inIsmailia()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب مواقع الإسماعيلية بنجاح',
            'data' => LocationResource::collection($locations)
        ]);
    }

    /**
     * Get distinct cities
     */
    public function cities()
    {
        $cities = Location::where('is_active', true)
            ->select('city_ar', 'city_en')
            ->distinct()
            ->orderBy('city_ar')
            ->get()
            ->map(function ($item) {
                return [
                    'ar' => $item->city_ar,
                    'en' => $item->city_en,
                    'name' => app()->getLocale() == 'ar' ? $item->city_ar : $item->city_en
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المدن بنجاح',
            'data' => $cities
        ]);
    }
}
