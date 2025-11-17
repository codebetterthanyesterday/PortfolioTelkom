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
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('data');
            $table->enum('category', ['social', 'project', 'team', 'system'])->default('system')->after('priority');
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'priority']);
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'read_at']);
            $table->dropIndex(['user_id', 'type']);
            $table->dropIndex(['user_id', 'priority']);
            $table->dropIndex(['user_id', 'category']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropColumn(['priority', 'category']);
        });
    }
};
