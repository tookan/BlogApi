<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NotesTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes',function(Blueprint $table){
            $table->increments('id');
            $table->string('title','90');
            $table->text('body','2500');
            $table->unsignedInteger('user_id');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       // Drop::table('notes');
    }
}
