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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->integer('part_number')->unique();
            $table->integer('garage_id');
            $table->integer('category');
            $table->integer('vehicle_brand');
            $table->integer('vehicle_model');
            $table->integer('part_brand');
            $table->string('part_model');
            $table->string('part_type')->nullable();
            $table->string('part_slug')->nullable();
            $table->mediumText('part_description')->nullable();
            $table->string('part_warranty')->default(0);
            $table->string('part_guarantee')->default(0);
            $table->decimal('purchase_price');
            $table->decimal('sale_price');
            $table->integer('discount_percent')->default(0);
            $table->integer('tax_percent')->default(0);
            $table->integer('added_by');
            $table->integer('edited_by');
            $table->enum('status', [0, 1])->default(1);
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
        Schema::dropIfExists('parts');
    }
};
