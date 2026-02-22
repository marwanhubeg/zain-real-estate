<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description_ar');
            $table->text('description_en')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('area', 8, 2);
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('floor')->nullable();
            $table->enum('type', ['sale', 'rent'])->default('sale');
            $table->enum('status', ['available', 'pending', 'sold', 'rented'])->default('available');
            $table->enum('finishing_type', ['finished', 'semi-finished', 'unfinished'])->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->string('payment_method')->default('cash');
            $table->integer('installment_years')->nullable();
            $table->decimal('down_payment', 10, 2)->nullable();
            $table->decimal('monthly_payment', 10, 2)->nullable();
            
            // العلاقات
            $table->foreignId('category_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('location_id')->nullable()->constrained();
            
            // الموقع
            $table->string('location')->nullable();
            $table->string('address_ar');
            $table->string('address_en')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // وسائط
            $table->string('video_url')->nullable();
            $table->string('virtual_tour_url')->nullable();
            
            // SEO
            $table->string('meta_title_ar')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->text('meta_description_ar')->nullable();
            $table->text('meta_description_en')->nullable();
            
            // التواريخ
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
