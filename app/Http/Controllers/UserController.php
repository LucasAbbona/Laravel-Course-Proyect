<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Events\OurExampleEvent;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(Request $request){
        $incomingField= $request->validate([
            'username'=>['required', 'min:3','max:20',Rule::unique('users','username')],
            'email'=>['required','email' ,Rule::unique('users','email')],
            'password'=>['required','min:6','confirmed']
        ]);
        $incomingField['password'] = bcrypt($incomingField['password']);

        $user = User::create($incomingField);
        auth()->login($user);
        return redirect('/')->with('success','Thank you for creating an account');
    }

    public function login(Request $request){
        $incomingField = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);
        if(auth()->attempt(['username'=>$incomingField['loginusername'], 'password' => $incomingField['loginpassword']])){
            $request->session()->regenerate();
            event(new OurExampleEvent(['username'=>auth()->user()->username,'action'=> 'login']));            return redirect('/')->with('success','You are now Logged in');
        }else{
            return redirect('/')->with('error','Invalid Login');
        }
    }


    public function logout(){
        event(new OurExampleEvent(['username'=>auth()->user()->username,'action'=> 'logout']));
        auth()->logout();
        return redirect('/')->with('success','You are now Logged out');
    }
    
    
    public function showCorrectHomepage(){
        if(auth()->check()){
            return view('homepage-feed', ['posts'=> auth()->user()->FeedPosts()->latest()->paginate(4)]);
        }else{
            $postCount = Cache::remember('postCount',20,function(){
                
                return Post::count();
            });

            return view("homepage",['postCount'=>$postCount]);
        }
    }

    private function getSharedData($user){
        $currentlyfollowing=0;
        if(auth()->check()){
            $currentlyfollowing=Follow::where([['user_id','=',auth()->user()->id],['followedUser','=',$user->id]])->count();
        }
        View::share('shareData',['username' => $user->username, 'postCount' => $user->posts()->count(),'followerCount'=> $user->followers()->count(),'followingCount'=> $user->following()->count() , 'avatar'=>$user->avatar,'currentlyfollowing'=>$currentlyfollowing]);
    }

    public function profile(User $user){
        $this->getSharedData($user);
        return view('profile-posts', [ 'posts' => $user->posts()->latest()->get()]);
    }

    

    public function profileFollowers(User $user){
        $this->getSharedData($user);
        return view('profile-followers', [ 'followers' => $user->followers()->latest()->get()]);
    }



    public function profileFollowing(User $user){
        $this->getSharedData($user);
        return view('profile-following', [ 'following' => $user->following()->latest()->get()]);
    }


    public function showAvatarForm(){
        return view('manage-avatar');
    }
    public function storeAvatar(Request $request){
        $request->validate([
            'avatar'=>'required|image|max:15000',

        ]);
        $request->file('avatar')->store('public/avatars/');
        $filename=$request->file('avatar')->hashName();

        $oldAvatar= auth()->user()->avatar;
        auth()->user()->avatar = $filename;
        auth()->user()->save();
        if($oldAvatar != '/fallback-avatar.jpg'){ 
            //Deleting the old avatar and replacing it with the new one.
            Storage::delete(str_replace('/storage/','public/',$oldAvatar));
        }
        return back()->with('success','Congrats on the new avatar');
    }
}
