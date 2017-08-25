<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class note extends Model
{
public $timestamps = true;
protected $fillable= [
    'title','body','user_id'
];
public function user(){
    return $this->belongsTo('App\User');
}
public function comment(){
    return $this->hasMany('App\comment');
}
}
