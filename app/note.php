<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class note extends Model
{
protected $fillable= [
    'title','body'
];
public function user(){
    return $this->belongsTo(User);
}
}
