<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class note extends Model
{
protected $fillable= [
    'title','body'
];
public function user(){
    return $this->belongsTo('App\User');
}
public function comment(){
    return $this->hasMany('App\comment');
}
}
