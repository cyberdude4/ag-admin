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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('garage_id');
            $table->enum('user_role', ['1', '2', '3', '4', '5'])->default(3);
            $table->integer('added_by');
            $table->integer('edited_by');
            $table->string('created_ip');
            $table->string('last_ip');
            $table->enum('status', [0, 1])->default(1);
            $table->enum('deleted', [0, 1])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * User role 1 = Admin
     * User role 2 = Billing
     * User role 3 = Mechanic
     * 
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
