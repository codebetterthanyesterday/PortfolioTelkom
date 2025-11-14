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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->comment('mention, comment, reply, etc');
            $table->morphs('notifiable'); // Polymorphic relation (creates notifiable_type, notifiable_id, and index)
            $table->text('data')->nullable()->comment('JSON data untuk info tambahan');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            // Note: morphs() already creates index for notifiable_type and notifiable_id
            $table->index('read_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
