<?php

namespace App\Http\Controllers;

use App\Jobs\SendNewPostEmail;
use App\Models\Post;
use App\Models\Posts;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{
    public function showCreateForm(){
        return view('create-post');
    }
    public function storeNewPosts(Request $request){
        $incomingFields= $request->validate([
            'title'=>'required',
            'body'=>'required'
        ]);
        
        $incomingFields['title']= strip_tags($incomingFields['title']);
        $incomingFields['body']= strip_tags($incomingFields['body']);
        $incomingFields['user_id']=auth()->id();
        $NewPost = Post::create($incomingFields);
        dispatch(new SendNewPostEmail(['sendTo'=>auth()->user()->email,'name'=> auth()->user()->username, 'title'=>$NewPost->title]));

        return redirect("/post/{$NewPost->id}")->with('success', 'New Post Successfully created');
    }

    public function viewSinglePosts(Post $post){
        $ourHTML=strip_tags(Str::markdown($post->body),'<p><ul><ol><li><strong><em><h3>');
        $post['body']=$ourHTML;
        return view('single-post', ['post'=>$post]);
    }

    public function delete(Post $post){
        $post->delete();
        return redirect('/profile/'.auth()->user()->username)->with('success','Post successfully deleted');
    }

    public function showEditForm(Post $post){
        return view('edit-post',['post'=> $post]);
    }
    public function update(Post $post, Request $request){
        $incomingFields= $request->validate([
            'title'=>'required',
            'body'=>'required'
        ]);
        $incomingFields['title']= strip_tags($incomingFields['title']);
        $incomingFields['body']= strip_tags($incomingFields['body']);
        $post->update($incomingFields);
        return back()->with('success', 'Post Successfully updated');
    }

    public function search($term){
        $posts=Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
    }


}
