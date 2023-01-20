<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model
{
    use HasFactory;

    public function UserDoingTheFollowing(){
        return $this->BelongsTo(User::class,'user_id');
    }
    public function UserBeingTheFollowed(){
        return $this->BelongsTo(User::class,'followedUser');
    }
}
