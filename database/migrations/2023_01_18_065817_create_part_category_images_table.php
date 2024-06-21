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
        Schema::create('part_category_images', function (Blueprint $table) {
            $table->id();
            $table->integer('cat_id');
            $table->string('image');
            $table->string('filepath');
            $table->integer('added_by');
            $table->integer('edited_by');
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
        Schema::dropIfExists('part_category_images');
    }
};
