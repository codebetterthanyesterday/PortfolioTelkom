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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['individual','school_group','personal_outside_school','team_outside_school'])->default('individual');
            $table->unsignedBigInteger('leader_id'); // Ketua
            $table->unsignedBigInteger('created_by'); // may be same as leader_id
            $table->enum('status', ['draft','published','archived'])->default('draft');
            $table->enum('visibility', ['public','private'])->default('public');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('leader_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('leader_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
