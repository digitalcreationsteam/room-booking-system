<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('extra_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('charge_type'); // room_service, laundry, minibar, etc
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->date('charge_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('extra_charges');
    }
};
