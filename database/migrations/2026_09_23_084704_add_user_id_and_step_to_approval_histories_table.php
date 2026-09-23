<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('approval_histories', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->after('doc_number');
        $table->integer('step')->nullable()->after('user_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_histories', function (Blueprint $table) {
            //
        });
    }
};
