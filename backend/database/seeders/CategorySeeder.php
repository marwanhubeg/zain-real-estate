<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'شقة',
                'name_en' => 'Apartment',
                'description_ar' => 'شقق سكنية للبيع والإيجار في الإسماعيلية',
                'description_en' => 'Residential apartments for sale and rent in Ismailia',
                'icon' => '🏢',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name_ar' => 'فيلا',
                'name_en' => 'Villa',
                'description_ar' => 'فلل مستقلة فاخرة مع حدائق خاصة',
                'description_en' => 'Luxury standalone villas with private gardens',
                'icon' => '🏠',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name_ar' => 'منزل',
                'name_en' => 'House',
                'description_ar' => 'منازل عائلية مستقلة',
                'description_en' => 'Independent family houses',
                'icon' => '🏡',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name_ar' => 'محل تجاري',
                'name_en' => 'Shop',
                'description_ar' => 'محلات تجارية للبيع والإيجار',
                'description_en' => 'Commercial shops for sale and rent',
                'icon' => '🏪',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name_ar' => 'مكتب',
                'name_en' => 'Office',
                'description_ar' => 'مكاتب إدارية للشركات',
                'description_en' => 'Administrative offices for companies',
                'icon' => '🏢',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name_ar' => 'أرض',
                'name_en' => 'Land',
                'description_ar' => 'أراضي للبناء والاستثمار',
                'description_en' => 'Lands for building and investment',
                'icon' => '🌲',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name_ar' => 'مزرعة',
                'name_en' => 'Farm',
                'description_ar' => 'مزارع وأراضي زراعية',
                'description_en' => 'Farms and agricultural lands',
                'icon' => '🌾',
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ تم إضافة ' . count($categories) . ' تصنيف بنجاح');
    }
}
