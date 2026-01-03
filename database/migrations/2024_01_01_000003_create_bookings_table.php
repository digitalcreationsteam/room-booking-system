<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Booking Identity
            $table->string('booking_number')->unique();

            // Customer Details
            $table->string('customer_name')->nullable();
            $table->string('customer_mobile')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('id_proof_type')->nullable();
            $table->string('id_proof_number')->nullable();
            $table->string('company_name')->nullable();
            $table->string('gst_number')->nullable();

            // Booking Dates
            $table->dateTime('check_in')->index();
            $table->dateTime('check_out')->index();
            $table->integer('number_of_adults');
            $table->integer('number_of_children');
            $table->integer('number_of_nights');

            // Amounts
            $table->decimal('room_charges', 10, 2)->default(0);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('service_tax', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0);
            $table->decimal('extra_charges', 10, 2)->default(0);

            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('advance_payment', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);

            // Payment
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->enum('payment_mode', ['cash', 'card', 'upi', 'bank_transfer'])->nullable();

            // Booking Lifecycle
            $table->enum('booking_status', [
                'confirmed',
                'checked_in',
                'checked_out',
                'cancelled'
            ])->default('confirmed')->index();

            $table->text('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);

            // Meta
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
