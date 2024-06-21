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
        Schema::create('garages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->nullable();
            $table->integer('user_id');
            $table->string('slug');
            $table->string('name');
            $table->integer('country');
            $table->integer('state');
            $table->integer('city');
            $table->string('street');
            $table->string('pincode');
            $table->double('lat')->nullable();
            $table->double('long')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('pancard')->nullable();
            $table->string('refer_by')->nullable();
            $table->string('refer_id')->nullable();
            $table->integer('added_by');
            $table->integer('edited_by');
            $table->string('created_ip');
            $table->string('last_ip');
            $table->enum('status', [0, 1, 2])->default(0);
            $table->enum('deleted', [0, 1])->default(0);
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
        Schema::dropIfExists('garages');
    }
};
