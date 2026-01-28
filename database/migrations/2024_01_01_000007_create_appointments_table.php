<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedInteger('buffer_at_booking')->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->decimal('price_at_booking', 10, 2);
            $table->decimal('deposit_at_booking', 10, 2);
            $table->decimal('voucher_discount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('locale', 2)->default('hu');
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();

            $table->index('start_time');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
