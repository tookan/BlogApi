<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullableImgs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notes', function(Blueprint $table){
            $table->string('big_img')->nullable()->change();
            $table->string('small_img')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.php
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
