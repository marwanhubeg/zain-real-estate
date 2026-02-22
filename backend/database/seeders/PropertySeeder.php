<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // التأكد من وجود مستخدم
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'مسوق عقاري',
                'email' => 'agent@example.com',
                'phone' => '01234567890',
                'password' => bcrypt('password'),
                'role' => 'agent',
                'is_active' => true,
            ]);
        }

        // التأكد من وجود تصنيفات
        $categoryApartment = Category::where('name_ar', 'شقة')->first();
        $categoryVilla = Category::where('name_ar', 'فيلا')->first();
        $categoryHouse = Category::where('name_ar', 'منزل')->first();

        $properties = [
            [
                'title_ar' => 'فيلا فاخرة في حي النخيل',
                'title_en' => 'Luxury Villa in Al Nakheel',
                'slug' => Str::slug('فيلا فاخرة في حي النخيل'),
                'description_ar' => 'فيلا فاخرة بإطلالة رائعة على البحيرة، مسبح خاص، حديقة واسعة، 5 غرف نوم، 6 حمامات، تشطيب سوبر لوكس',
                'description_en' => 'Luxury villa with stunning lake view, private pool, large garden, 5 bedrooms, 6 bathrooms, super lux finishing',
                'price' => 3500000,
                'area' => 450,
                'bedrooms' => 5,
                'bathrooms' => 6,
                'type' => 'sale',
                'category_id' => $categoryVilla?->id ?? 1,
                'user_id' => $user->id,
                'location' => 'حي النخيل',
                'address_ar' => 'شارع 18، حي النخيل، الإسماعيلية',
                'address_en' => 'Street 18, Al Nakheel, Ismailia',
                'status' => 'available',
                'is_featured' => true,
                'finishing_type' => 'finished',
                'payment_method' => 'cash',
            ],
            [
                'title_ar' => 'شقة حديثة في الحي التجاري',
                'title_en' => 'Modern Apartment in Business District',
                'slug' => Str::slug('شقة حديثة في الحي التجاري'),
                'description_ar' => 'شقة عصرية بالقرب من الخدمات، تشطيب سوبر لوكس، 3 غرف نوم، 2 حمامات',
                'description_en' => 'Modern apartment near services, super lux finishing, 3 bedrooms, 2 bathrooms',
                'price' => 850000,
                'area' => 140,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'type' => 'sale',
                'category_id' => $categoryApartment?->id ?? 1,
                'user_id' => $user->id,
                'location' => 'الحي التجاري',
                'address_ar' => 'شارع قناة السويس، الإسماعيلية',
                'address_en' => 'Suez Canal Street, Ismailia',
                'status' => 'available',
                'is_featured' => false,
                'finishing_type' => 'finished',
                'payment_method' => 'installment',
                'installment_years' => 7,
                'down_payment' => 170000,
                'monthly_payment' => 8500,
            ],
            [
                'title_ar' => 'دوبلكس للبيع في كمبوند النخبة',
                'title_en' => 'Duplex for Sale in Elite Compound',
                'slug' => Str::slug('دوبلكس للبيع في كمبوند النخبة'),
                'description_ar' => 'دوبلكس فاخر في كمبوند النخبة، 3 غرف نوم، رووف خاص، تشطيب حديث',
                'description_en' => 'Luxury duplex in Elite Compound, 3 bedrooms, private roof, modern finishing',
                'price' => 1250000,
                'area' => 220,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'type' => 'sale',
                'category_id' => $categoryApartment?->id ?? 1,
                'user_id' => $user->id,
                'location' => 'كمبوند النخبة',
                'address_ar' => 'مدينة الإسماعيلية الجديدة',
                'address_en' => 'New Ismailia City',
                'status' => 'available',
                'is_featured' => true,
                'finishing_type' => 'finished',
                'payment_method' => 'cash',
            ],
            [
                'title_ar' => 'شقة للإيجار بجوار الجامعة',
                'title_en' => 'Apartment for Rent Near University',
                'slug' => Str::slug('شقة للإيجار بجوار الجامعة'),
                'description_ar' => 'شقة مؤثثة للإيجار، مناسبة للطلاب والأكاديميين، 2 غرف نوم',
                'description_en' => 'Furnished apartment for rent, suitable for students and academics, 2 bedrooms',
                'price' => 8500,
                'area' => 100,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'type' => 'rent',
                'category_id' => $categoryApartment?->id ?? 1,
                'user_id' => $user->id,
                'location' => 'الجامعة',
                'address_ar' => 'شارع الجامعة، الإسماعيلية',
                'address_en' => 'University Street, Ismailia',
                'status' => 'available',
                'is_featured' => false,
                'finishing_type' => 'semi-finished',
                'payment_method' => 'cash',
            ],
            [
                'title_ar' => 'فيلا بالإيجار السياحي',
                'title_en' => 'Villa for Tourist Rent',
                'slug' => Str::slug('فيلا بالإيجار السياحي'),
                'description_ar' => 'فيلا مفروشة فاخرة للإيجار السياحي، مسبح، حديقة، 4 غرف نوم',
                'description_en' => 'Luxury furnished villa for tourist rent, pool, garden, 4 bedrooms',
                'price' => 15000,
                'area' => 350,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'type' => 'rent',
                'category_id' => $categoryVilla?->id ?? 1,
                'user_id' => $user->id,
                'location' => 'منتجع الجزيرة',
                'address_ar' => 'شارع البحيرة، الإسماعيلية',
                'address_en' => 'Lake Street, Ismailia',
                'status' => 'available',
                'is_featured' => true,
                'finishing_type' => 'finished',
                'payment_method' => 'cash',
            ],
            [
                'title_ar' => 'منزل عائلي في حي السلام',
                'title_en' => 'Family House in Al Salam District',
                'slug' => Str::slug('منزل عائلي في حي السلام'),
                'description_ar' => 'منزل عائلي مستقل، حديقة خاصة، 4 غرف نوم، 3 حمامات',
                'description_en' => 'Independent family house, private garden, 4 bedrooms, 3 bathrooms',
                'price' => 950000,
                'area' => 200,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'type' => 'sale',
                'category_id' => $categoryHouse?->id ?? 1,
                'user_id' => $user->id,
                'location' => 'حي السلام',
                'address_ar' => 'شارع 15، حي السلام، الإسماعيلية',
                'address_en' => 'Street 15, Al Salam, Ismailia',
                'status' => 'available',
                'is_featured' => false,
                'finishing_type' => 'semi-finished',
                'payment_method' => 'installment',
                'installment_years' => 5,
                'down_payment' => 200000,
                'monthly_payment' => 13500,
            ],
        ];

        foreach ($properties as $property) {
            Property::create($property);
        }

        $this->command->info('✅ تم إضافة ' . count($properties) . ' عقار تجريبي بنجاح');
    }
}
