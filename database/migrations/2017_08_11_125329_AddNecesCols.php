<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNecesCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_profiles', function (Blueprint $table){
           $table->string('avatar');
        });
        Schema::table('users', function (Blueprint $table){
            $table->string('status');
        });
        Schema::table('notes', function (Blueprint $table){
            $table->string('big_img');
            $table->string('small_img');
        });
    }

    public function down()
    {

    }
}
