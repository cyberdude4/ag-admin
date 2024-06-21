<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->integer('garage');
            $table->integer('category');
            $table->integer('make');
            $table->integer('model');
            $table->date('purchase_date');
            $table->string('engine_number');
            $table->string('chasis_number');
            $table->string('vehicle_number');
            $table->string('fuel_level');
            $table->string('odometer');
            $table->integer('added_by');
            $table->integer('edited_by');
            $table->integer('owner_id');
            $table->enum('status', [0,1])->default(1);
            $table->enum('deleted', [0,1])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};
