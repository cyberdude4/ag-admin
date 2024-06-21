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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->integer('orderid');
            $table->integer('garageid');
            $table->integer('user');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2);
            $table->decimal('gst_amount', 10, 2);
            $table->integer('cgst');
            $table->integer('sgst');
            $table->string('currency');
            $table->ipAddress('first_ip');
            $table->ipAddress('last_ip');
            $table->integer('added_by');
            $table->integer('edited_by');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
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
        Schema::dropIfExists('invoices');
    }
};
