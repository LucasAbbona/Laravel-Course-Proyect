<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user){
        if($user->id == auth()->user()->id){
            return back()->with('error','You Cannot follow yourself');
        }
        $existCheck= Follow::where([['user_id','=',auth()->user()->id],['followedUser','=',$user->id]])->count();
        if($existCheck){
            return back()->with('error','You are already following that user');
        }
        $newFollow= new Follow;
        $newFollow->user_id= auth()->user()->id;
        $newFollow->followedUser = $user->id;
        $newFollow->save();
        return back()->with('success','you now follow this user');
    }
    public function removeFollow(User $user){
        Follow::where([['user_id','=',auth()->user()->id],['followedUser','=',$user->id]])->delete();
        return back()->with('success','User Successfully unfollowed');
    }
}
