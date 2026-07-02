<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qc', function (Blueprint $table) {

            $table->json('qualityProblems')->nullable();

            $table->string('has_problem')
                  ->default('no');

        });
    }

    public function down(): void
    {
        Schema::table('qc', function (Blueprint $table) {

            $table->dropColumn([
                'qualityProblems',
                'has_problem',
            ]);

        });
    }
};