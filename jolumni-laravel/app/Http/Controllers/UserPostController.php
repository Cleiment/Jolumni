<?php

namespace App\Http\Controllers;

use App\Models\UserPost;
use App\Models\UserPostLike;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserPostController extends Controller
{
    public function getAll()
    {
        return UserPost::all();
    }

    public function getSpecific($id){
        return UserPost::where('id', $id)->first();
    }

    public function create(Request $request)
    {
        $validated = Validator::make($request->all(), [
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
            ]);
        }

        $newpost = new UserPost;

        $newpost->caption = $request->caption;
        $newpost->owner = request()->user()->user_id;

        $file = $request->gambar;
        $path = $file->store('UserPost', 'public');
        $newpost->gambar = $path;
        
        $newpost->save();

        return response([
            'message' => 'berhasil'
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
            ]);
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
            'message' => 'berhasil'
        ]);
    }

    public function drop(Request $request)
    {
        $post = UserPost::where('id', $request->id)->first();
        Storage::disk('public')->delete($post->gambar);
        $post->delete();

        return response([
            'message' => 'berhasil'
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
            'message' => 'berhasil'
        ]);
    }

    public function unlike(Request $request)
    {
        $like = UserPostLike::where('post_id', $request->post_id)->where('user_liked', request()->user()->user_id)->first();
        $like->delete();

        return response([
            'message' => 'berhasil'
        ]);
    }
}
