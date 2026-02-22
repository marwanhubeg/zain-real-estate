<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // الحقول الجديدة
            $table->string('booking_number')->unique()->nullable()->after('id');
            $table->foreignId('agent_id')->nullable()->constrained('users')->after('property_id');
            $table->integer('duration_minutes')->default(60)->after('booking_time');
            $table->integer('number_of_people')->default(1)->after('duration_minutes');
            $table->text('special_requests')->nullable()->after('notes');
            $table->string('cancellation_reason')->nullable()->after('special_requests');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->after('cancelled_at');
            $table->timestamp('confirmed_at')->nullable()->after('cancelled_by');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->after('confirmed_at');
            $table->timestamp('completed_at')->nullable()->after('confirmed_by');
            $table->timestamp('reminder_sent_at')->nullable()->after('completed_at');
            $table->integer('feedback_rating')->nullable()->after('reminder_sent_at');
            $table->text('feedback_comment')->nullable()->after('feedback_rating');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_number',
                'agent_id',
                'duration_minutes',
                'number_of_people',
                'special_requests',
                'cancellation_reason',
                'cancelled_at',
                'cancelled_by',
                'confirmed_at',
                'confirmed_by',
                'completed_at',
                'reminder_sent_at',
                'feedback_rating',
                'feedback_comment'
            ]);
        });
    }
};
