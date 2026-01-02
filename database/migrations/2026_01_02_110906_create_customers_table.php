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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('customer_name');
            $table->string('customer_mobile', 20)->unique();
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();

            $table->string('id_proof_type')->nullable();
            $table->string('id_proof_number')->nullable();

            $table->string('company_name')->nullable();
            $table->string('gst_number', 15)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
