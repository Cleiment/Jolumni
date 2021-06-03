<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use App\Models\Lowongan;
use App\Models\LowonganDetail;
use App\Models\UserPost;
use App\Models\UserPostLike;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Laravel\Sanctum\Sanctum;

class UserController extends Controller
{
    // USER

    public function getUser()
    {
        return User::where('user_id', request()->user()->user_id)->first();
    }

    public function getUserWithId($id)
    {
        return User::where('user_id', $id)->first();
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required'
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
        $user = User::where('email', $request->email)->first();
        // return response([
        //     'user' => $user,
        //     'request' => $request->all()
        // ]);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['The provided credentials does not match our records']
            ]);
        }

        $user->tokens()->where('tokenable_id', $user->user_id)->delete();

        $token = $user->createToken(Str::random(60))->plainTextToken;

        return response([
            'token' => $token
        ]);
    }

    public function regStage1(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama_depan'=>'required|max:100',
            'nama_belakang'=>'required|max:100',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6',
            'jurusan'=>'required',
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

        $newuser = new User;

        $newuser->nama_depan = $request->nama_depan;
        $newuser->nama_belakang = $request->nama_belakang;
        $newuser->email = $request->email;
        $newuser->jurusan = $request->jurusan;
        $newuser->password = Hash::make($request->password);
        $newuser->gambar = 'User/default.png';

        if(count(User::all()) != 0){
            $id = User::all()->last()->user_id;
            $newuser->user_id = 'U'.str_pad(((int)substr($id, 1)) + 1, 9, '0', STR_PAD_LEFT);
        }else{
            $newuser->user_id = 'U000000001';
        }

        $newuser->save();

        return response([
            'msg' => 'berhasil'
        ]);
    }

    public function edit(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama_depan'=>'required',
            'nama_belakang'=>'required',
            'email'=>'required',
            'tgl_lahir'=>'required|date',
            'tahun_masuk'=>'required|size:4',
            'tahun_selesai'=>'required|size:4',
            'no_telp'=>'required|max:20',
            'confPassword'=>'required',
            // 'alamat'=>'required',
            // 'pekerjaan'=>'required',
            // 'gambar'=>'required|file'
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

        $user = User::where('user_id', request()->user()->user_id)->first();

        if (!$user || !Hash::check($request->confPassword, $user->password)) {
            return response([
                'message' => ['The password is wrong!']
            ]);
        }

        $user->nama_depan = $request->nama_depan;
        $user->nama_belakang = $request->nama_belakang;
        $user->email = $request->email;
        $user->tgl_lahir = $request->tgl_lahir;
        $user->no_telp = $request->no_telp;
        $user->tahun_masuk = $request->tahun_masuk;
        $user->tahun_selesai = $request->tahun_selesai;
        $user->jurusan = $request->jurusan;
        $user->alamat = $request->alamat;
        $user->pekerjaan = $request->pekerjaan;

        if ($request->fileNya == 'upload') {
            if ($user->gambar != 'User/default.png') {
                Storage::disk('public')->delete($user->gambar);
            }
            $file = $request->file;
            $path = $file->store('User', 'public');
            $user->gambar = $path;
        }

        $user->save();

        return response([
            'msg' => 'berhasil'
        ]);
    }

    public function logout(Request $request)
    {
        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        
        return response([
            'msg' => 'berhasil'
        ]);
    }

    // FOLLOW

    public function follow(Request $request)
    {
        $userid = request()->user()->user_id;

        if(Follow::where('follower', $userid)->where('following', $request->toFollow)->first()){
            return response([
                'message' => 'already followed'
            ]);
        }

        $follow = new Follow;

        $follow->follower = $userid;
        $follow->following = $request->toFollow;

        $follow->save();

        return response([
            'msg' => 'berhasil'
        ]);
    }

    public function checkFollow(Request $request)
    {
        $userid = request()->user()->user_id;

        $following = Follow::where('follower', $userid)->pluck('following');
        $followers = Follow::where('following', $userid)->pluck('follower');

        return response([
            'id' => $userid,
            'following' => [
                'count' => count($following),
                'users' => $following
            ],
            'followers' => [
                'count' => count($followers),
                'users' => $followers
            ]
        ]);
    }
    
    public function delAcc(Request $request)
    {
        $u = request()->user();
        $user_id = $u->user_id;
        
        $user = User::where('user_id', $user_id)->first();
        
        if (!$user || !Hash::check($request->pass, $user->password)) {
            return response([
                'error' => ['The password is wrong!']
            ], 400);
        }

        $user_posts = UserPost::where('owner', $user_id)->get();
        foreach ($user_posts as $user_post) {
            UserPostLike::where('post_id', $user_post->id)->delete();
        }

        UserPostLike::where('user_liked', $user_id)->delete();
        UserPost::where('owner', $user_id)->delete();

        $lowongan = Lowongan::where('owner', $user_id)->get();
        foreach ($lowongan as $lw) {
            LowonganDetail::where('lowongan_id', $lw->id)->delete();
        }

        LowonganDetail::where('pelamar', $user_id)->delete();
        Lowongan::where('owner', $user_id)->delete();

        Follow::where('follower', $user_id)->delete();
        Follow::where('following', $user_id)->delete();

        $u->tokens()->where('id', $u->currentAccessToken()->id)->delete();

        User::where('user_id', $user_id)->delete();

        return response(['message' => 'Berhasil hapus akun!']);
    }
}
