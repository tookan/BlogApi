<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
public $timestamps=false;
protected $fillable = ['body','user_id','note_id'];

}
