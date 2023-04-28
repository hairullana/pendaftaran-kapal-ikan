<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Mail\RegisterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'name' => ['required', 'min:3'],
            'password' => ['required', 'confirmed']
        ]);

        if($validator->fails()){
            return response()->json($validator->messages());
        }
        
        DB::beginTransaction();
        try {
            $otp = rand(100000,999999); // 6 angka
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'status' => User::PENGAJUAN
            ]);

            Mail::to($request->email)
                ->send(new RegisterMail([
                    'email' => $request->email,
                    'otp' => $otp,
                    'link' => route('confirmation_link', ['email' => $request->email, 'otp' => $otp])
                ]));

            DB::commit();

            return response()->json(['status' => false, 'message' => "Sukses membuat akun, silakan cek email $request->email untuk konfirmasi email"]);
        } catch (Exception $e) {
            DB::rollback();
            
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function confirmation_link($email, $otp)
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', $email)->first();

            // jika email sudah diverifiksi
            if ($user->email_verified_at != null) return response()->json(['status' => false, 'messsage' => 'Email sudah diverfikasi']);
            
            // jika otp atau email tidak match
            if ($user->email != $email || $user->otp != $otp) {
                return response()->json(['status' => false, 'messsage' => 'email atau kode otp tidak valid']);
            }

            $user->email_verified_at = now();
            $user->otp = null;
            $user->save();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Berhasil verifikasi email, sedang menunggu verifikasi akun dari admin']);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function confirmation_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required'],
            'otp' => ['required']
        ]);

        if($validator->fails()){
            return response()->json($validator->messages());
        }
        
        $email = $request->email;
        $otp = $request->otp;

        DB::beginTransaction();
        
        try {
            $user = User::where('email', $email)->first();

            // jika email sudah diverifiksi
            if ($user->email_verified_at != null) return response()->json(['status' => false, 'messsage' => 'Email sudah diverfikasi']);
            
            // jika otp atau email tidak match
            if ($user->email != $email || $user->otp != $otp) {
                return response()->json(['status' => false, 'messsage' => 'email atau kode otp tidak valid']);
            }

            $user->email_verified_at = now();
            $user->otp = null;
            $user->save();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Berhasil verifikasi email, sedang menunggu verifikasi akun dari admin']);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
