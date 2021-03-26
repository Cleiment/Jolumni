<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\LowonganDetail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LowonganController extends Controller
{
    public function getAll()
    {
        return Lowongan::all();
    }

    public function getSpecific($id){
        return Lowongan::where('id', $id)->first();
    }

    public function create(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'judul'=>'required|max:50',
            'detail_lowongan'=>'required',
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

        $newlowongan = new Lowongan;

        $newlowongan->judul = $request->judul;
        $newlowongan->detail_lowongan = $request->detail_lowongan;
        $newlowongan->owner = request()->user()->user_id;

        $file = $request->gambar;
        $path = $file->store('Lowongan', 'public');
        $newlowongan->gambar = $path;
        
        $newlowongan->save();

        return response([
            'message' => 'berhasil'
        ]);
    }

    public function update(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id'=>'required',
            'judul'=>'required|max:50',
            'detail_lowongan'=>'required',
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

        $lowongan = Lowongan::where('id', $request->id)->first();
        $lowongan->judul = $request->judul;
        $lowongan->detail_lowongan = $request->detail_lowongan;

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($lowongan->gambar);
            
            $file = $request->gambar;
            $path = $file->store('Lowongan', 'public');
            $lowongan->gambar = $path;
        }
        $lowongan->save();

        return response([
            'message' => 'berhasil'
        ]);
    }

    public function drop(Request $request)
    {
        $lowongan = Lowongan::where('id', $request->id)->first();
        Storage::disk('public')->delete($lowongan->gambar);
        $lowongan->delete();

        return response([
            'message' => 'berhasil'
        ]);
    }

    public function lamar(Request $request)
    {
        $newlamaran = new LowonganDetail;
        $newlamaran->lowongan_id = $request->lowongan_id;
        $newlamaran->pelamar = $request->pelamar;
        $newlamaran->save();

        return response([
            'message'=>'berhasil'
        ]);
    }
}
