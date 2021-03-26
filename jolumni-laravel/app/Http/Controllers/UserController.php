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
        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['The provided credentials does not match our records']
            ], 404);
        }

        $user->tokens()->where('tokenable_id', $user->user_id)->delete();

        $token = $user->createToken(Str::random(60))->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama_depan'=>'required|max:100',
            'nama_belakang'=>'required|max:100',
            'email'=>'required|email|unique:users,email',
            'password'=>'required',
            'tgl_lahir'=>'required|date',
            'tahun_masuk'=>'required|size:4',
            'tahun_selesai'=>'required|size:4',
            'jurusan'=>'required',
            'alamat'=>'required',
            'no_telp'=>'required|max:20',
            'pekerjaan'=>'required',
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
            ]);
        }

        $newuser = new User;

        $newuser->nama_depan = $request->nama_depan;
        $newuser->nama_belakang = $request->nama_belakang;
        $newuser->email = $request->email;
        $newuser->tgl_lahir = $request->tgl_lahir;
        $newuser->tahun_masuk = $request->tahun_masuk;
        $newuser->tahun_selesai = $request->tahun_selesai;
        $newuser->jurusan = $request->jurusan;
        $newuser->alamat = $request->alamat;
        $newuser->no_telp = $request->no_telp;
        $newuser->pekerjaan = $request->pekerjaan;

        $newuser->gambar = '/User/default.png';
        if(count(User::all()) != 0){
            $id = User::all()->last()->user_id;
            $newuser->user_id = 'U'.str_pad(((int)substr($id, 1)) + 1, 9, '0', STR_PAD_LEFT);
        }else{
            $newuser->user_id = 'U000000001';
        }

        $newuser->password = Hash::make($request->password);

        $newuser->save();

        return response([
            'message' => 'berhasil'
        ]);
    }

    public function logout(Request $request)
    {
        $user = request()->user();
        $user->tokens()->where('id',$user->currentAccessToken()->id)->delete();
        // $user->tokens()-delete();
        
        return 'berhasil';
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
