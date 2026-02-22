<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة الإعدادات'
            ], 403);
        }

        $query = Setting::query();

        // فلترة حسب المجموعة
        if ($request->has('group')) {
            $query->where('group', $request->group);
        }

        $settings = $query->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الإعدادات بنجاح',
            'data' => SettingResource::collection($settings)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإضافة إعدادات'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:settings|max:255',
            'value' => 'required',
            'type' => 'required|in:text,textarea,image,file,boolean,json',
            'group' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $setting = Setting::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الإعداد بنجاح',
            'data' => new SettingResource($setting)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Setting $setting)
    {
        // فقط للمشرفين أو الإعدادات العامة
        if (!$setting->is_public && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة هذا الإعداد'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الإعداد بنجاح',
            'data' => new SettingResource($setting)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتحديث الإعدادات'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'type' => 'sometimes|in:text,textarea,image,file,boolean,json',
            'group' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $setting->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإعداد بنجاح',
            'data' => new SettingResource($setting)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Setting $setting)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف الإعدادات'
            ], 403);
        }

        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإعداد بنجاح'
        ]);
    }

    /**
     * Get public settings
     */
    public function public(Request $request)
    {
        $settings = Setting::public()
            ->orderBy('sort_order')
            ->get()
            ->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الإعدادات العامة بنجاح',
            'data' => $settings
        ]);
    }

    /**
     * Get settings by group
     */
    public function byGroup(Request $request, $group)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة هذه الإعدادات'
            ], 403);
        }

        $settings = Setting::byGroup($group)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب إعدادات المجموعة بنجاح',
            'data' => SettingResource::collection($settings)
        ]);
    }
}
