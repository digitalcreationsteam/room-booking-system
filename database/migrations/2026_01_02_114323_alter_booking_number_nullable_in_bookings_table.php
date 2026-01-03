<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Sirf nullable change karo, unique nahi
            $table->string('booking_number')
                ->nullable()
                ->change();
            $table->string('customer_name')
                ->nullable()
                ->change();
            $table->string('customer_mobile')
                ->nullable()
                ->change();
            $table->text('customer_address')
                ->nullable()
                ->change();
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_number')
                ->nullable(false)
                ->change();
            $table->string('customer_name')
                ->nullable(false)
                ->change();
            $table->string('customer_mobile')
                ->nullable(false)
                ->change();
            $table->text('customer_address')
                ->nullable(false)
                ->change();
        });
    }
};


