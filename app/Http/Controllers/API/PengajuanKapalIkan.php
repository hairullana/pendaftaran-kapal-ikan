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

        // cek apakah kode sudah dipakai
        $cek_kode = KapalIkan::where('kode_kapal', $request->kode_kapal)->first();
        if (!$cek_kode) return response()->json(['status' => false, 'message' => "kapal dengan kode $request->kode_kapal sudah pernah dibuat sebelumnya."]);

        DB::beginTransaction();
        try {
            KapalIkan::create([
                'user_id' => auth()->user()->id,
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

    public function edit(Request $request, $id)
    {
        $kapal_ikan = KapalIkan::find($id);

        // cek ada datanya atau tidak
        if (!$kapal_ikan) return response()->json(['status' => false, 'message' => 'Tidak bisa edit karena Kapal Ikan tidak ditemukan atau sudah dihapus']);
        // jika yg edit user dan kapal bukan punya si user, jangan kasi edit
        if (auth()->user()->hasRole('user') && $kapal_ikan->user_id != auth()->user()->id) return response()->json(['status' => false, 'message' => 'Tidak bisa edit karena Kapal ikan bukan milik anda']);
        // jika bukan punya si user, jangan kasi edit
        if ($kapal_ikan->status != KapalIkan::PENGAJUAN) return response()->json(['status' => false, 'message' => 'Tidak bisa edit karena Kapal ikan sudah bukan dalam status pengajuan']);

        $validator = Validator::make($request->all(), [
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
            $kapal_ikan->update([
                'nama_kapal' => $request->nama_kapal,
                'nama_pemilik' => $request->nama_pemilik,
                'alamat_pemilik' => $request->alamat_pemilik,
                'ukuran_kapal' => $request->ukuran_kapal,
                'kapten' => $request->kapten,
                'jumlah_anggota' => $request->jumlah_anggota,
                'foto_kapal' => $request->foto_kapal,
                'nomor_izin' => $request->nomor_izin,
                'dokumen_perizinan' => $request->dokumen_perizinan
            ]);

            DB::commit();

            return response()->json(['status' => false, 'message' => 'Sukses mengedit pengajuan kapal ikan, sedang menunggu verifikasi dari admin', 'data' => $kapal_ikan]);
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

    public function terima($id)
    {
        DB::beginTransaction();
        try {
            $user = KapalIkan::find($id);

            // jika sudah di acc atau tolak
            if ($user->status == 0 || $user->status == 1) return response()->json(['status' => false, 'message' => 'Kapal Ikan sudah di terima / tolak oleh admin sebelumnya']);

            $user->status = KapalIkan::DITERIMA;
            $user->save();

            DB::commit();

            return response()->json(['status' => false, 'message' => 'Suskes terima pengajuan kapal ikan']);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tolak(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'alasan' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json($validator->messages());
        }

        DB::beginTransaction();
        try {
            $user = KapalIkan::find($id);

            // jika sudah di acc atau tolak
            if ($user->status == 0 || $user->status == 1) return response()->json(['status' => false, 'message' => 'Kapal Ikan sudah di terima / tolak oleh admin sebelumnya']);

            $user->status = KapalIkan::DITOLAK;
            $user->alasan_ditolak = $request->alasan;
            $user->save();

            DB::commit();

            return response()->json(['status' => false, 'message' => 'Suskes tolak pengajuan kapal ikan dengan alasan ' . $request->alasan]);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
