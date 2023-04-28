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
            KapalIkan::create([
                'kode_kapal' => $request->kode_kapal,
                'nama_kapal' => $request->nama_kapal,
                'nama_pemilik' => $request->nama_pemilik,
                'alamat_pemilik' => $request->alamat_pemilik,
                'ukuran_kapal' => $request->ukuran_kapal,
                'kapten' => $request->kapten,
                'jumlah_anggota' => $request->jumlah_anggota,
                'foto_kapal' => $request->foto_kapal,
                'nomor_izin' => $request->nomor_izin,
                'dokumen_perizinan' => $request->dokumen_perizinan,
                'status' => KapalIkan::PENGAJUAN
            ]);

            DB::commit();

            return response()->json(['status' => false, 'message' => 'Sukses membuat pengajuan kapal ikan, sedang menunggu verifikasi dari admin']);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function list()
    {
        $new_kapal = KapalIkan::where('status', KapalIkan::PENGAJUAN)->get();
        return response(['status' => true, 'data' => $new_kapal]);
    }
}
