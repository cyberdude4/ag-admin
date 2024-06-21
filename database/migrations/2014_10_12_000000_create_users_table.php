<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use App\Traits\UUID;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    // use UUID;

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            
            $table->id();
            $table->uuid('uid')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('mobile')->unique();
            $table->string('email')->unique();
            $table->date('dob')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('refreshtoken')->nullable();
            $table->string('first_ip', 50);
            $table->string('last_ip', 50);
            $table->timestamp('first_login');
            $table->timestamp('last_login')->nullable();
            $table->enum('user_type', [0, 1, 2, 3, 4])->default(4);
            $table->enum('status', [0, 1])->default(1);
            $table->enum('deleted', [0, 1])->default(0);
            $table->rememberToken();
            $table->timestamps();

        });
    }

    /** User type 
    * 
    *   0 = Super admin
    *   1 = Normal Admin
    *   2 = Garage Vendor
    *   3 = Garage management user
    *   4 = End User
    *  
    * /

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
