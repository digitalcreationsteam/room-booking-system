<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('hotel_name');
            $table->text('hotel_address')->nullable();
            $table->string('hotel_gst_number')->nullable();
            $table->string('hotel_mobile')->nullable();
            $table->string('hotel_telephone')->nullable();
            $table->string('hotel_email')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
