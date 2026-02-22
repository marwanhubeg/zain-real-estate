<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use App\Services\AI\RecommendationEngine;
use App\Services\AI\PredictionEngine;
use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    protected $recommendationEngine;
    protected $predictionEngine;

    public function __construct(
        RecommendationEngine $recommendationEngine,
        PredictionEngine $predictionEngine
    ) {
        $this->recommendationEngine = $recommendationEngine;
        $this->predictionEngine = $predictionEngine;
    }

    /**
     * توصيات مخصصة للمستخدم
     */
    public function personalized(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            // للمستخدمين غير المسجلين، استخدم التوصيات الشائعة
            return $this->trending($request);
        }
        
        $recommendations = $this->recommendationEngine
            ->getPersonalizedRecommendations($user->id, $request->get('limit', 10));
        
        return response()->json([
            'success' => true,
            'message' => 'توصيات مخصصة',
            'data' => PropertyResource::collection($recommendations),
            'meta' => [
                'total' => $recommendations->count(),
                'type' => 'personalized'
            ]
        ]);
    }

    /**
     * توصيات مشابهة لعقار محدد
     */
    public function similar($propertyId, Request $request)
    {
        $property = Property::find($propertyId);
        
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'العقار غير موجود'
            ], 404);
        }
        
        $similar = $this->recommendationEngine
            ->getSimilarProperties($propertyId, $request->get('limit', 6));
        
        return response()->json([
            'success' => true,
            'message' => 'عقارات مشابهة',
            'data' => PropertyResource::collection($similar),
            'meta' => [
                'total' => $similar->count(),
                'type' => 'similar',
                'property_id' => $propertyId
            ]
        ]);
    }

    /**
     * توصيات شائعة
     */
    public function trending(Request $request)
    {
        $trending = $this->recommendationEngine
            ->getTrendingProperties($request->get('limit', 10));
        
        return response()->json([
            'success' => true,
            'message' => 'العقارات الأكثر طلباً',
            'data' => PropertyResource::collection($trending),
            'meta' => [
                'total' => $trending->count(),
                'type' => 'trending'
            ]
        ]);
    }

    /**
     * تنبؤ بأسعار العقارات
     */
    public function predictPrices(Request $request)
    {
        $request->validate([
            'location_id' => 'nullable|exists:locations,id',
            'months' => 'nullable|integer|min:1|max:24'
        ]);

        $prediction = $this->predictionEngine
            ->predictPriceTrend($request->location_id, $request->get('months', 12));

        return response()->json([
            'success' => true,
            'message' => 'تنبؤات الأسعار',
            'data' => $prediction
        ]);
    }

    /**
     * تنبؤ بموسمية الطلب
     */
    public function predictDemand(Request $request)
    {
        $request->validate([
            'location_id' => 'nullable|exists:locations,id'
        ]);

        $seasonality = $this->predictionEngine
            ->predictDemandSeasonality($request->location_id);

        return response()->json([
            'success' => true,
            'message' => 'توقعات الطلب الموسمي',
            'data' => $seasonality
        ]);
    }

    /**
     * توصيات حسب الميزانية
     */
    public function byBudget(Request $request)
    {
        $request->validate([
            'budget' => 'required|numeric|min:1000',
            'purpose' => 'sometimes|in:sale,rent'
        ]);

        $query = Property::where('status', 'available');
        
        if ($request->has('purpose')) {
            $query->where('type', $request->purpose);
        }
        
        // توصيات حسب الميزانية
        if ($request->purpose === 'rent') {
            $query->where('price', '<=', $request->budget);
        } else {
            $query->where('price', '<=', $request->budget);
        }
        
        $recommendations = $query->orderBy('price', 'desc')
            ->with(['category', 'location'])
            ->limit($request->get('limit', 10))
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'توصيات حسب الميزانية',
            'data' => PropertyResource::collection($recommendations),
            'meta' => [
                'total' => $recommendations->count(),
                'type' => 'budget',
                'budget' => $request->budget,
                'purpose' => $request->purpose ?? 'all'
            ]
        ]);
    }
}
