<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPost;
use App\Models\UserPostLike;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserPostController extends Controller
{
    public function getAll()
    {
        $user_post = UserPost::all()->shuffle();
        for ($i = 0; $i < count($user_post); $i++) { 
            $user = User::where('user_id', $user_post[$i]->owner)->first();
            $user_post[$i]->nama_depan = $user->nama_depan;
            $post_like = UserPostLike::where('post_id', $user_post[$i]->id)->get();
            $post_likes = array();
            foreach ($post_like as $value) {
                array_push($post_likes, $value);
            }

            $user_post[$i]->like = $post_likes;
        }

        return $user_post;
    }

    public function getSpecific($id){
        return UserPost::where('id', $id)->first();
    }

    public function getSpecificOwner($id)
    {
        $user_post_owner = UserPost::where('owner', $id)->get();

        for ($i = 0; $i < count($user_post_owner); $i++) { 
            $user_post_owner[$i]->nama_depan = User::where('user_id', $user_post_owner[$i]->owner)->get('nama_depan');
            $post_like = UserPostLike::where('post_id', $user_post_owner[$i]->id)->get();
            $post_likes = array();
            foreach ($post_like as $value) {
                array_push($post_likes, $value);
            }

            $user_post_owner[$i]->like = $post_likes;
        }

        return $user_post_owner;
    }

    public function getRandom()
    {
        $user_posts = UserPost::all();

        if ($user_posts->count() >= 4) {
            $random_count = 4;
        }
        else if ($user_posts->count() < 4){
            $random_count = $user_posts->count();
        }

        $user_post = UserPost::all()->random($random_count)->shuffle();
        for ($i = 0; $i < count($user_post); $i++) { 
            $user = User::where('user_id', $user_post[$i]->owner)->first();
            $user_post[$i]->nama_depan = $user->nama_depan;
            $post_like = UserPostLike::where('post_id', $user_post[$i]->id)->get();
            $post_likes = array();
            foreach ($post_like as $value) {
                array_push($post_likes, $value);
            }

            $user_post[$i]->like = $post_likes;
        }

        return $user_post;
    }

    public function create(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'caption'=>'required|max:100',
            'file'=>'required|file',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors();
            $nferrors = [];
            foreach ($errors->all() as $message) {
                array_push($nferrors, $message);
            }
            return response([
                'errors' => $nferrors
            ],400);
        }

        $newpost = new UserPost;

        $newpost->caption = $request->caption;
        $newpost->owner = request()->user()->user_id;

        $file = $request->file;
        $path = $file->store('UserPost', 'public');
        $newpost->gambar = $path;
        
        $newpost->save();

        return response([
            'msg' => 'berhasil'
        ]);
    }

    public function update(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id'=>'required',
            'caption'=>'required|max:100',
            'gambar'=>'required|file',
        ]);

        if ($validated->fails()) {
            $errors = $validated->errors();
            $nferrors = [];
            foreach ($errors->all() as $message) {
                array_push($nferrors, $message);
            }
            return response([
                'errors' => $nferrors
            ], 400);
        }

        $post = UserPost::where('id', $request->id)->first();
        $post->caption = $request->caption;
        $post->owner = request()->user()->user_id;

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($post->gambar);

            $file = $request->gambar;
            $path = $file->store('UserPost', 'public');
            $post->gambar = $path;
        }
        $post->save();

        return response([
            'msg' => 'berhasil'
        ]);
    }

    public function drop(Request $request)
    {
        $post = UserPost::where('id', $request->id)->first();

        UserPostLike::where('post_id', $post->id)->delete();

        Storage::disk('public')->delete($post->gambar);
        UserPost::where('id', $request->id)->delete();

        return response([
            'message' => 'Berhasil Hapus Post!'
        ]);
    }

    public function like(Request $request)
    {
        if (UserPostLike::where('post_id', $request->post_id)->where('user_liked', request()->user()->user_id)->first()) {
            return response([
                'message' => 'already liked'
            ]);
        }

        $like = new UserPostLike;
        $like->post_id = $request->post_id;
        $like->user_liked = request()->user()->user_id;
        $like->save();

        return response([
            'msg' => 'berhasil'
        ]);
    }

    public function unlike(Request $request)
    {
        $like = UserPostLike::where('post_id', $request->post_id)->where('user_liked', request()->user()->user_id)->first();
        $like->delete();

        return response([
            'msg' => 'berhasil'
        ]);
    }
}
