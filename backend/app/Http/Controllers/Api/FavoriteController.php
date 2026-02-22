<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;

class FavoriteController extends Controller
{
    /**
     * Display user's favorites
     */
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favoriteProperties()
            ->with(['category', 'location'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المفضلة بنجاح',
            'data' => PropertyResource::collection($favorites),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total()
            ]
        ]);
    }

    /**
     * Toggle favorite status
     */
    public function toggle(Request $request, Property $property)
    {
        $user = $request->user();
        
        if ($user->favoriteProperties()->where('property_id', $property->id)->exists()) {
            // إزالة من المفضلة
            $user->favoriteProperties()->detach($property->id);
            $message = 'تم إزالة العقار من المفضلة';
            $is_favorite = false;
        } else {
            // إضافة إلى المفضلة
            $user->favoriteProperties()->attach($property->id);
            $message = 'تم إضافة العقار إلى المفضلة';
            $is_favorite = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'property_id' => $property->id,
                'is_favorite' => $is_favorite
            ]
        ]);
    }

    /**
     * Remove from favorites
     */
    public function remove(Request $request, Property $property)
    {
        $request->user()->favoriteProperties()->detach($property->id);

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة العقار من المفضلة',
            'data' => [
                'property_id' => $property->id,
                'is_favorite' => false
            ]
        ]);
    }

    /**
     * Check if property is favorited
     */
    public function check(Request $request, Property $property)
    {
        $isFavorite = $request->user()
            ->favoriteProperties()
            ->where('property_id', $property->id)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'property_id' => $property->id,
                'is_favorite' => $isFavorite
            ]
        ]);
    }
}
