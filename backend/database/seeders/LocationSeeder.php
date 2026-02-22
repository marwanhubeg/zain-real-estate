<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'city_ar' => 'الإسماعيلية',
                'city_en' => 'Ismailia',
                'district_ar' => 'حي النخيل',
                'district_en' => 'Al Nakheel',
                'latitude' => 30.6046,
                'longitude' => 32.2723,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'city_ar' => 'الإسماعيلية',
                'city_en' => 'Ismailia',
                'district_ar' => 'الحي التجاري',
                'district_en' => 'Business District',
                'latitude' => 30.5946,
                'longitude' => 32.2823,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'city_ar' => 'الإسماعيلية',
                'city_en' => 'Ismailia',
                'district_ar' => 'كمبوند النخبة',
                'district_en' => 'Elite Compound',
                'latitude' => 30.6146,
                'longitude' => 32.2923,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'city_ar' => 'الإسماعيلية',
                'city_en' => 'Ismailia',
                'district_ar' => 'الجامعة',
                'district_en' => 'University',
                'latitude' => 30.5846,
                'longitude' => 32.2623,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'city_ar' => 'الإسماعيلية',
                'city_en' => 'Ismailia',
                'district_ar' => 'منتجع الجزيرة',
                'district_en' => 'Al Jazeera Resort',
                'latitude' => 30.6246,
                'longitude' => 32.3023,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }

        $this->command->info('✅ تم إضافة ' . count($locations) . ' موقع بنجاح');
    }
}
