<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Mail\RegisterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function list_users()
    {
        $new_user = User::all();
        return response(['status' => true, 'data' => $new_user]);
    }

    public function detail_users($id)
    {
        $new_user = User::with('kapal_ikan')->find($id);
        return response(['status' => true, 'data' => $new_user]);
    }

    public function edit_profile(Request $request)
    {
        // jika tdk ada data requeest
        if ($request->all() == []) return response()->json(['status' => false, 'message' => 'Silahkan masukkan data untuk mengubah profil (nama / email)']);

        $validator = Validator::make($request->all(), [
            'email' => ['email'],
            'name' => ['min:3']
        ]);

        if($validator->fails()){
            return response()->json($validator->messages());
        }
        
        DB::beginTransaction();
        try {
            $otp = rand(100000,999999); // 6 angka
            $user = User::find(auth()->user()->id);
            // ganti nama
            if ($request->name) $user->name = $request->name;
            // jika ganti email, kirim untuk verifikasi lagi
            if ($request->email) {
                $user->email = $request->email;
                Mail::to($request->email)
                    ->send(new RegisterMail([
                        'email' => $request->email,
                        'otp' => $otp,
                        'link' => route('confirmation_link', ['email' => $request->email, 'otp' => $otp])
                    ]));
                $user->email_verified_at = null;
            }

            DB::commit();

            if ($request->email) {
                return response()->json(['status' => false, 'message' => "Sukses mengedit profil, silakan cek email $request->email untuk konfirmasi email lagi", 'data' => $user]);
            } else {
                return response()->json(['status' => false, 'message' => "Sukses mengedit profil", 'data' => $user]);
            }
        } catch (Exception $e) {
            DB::rollback();
            
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
