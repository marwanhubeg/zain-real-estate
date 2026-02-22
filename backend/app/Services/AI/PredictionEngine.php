<?php

namespace App\Services\AI;

use App\Models\Property;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PredictionEngine
{
    /**
     * تنبؤ بأسعار العقارات
     */
    public function predictPriceTrend($locationId = null, $months = 12)
    {
        $cacheKey = "price_prediction:" . ($locationId ?? 'all') . ":{$months}";
        
        return Cache::remember($cacheKey, 3600, function () use ($locationId, $months) {
            // جمع البيانات التاريخية
            $query = Property::where('type', 'sale');
            
            if ($locationId) {
                $query->where('location_id', $locationId);
            }
            
            $historicalData = $query->orderBy('created_at')
                ->get(['price', 'created_at'])
                ->groupBy(function ($item) {
                    return $item->created_at->format('Y-m');
                })
                ->map(function ($group) {
                    return $group->avg('price');
                });
            
            if (empty($historicalData)) {
                return [
                    'current' => 0,
                    'predictions' => [],
                    'trend' => 'stable'
                ];
            }
            
            // تنبؤ بسيط بناءً على المتوسط
            $avgIncrease = $this->calculateAverageIncrease($historicalData);
            $lastPrice = end($historicalData);
            
            $predictions = [];
            for ($i = 1; $i <= $months; $i++) {
                $predictedPrice = $lastPrice * (1 + ($avgIncrease * $i));
                $predictions[] = [
                    'month' => Carbon::now()->addMonths($i)->format('Y-m'),
                    'predicted_price' => round($predictedPrice, 2)
                ];
            }
            
            // تحليل الاتجاه
            $trend = $this->analyzeTrend($predictions);
            
            return [
                'current' => round($lastPrice, 2),
                'predictions' => $predictions,
                'trend' => $trend,
                'confidence' => 'medium'
            ];
        });
    }
    
    /**
     * حساب متوسط الزيادة
     */
    private function calculateAverageIncrease($historicalData)
    {
        $values = array_values($historicalData);
        if (count($values) < 2) {
            return 0.01; // 1% افتراضي
        }
        
        $totalIncrease = 0;
        for ($i = 1; $i < count($values); $i++) {
            $increase = ($values[$i] - $values[$i-1]) / $values[$i-1];
            $totalIncrease += $increase;
        }
        
        return $totalIncrease / (count($values) - 1);
    }
    
    /**
     * تحليل الاتجاه
     */
    private function analyzeTrend($predictions)
    {
        if (empty($predictions)) {
            return 'stable';
        }
        
        $firstPrice = $predictions[0]['predicted_price'];
        $lastPrice = end($predictions)['predicted_price'];
        
        $change = ($lastPrice - $firstPrice) / $firstPrice;
        
        if ($change > 0.1) return 'rising';
        if ($change < -0.1) return 'falling';
        return 'stable';
    }
    
    /**
     * تنبؤ بموسمية الطلب
     */
    public function predictDemandSeasonality($locationId = null)
    {
        $cacheKey = "demand_seasonality:" . ($locationId ?? 'all');
        
        return Cache::remember($cacheKey, 86400, function () use ($locationId) {
            $query = Booking::query();
            
            if ($locationId) {
                $query->whereHas('property', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                });
            }
            
            $bookingsByMonth = $query->selectRaw('
                    strftime("%m", created_at) as month,
                    count(*) as booking_count
                ')
                ->where('created_at', '>=', Carbon::now()->subYears(2))
                ->groupBy('month')
                ->get()
                ->pluck('booking_count', 'month')
                ->toArray();
            
            $seasonality = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
                $count = $bookingsByMonth[$monthStr] ?? 0;
                $maxCount = max($bookingsByMonth) ?: 1;
                
                $seasonality[$month] = [
                    'month_name' => $this->getMonthName($month),
                    'demand_level' => $this->getDemandLevel($count, $maxCount),
                    'booking_count' => $count
                ];
            }
            
            return $seasonality;
        });
    }
    
    /**
     * الحصول على اسم الشهر
     */
    private function getMonthName($month)
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        return $months[$month] ?? '';
    }
    
    /**
     * حساب مستوى الطلب
     */
    private function getDemandLevel($count, $maxCount)
    {
        $ratio = $count / $maxCount;
        
        if ($ratio >= 0.8) return 'high';
        if ($ratio >= 0.5) return 'medium';
        if ($ratio >= 0.2) return 'low';
        return 'very_low';
    }
}
