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
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'status' => User::PENGAJUAN
            ]);

            $user->assignRole('user');

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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if($validator->fails()){
            return response()->json($validator->messages());
        }

        $user = User::where('email', $request->email)->first();
        if ($user->status == User::PENGAJUAN) return response()->json(['status' => false, 'message' => 'Email belum diverifikasi']);
        if ($user->status == User::DITOLAK) return response()->json(['status' => false, 'message' => 'Akun ditolak oleh admin']);

        //get credentials from request
        $credentials = $request->only('email', 'password');

        //if auth failed
        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ]);
        }

        //if auth success
        return response()->json([
            'success' => true,
            'token'   => $token   
        ], 200);
    }

    public function list_verifikasi_user()
    {
        $new_user = User::where('status', User::PENGAJUAN)->get();
        return response(['status' => true, 'data' => $new_user]);
    }

    public function terima_user($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);

            // jika sudah di acc atau tolak
            if ($user->status == 0 || $user->status == 1) return response()->json(['status' => false, 'message' => 'User sudah di terima / tolak oleh admin sebelumnya']);

            $user->status = User::DITERIMA;
            $user->save();

            DB::commit();

            return response(['status' => true, 'message' => 'Berhasil menerima user', 'data' => $user]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tolak_user($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);

            // jika sudah di acc atau tolak
            if ($user->status == 0 || $user->status == 1) return response()->json(['status' => false, 'message' => 'User sudah di terima / tolak oleh admin sebelumnya']);

            $user->status = User::DITOLAK;
            $user->save();

            DB::commit();

            return response(['status' => true, 'message' => 'Berhasil menolak user', 'data' => $user]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
