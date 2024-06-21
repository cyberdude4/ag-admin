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
        Schema::create('orderworkflow', function (Blueprint $table) {
            $table->id();
            $table->integer('order');
            $table->integer('user');
            $table->enum('type', ['work', 'bill'])->default('work');
            $table->timestamp('start')->default(now());
            $table->timestamp('end')->default(now());
            $table->enum('status', ['0', '1'])->default(0);
            $table->enum('deleted', ['0', '1'])->default(0);
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
        Schema::dropIfExists('orderworkflow');
    }
};
