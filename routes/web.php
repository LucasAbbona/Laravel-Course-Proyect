<?php

use GuzzleHttp\Middleware;
use App\Events\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//User Related
Route::get('/', [UserController::class, 'showCorrectHomepage'])->name('login');
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');

//Blog Posts Related
Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware('admin');
Route::post('/create-post', [PostController::class, 'storeNewPosts'])->middleware('auth');
Route::get('/post/{post}', [PostController::class, 'viewSinglePosts']);
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'update'])->middleware('can:update,post');


//Profile Related
Route::get('/profile/{user:username}', [UserController::class, 'profile']);
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('admin');
Route::post('/manageAvatar', [UserController::class, 'storeAvatar'])->middleware('admin');
Route::get('/profile/{user:username}/following', [UserController::class, 'profileFollowing']);
Route::get('/profile/{user:username}/followers', [UserController::class, 'profileFollowers']);


//
Route::get('/admin-only', function(){    
    return 'only admin should be able to see this page';
})->middleware('can:visitAdminPages');

//Follow Related
Route::post('/create-follow/{user:username}',[FollowController::class, 'createFollow'])->middleware('admin');
Route::post('/remove-follow/{user:username}',[FollowController::class, 'removeFollow'])->middleware('admin');

//Search Related
Route::get('/search/{term}',[PostController::class, 'search']);

//Chat Related
Route::post('/send-chat-message', function (Request $request){
    $formFields= $request->validate([
        'textvalue'=>'required'
    ]);
    if(!trim(strip_tags($formFields['textvalue']))){
        return response()->noContent();
    }
    broadcast(new ChatMessage(['username'=> auth()->user()->username, 'textvalue'=> strip_tags($request->textvalue),'avatar'=> auth()->user()->avatar]))->toOthers();
    return response()->noContent();
})->middleware('admin');