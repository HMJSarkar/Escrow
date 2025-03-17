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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['money', 'crypto', 'physical', 'service', 'other'])->default('physical');
            $table->decimal('value', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('condition')->nullable(); // for physical items: new, used, etc.
            $table->string('location')->nullable(); // for physical items
            $table->json('images')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'exchanged'])->default('pending');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('approval_notes')->nullable();
            $table->json('metadata')->nullable(); // additional data based on asset type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};