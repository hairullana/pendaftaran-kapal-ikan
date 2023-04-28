<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\KapalIkan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PengajuanKapalIkan extends Controller
{
    public function pengajuan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_kapal' => ['required'],
            'nama_kapal' => ['required'],
            'nama_pemilik' => ['required'],
            'alamat_pemilik' => ['required'],
            'ukuran_kapal' => ['required'],
            'kapten' => ['required'],
            'jumlah_anggota' => ['required'],
            'foto_kapal' => ['required'],
            'nomor_izin' => ['required'],
            'dokumen_perizinan' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json($validator->messages());
        }

        DB::beginTransaction();
        try {
            KapalIkan::create($request->all());

            DB::commit();

            return response()->json(['status' => false, 'message' => 'Sukses membuat pengajuan kapal ikan, sedang menunggu verifikasi dari admin']);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
