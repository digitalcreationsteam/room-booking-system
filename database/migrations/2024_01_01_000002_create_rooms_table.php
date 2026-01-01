<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->integer('floor_number');
            $table->decimal('base_price', 10, 2);
            $table->decimal('gst_percentage', 5, 2)->default(12.00);
            $table->decimal('service_tax_percentage', 5, 2)->default(0.00);
            $table->decimal('other_charges', 10, 2)->default(0.00);
            $table->text('amenities')->nullable();
            $table->enum('status', ['available', 'booked', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
