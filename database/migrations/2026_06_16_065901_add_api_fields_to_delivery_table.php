<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery', function (Blueprint $table) {
            $table->string('bp_code')->nullable()->after('id');
            $table->integer('period_month')->nullable()->after('bp_code');
            $table->integer('period_year')->nullable()->after('period_month');
            $table->integer('total_delay_days')->nullable();
            $table->integer('total_deliveries')->nullable();
            $table->integer('on_time_deliveries')->nullable();
            $table->bigInteger('total_dn_qty')->nullable();
            $table->bigInteger('total_receipt_qty')->nullable();
            $table->decimal('fulfillment_percentage', 8, 2)->nullable();
            $table->integer('final_score')->nullable();
            $table->decimal('cumulative_final_score', 8, 2)->nullable();
            $table->string('cumulative_performance_grade')->nullable();
            $table->integer('cumulative_ranking')->nullable();
            $table->string('performance_grade')->nullable();
            $table->integer('ranking')->nullable();
            $table->string('category')->nullable();
            $table->string('supplier_name')->nullable();
            $table->boolean('synced_from_api')->default(false);
            $table->timestamp('last_synced_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('delivery', function (Blueprint $table) {
            $table->dropColumn([
                'bp_code', 'period_month', 'period_year', 'total_delay_days',
                'total_deliveries', 'on_time_deliveries', 'total_dn_qty',
                'total_receipt_qty', 'fulfillment_percentage', 'final_score',
                'cumulative_final_score', 'cumulative_performance_grade',
                'cumulative_ranking', 'performance_grade', 'ranking',
                'category', 'supplier_name', 'synced_from_api', 'last_synced_at',
            ]);
        });
    }
};