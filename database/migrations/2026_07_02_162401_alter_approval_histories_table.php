<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('approval_histories', 'user_name')) {
                $table->string('user_name')->after('doc_number');
            }
            if (!Schema::hasColumn('approval_histories', 'role_name')) {
                $table->string('role_name')->after('user_name');
            }
            if (!Schema::hasColumn('approval_histories', 'department')) {
                $table->string('department')->after('role_name');
            }
            if (!Schema::hasColumn('approval_histories', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('action');
            }
        });
    }

    public function down(): void
    {
        Schema::table('approval_histories', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['user_name', 'role_name', 'department', 'approved_at'] as $column) {
                if (Schema::hasColumn('approval_histories', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};