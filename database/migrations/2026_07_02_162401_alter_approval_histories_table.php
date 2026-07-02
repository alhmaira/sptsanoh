<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_histories', function (Blueprint $table) {
            $table->string('user_name')->after('doc_number');
            $table->string('role_name')->after('user_name');
            $table->string('department')->after('role_name');
            $table->timestamp('approved_at')->nullable()->after('action');
        });
    }

    public function down(): void
    {
        Schema::table('approval_histories', function (Blueprint $table) {
            $table->dropColumn([
                'user_name',
                'role_name',
                'department',
                'approved_at',
            ]);
        });
    }
};
