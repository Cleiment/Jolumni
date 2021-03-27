<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;

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
            'user' => $user,
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

        if(count(User::all()) != 0){
            $id = User::all()->last()->user_id;
            $newuser->user_id = 'U'.str_pad(((int)substr($id, 1)) + 1, 9, '0', STR_PAD_LEFT);
        }else{
            $newuser->user_id = 'U000000001';
        }

        $newuser->save();

        return response([
            'message' => 'berhasil'
        ]);
    }

    public function regStage2(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'tgl_lahir'=>'required|date',
            'tahun_masuk'=>'required|size:4',
            'tahun_selesai'=>'required|size:4',
            'alamat'=>'required',
            'no_telp'=>'required|max:20',
            'pekerjaan'=>'required',
            'gambar'=>'required|file'
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

        $newuser = User::where('id', request()->user()->user_id);

        $newuser->tgl_lahir = $request->tgl_lahir;
        $newuser->tahun_masuk = $request->tahun_masuk;
        $newuser->tahun_selesai = $request->tahun_selesai;
        $newuser->alamat = $request->alamat;
        $newuser->no_telp = $request->no_telp;
        $newuser->pekerjaan = $request->pekerjaan;

        $newuser->gambar = '/User/default.png';

        $newuser->save();

        return response([
            'message' => 'berhasil'
        ]);
    }

    public function logout(Request $request)
    {
        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        
        return response([
            'message' => 'berhasil'
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
            'message' => 'berhasil'
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
}
