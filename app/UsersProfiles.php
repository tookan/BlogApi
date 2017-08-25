<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersProfiles extends Model
{
    public $timestamps = false;
    protected $table='users_profiles';

    protected $fillable = ['first_name','last_name','about','user_id','middle_name','avatar'];

}
