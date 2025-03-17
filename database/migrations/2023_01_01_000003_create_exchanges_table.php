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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('responder_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('offered_asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('requested_asset_id')->nullable()->constrained('assets')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->enum('escrow_status', ['waiting_initiator', 'waiting_responder', 'both_deposited', 'released', 'refunded'])->nullable();
            $table->decimal('fee_amount', 15, 2)->nullable();
            $table->string('fee_currency', 10)->default('USD');
            $table->boolean('fee_paid')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};