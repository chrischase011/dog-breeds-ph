<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDogChoice extends Model
{
    protected $table = 'user_dog_choices';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
