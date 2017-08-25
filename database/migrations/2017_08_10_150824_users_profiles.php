<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersProfiles extends Migration
{

    public function up()
    {
        Schema::create('users_profiles',function (Blueprint $table){
           $table->bigIncrements('id');
           $table->bigInteger('user_id');
           $table->string('name',20);
           $table->string('last_name',30);
           $table->string('middle_name',30);
           $table->string('about',800);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
