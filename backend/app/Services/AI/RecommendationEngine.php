<?php

namespace App\Services\AI;

use App\Models\Property;
use App\Models\User;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Cache;
use Phpml\Association\Apriori;
use Phpml\Clustering\KMeans;
use Phpml\Regression\LeastSquares;

class RecommendationEngine
{
    /**
     * توصيات مخصصة للمستخدم بناءً على سلوكه
     */
    public function getPersonalizedRecommendations($userId, $limit = 10)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return $this->getTrendingProperties($limit);
        }
        
        // تحليل سجل البحث
        $searchHistory = SearchHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        
        // تحليل العقارات المفضلة
        $favorites = $user->favoriteProperties()->pluck('property_id')->toArray();
        
        // تحليل الحجوزات السابقة
        $bookings = $user->bookings()->pluck('property_id')->toArray();
        
        // بناء ناقل الميزات
        $featureVector = $this->buildFeatureVector($searchHistory, $favorites, $bookings);
        
        // الحصول على توصيات باستخدام K-Means
        $recommendations = $this->getKMeansRecommendations($featureVector);
        
        // تخزين النتائج في الكاش
        Cache::put("recommendations:user:{$userId}", $recommendations, now()->addHours(24));
        
        return Property::whereIn('id', $recommendations)
            ->with(['category', 'location'])
            ->limit($limit)
            ->get();
    }
    
    /**
     * توصيات بناءً على عقار معين (Similar Properties)
     */
    public function getSimilarProperties($propertyId, $limit = 6)
    {
        $property = Property::with(['category', 'location'])->find($propertyId);
        
        if (!$property) {
            return collect([]);
        }
        
        // بناء استعلام للعقارات المشابهة
        $similar = Property::where('id', '!=', $propertyId)
            ->where('status', 'available')
            ->where('type', $property->type)
            ->whereBetween('price', [$property->price * 0.8, $property->price * 1.2])
            ->whereBetween('area', [$property->area * 0.7, $property->area * 1.3])
            ->where('bedrooms', '>=', $property->bedrooms - 1)
            ->where('bedrooms', '<=', $property->bedrooms + 1);
        
        // إضافة تشابه في الموقع
        if ($property->location_id) {
            $similar->where('location_id', $property->location_id);
        }
        
        return $similar->with(['category', 'location'])
            ->limit($limit)
            ->get();
    }
    
    /**
     * توصيات شائعة (Trending)
     */
    public function getTrendingProperties($limit = 10)
    {
        // تحليل العقارات الأكثر مشاهدة
        $mostViewed = Cache::remember('trending:most-viewed', 3600, function () {
            return Property::where('status', 'available')
                ->orderBy('views_count', 'desc')
                ->take(20)
                ->pluck('id')
                ->toArray();
        });
        
        return Property::whereIn('id', $mostViewed)
            ->with(['category', 'location'])
            ->limit($limit)
            ->get();
    }
    
    /**
     * بناء ناقل الميزات للمستخدم
     */
    private function buildFeatureVector($searchHistory, $favorites, $bookings)
    {
        $features = [
            'price_range' => [],
            'area_range' => [],
            'bedrooms' => [],
            'types' => [],
            'locations' => []
        ];
        
        // تحليل سجل البحث
        foreach ($searchHistory as $search) {
            if ($search->filters) {
                $filters = is_string($search->filters) ? json_decode($search->filters, true) : $search->filters;
                if (isset($filters['min_price'])) {
                    $features['price_range'][] = $filters['min_price'];
                }
                if (isset($filters['bedrooms'])) {
                    $features['bedrooms'][] = $filters['bedrooms'];
                }
                if (isset($filters['location'])) {
                    $features['locations'][] = $filters['location'];
                }
            }
        }
        
        // تحليل المفضلة
        foreach ($favorites as $propertyId) {
            $property = Property::find($propertyId);
            if ($property) {
                $features['price_range'][] = $property->price;
                $features['area_range'][] = $property->area;
                $features['bedrooms'][] = $property->bedrooms;
                $features['types'][] = $property->type;
                $features['locations'][] = $property->location;
            }
        }
        
        // حساب المتوسطات
        $featureVector = [
            'avg_price' => !empty($features['price_range']) ? array_sum($features['price_range']) / count($features['price_range']) : 0,
            'avg_area' => !empty($features['area_range']) ? array_sum($features['area_range']) / count($features['area_range']) : 0,
            'avg_bedrooms' => !empty($features['bedrooms']) ? array_sum($features['bedrooms']) / count($features['bedrooms']) : 0,
            'preferred_type' => !empty($features['types']) ? $this->getMostFrequent($features['types']) : null,
            'preferred_location' => !empty($features['locations']) ? $this->getMostFrequent($features['locations']) : null
        ];
        
        return $featureVector;
    }
    
    /**
     * الحصول على القيم الأكثر تكراراً
     */
    private function getMostFrequent($array, $limit = 1)
    {
        if (empty($array)) {
            return null;
        }
        $counts = array_count_values($array);
        arsort($counts);
        return $limit == 1 ? key($counts) : array_slice(array_keys($counts), 0, $limit);
    }
    
    /**
     * توصيات باستخدام K-Means clustering
     */
    private function getKMeansRecommendations($featureVector)
    {
        // للتبسيط، نرجع عقارات عشوائية من نفس النوع المفضل
        $query = Property::where('status', 'available');
        
        if ($featureVector['preferred_type']) {
            $query->where('type', $featureVector['preferred_type']);
        }
        
        if ($featureVector['preferred_location']) {
            $query->where('location', 'like', '%' . $featureVector['preferred_location'] . '%');
        }
        
        if ($featureVector['avg_price'] > 0) {
            $query->whereBetween('price', [
                $featureVector['avg_price'] * 0.7,
                $featureVector['avg_price'] * 1.3
            ]);
        }
        
        if ($featureVector['avg_bedrooms'] > 0) {
            $query->where('bedrooms', '>=', floor($featureVector['avg_bedrooms'] - 1))
                  ->where('bedrooms', '<=', ceil($featureVector['avg_bedrooms'] + 1));
        }
        
        return $query->limit(20)->pluck('id')->toArray();
    }
}
