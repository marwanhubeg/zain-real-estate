<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // الحقول الجديدة
            $table->foreignId('booking_id')->nullable()->constrained()->after('property_id');
            $table->string('title_ar')->nullable()->after('rating');
            $table->string('title_en')->nullable()->after('title_ar');
            $table->text('pros_ar')->nullable()->after('comment_en');
            $table->text('pros_en')->nullable()->after('pros_ar');
            $table->text('cons_ar')->nullable()->after('pros_en');
            $table->text('cons_en')->nullable()->after('cons_ar');
            $table->boolean('is_verified')->default(false)->after('cons_en');
            $table->boolean('is_approved')->default(false)->after('is_verified');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
            $table->integer('helpful_count')->default(0)->after('approved_by');
            $table->json('images')->nullable()->after('helpful_count');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'booking_id',
                'title_ar',
                'title_en',
                'pros_ar',
                'pros_en',
                'cons_ar',
                'cons_en',
                'is_verified',
                'is_approved',
                'approved_at',
                'approved_by',
                'helpful_count',
                'images'
            ]);
        });
    }
};
