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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('garageid');
            $table->integer('orderid');
            $table->integer('invoiceid');
            $table->integer('user');
            $table->string('rzppayment');
            $table->decimal('amount', 10, 2);
            $table->string('signature')->nullable();
            $table->datetime('payment_time');
            $table->string('currency');
            $table->ipAddress('first_ip');
            $table->ipAddress('last_ip');
            $table->integer('added_by');
            $table->integer('edited_by');
            $table->enum('payment_mode', ['cash', 'online']);
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
        Schema::dropIfExists('payments');
    }
};
