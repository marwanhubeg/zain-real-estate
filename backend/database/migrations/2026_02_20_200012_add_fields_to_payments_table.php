<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // الحقول الجديدة
            $table->string('payment_number')->unique()->nullable()->after('id');
            $table->foreignId('property_id')->nullable()->constrained()->after('booking_id');
            $table->timestamp('payment_date')->useCurrent()->after('reference_number');
            $table->timestamp('paid_at')->nullable()->after('payment_date');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
            $table->string('refund_reason')->nullable()->after('refunded_at');
            $table->string('receipt_url')->nullable()->after('notes');
            $table->json('gateway_response')->nullable()->after('receipt_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_number',
                'property_id',
                'payment_date',
                'paid_at',
                'refunded_at',
                'refund_reason',
                'receipt_url',
                'gateway_response'
            ]);
        });
    }
};
