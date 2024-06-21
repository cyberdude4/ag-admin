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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('garage');
            $table->integer('vehicle');
            $table->integer('user');
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2);
            $table->decimal('gst', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->integer('offer_id')->nullable();
            $table->enum('pick_vehicle', ['no', 'yes'])->default('no');
            $table->datetime('pickup_date')->nullable();
            $table->datetime('delivery_date')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->mediumText('notes')->nullable();
            $table->ipAddress('first_ip');
            $table->ipAddress('last_ip');
            $table->integer('added_by');
            $table->integer('edited_by');
            $table->enum('status', ['drafted', 'open', 'accepted', 'inprocess', 'cancel', 'rejected', 'ready', 'unpaid', 'complete'])->default('drafted');
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
        Schema::dropIfExists('orders');
    }
};
