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
           $table->string('avatar')->default('');
        });
    
        Schema::table('notes', function (Blueprint $table){
            $table->string('big_img')->default('');
            $table->string('small_img')->default('');
        });
    }

    public function down()
    {

    }
}
