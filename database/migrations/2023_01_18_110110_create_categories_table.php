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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('garage');
            $table->uuid('cat_uid');
            $table->string('cat_slug');
            $table->integer('parent_cat')->default(0); // 0 - Root/Main
            $table->string('cat_name');
            $table->string('picture');
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
        Schema::dropIfExists('categories');
    }
};
