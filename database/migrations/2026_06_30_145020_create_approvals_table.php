<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {

            $table->id();

            $table->string('doc_number');

            $table->string('status')->default('pending');

            $table->integer('current_step')->default(1);

            $table->string('submitted_by')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->string('current_department')->nullable();

            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};