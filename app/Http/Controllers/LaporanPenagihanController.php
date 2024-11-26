<?php

namespace App\Http\Controllers;

use App\Models\LaporanPenagihan;
use App\Models\DataPenagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FonnteService;


class LaporanPenagihanController extends Controller
{
    public function index()
    {
        $laporanPenagihan = LaporanPenagihan::all();
        return view('admin.laporan_penagihan.data', compact('laporanPenagihan'));
    }

        public function filter(Request $request)
        {
        $query = LaporanPenagihan::query();

        if ($request->has('jenis_pajak_id') && $request->jenis_pajak_id != '') {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }

        // KATEGORI
        // if ($request->has('kategori_pajak_id') && $request->kategori_pajak_id != '') {
        //     $query->where('kategori_pajak_id', $request->kategori_pajak_id);
        // }
        
        // JENIS
        // if ($request->has('jenis_pajak_id') && $request->jenis_pajak_id != '') {
        //     $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        // }
        // Filter berdasarkan tanggal tagihan
        // if ($request->has('bulan_tagihan') && $request->bulan_tagihan != '') {
        //     $query->whereMonth('tanggal_tagihan', $request->bulan_tagihan);
        // }

        // Filter berdasarkan tanggal pembayaran
        // if ($request->has('bulan_pembayaran') && $request->bulan_pembayaran != '') {
        //     $query->whereMonth('tanggal_pembayaran', $request->bulan_pembayaran);
        // }

        // Filter berdasarkan pembagian zonasi
        if ($request->has('pembagian_zonasi') && $request->pembagian_zonasi != '') {
            $query->where('pembagian_zonasi', $request->pembagian_zonasi);
        }

        // Filter berdasarkan status
        // if ($request->has('status') && $request->status != '') {
        //     $query->where('status', $request->status);
        // }

                        // Pencarian berdasarkan nama atau username
                        if ($request->has('search') && $request->search != '') {
                            $query->where(function ($q) use ($request) {
                                $q->where('npwpd', 'LIKE', '%' . $request->search . '%')
                                ->orWhere('nama_pajak', 'LIKE', '%' . $request->search . '%')
                                ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
                                // ->orWhere('penanggung_jawab', 'LIKE', '%' . $request->search . '%')
                                // ->orWhere('alamat_penanggung_jawab', 'LIKE', '%' . $request->search . '%');
                                // ->orWhere('kategori_pajak', 'LIKE', '%' . $request->search . '%')
                                // ->orWhere('tanggal_tagihan', 'LIKE', '%' . $request->search . '%')
                                // ->orWhere('jumlah_piutang', 'LIKE', '%' . $request->search . '%')
                                // ->orWhere('bulan', 'LIKE', '%' . $request->search . '%')
                                // ->orWhere('tanggal_pembayaran', 'LIKE', '%' . $request->search . '%')
                                // ->orWhere('kode_zonasi', 'LIKE', '%' . $request->search . '%')
                                // ->orWhere('pembagian_zonasi', 'LIKE', '%' . $request->search . '%');
                            });
                        }
                        

        // Ambil data laporan yang sudah difilter
        $laporanPenagihan = $query->get();
        return view('admin.laporan_penagihan.data', compact('laporanPenagihan'));
        }


        public function markAsPaid(Request $request, $id)
        {
            $dataPenagihan = DataPenagihan::findOrFail($id);
        
            // Validasi file yang diupload
            $request->validate([
                'uploadbuktivisit' => 'required|image|mimes:jpg,png,jpeg|max:2048',
                'uploadbuktipembayaran' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            ]);
        
            // Proses upload file
            $visitFilePath = $request->file('uploadbuktivisit')->store('bukti_visit', 'public');
            $paymentFilePath = $request->file('uploadbuktipembayaran')->store('bukti_pembayaran', 'public');
        
            // Tandai sebagai terbayar
            $dataPenagihan->status = 'terbayar';
            $dataPenagihan->uploadbuktivisit = $visitFilePath;
            $dataPenagihan->uploadbuktipembayaran = $paymentFilePath;
            $dataPenagihan->save();
        
            // Cek apakah data sudah ada di laporan penagihan berdasarkan npwpd
            $existingLaporan = LaporanPenagihan::where('npwpd', $dataPenagihan->npwpd)->first();
        
            if (!$existingLaporan) {
                // Pindahkan data ke laporan penagihan jika belum ada
                LaporanPenagihan::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'nomor_telepon' => $dataPenagihan->nomor_telepon,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'kategori_pajak_id' => $dataPenagihan->kategori_pajak_id,
                    // 'tanggal_tagihan' => $dataPenagihan->tanggal_tagihan,
                    'jumlah_piutang' => $dataPenagihan->jumlah_piutang,
                    'pembagian_zonasi' => $dataPenagihan->pembagian_zonasi,
                    'uploadbuktivisit' => $dataPenagihan->uploadbuktivisit,
                    'uploadbuktipembayaran' => $dataPenagihan->uploadbuktipembayaran,
                    'tanggal_pembayaran' => $dataPenagihan->tanggal_pembayaran,
                    'status' => 'terbayar',
                    'verifikasi' => '',
                ]);
            } else {
                // Jika data sudah ada, bisa diberikan notifikasi atau penanganan lain
                return redirect()->route('data_penagihan.data')->with('error', 'Data dengan NPWPD tersebut sudah ada di laporan penagihan.');
            }
        
            // Hapus data dari DataPenagihan setelah dipindahkan
            $dataPenagihan->delete();
        
            // Kirim pesan WhatsApp ke nomor wajib pajak menggunakan Fonnte
            $fonnteService = new FonnteService();
            $message = "Pembayaran untuk NPWPD {$dataPenagihan->npwpd} telah terbayar. Terima kasih atas kerjasama Anda.";
            $result = $fonnteService->sendMessage($dataPenagihan->nomor_telepon, $message);
            
            if ($result) {
                return redirect()->route('data_penagihan.data')->with('success', 'Pembayaran berhasil dilakukan dan data telah dipindahkan. Pesan WhatsApp berhasil dikirim.');
            } else {
                return redirect()->route('data_penagihan.data')->with('error', 'Pembayaran berhasil, tetapi gagal mengirim pesan WhatsApp.');
            }
        }
        
        
        

    public function showVisitProof($id)
{
    $laporanPenagihan = LaporanPenagihan::find($id);
    if (!$laporanPenagihan || !$laporanPenagihan->uploadbuktivisit) {
        return abort(404, 'Bukti visit tidak ditemukan.');
    }
    return response()->file(storage_path("app/public/{$laporanPenagihan->uploadbuktivisit}"));
}

public function showPaymentProof($id)
{
    $laporanPenagihan = LaporanPenagihan::find($id);
    if (!$laporanPenagihan || !$laporanPenagihan->uploadbuktipembayaran) {
        return abort(404, 'Bukti pembayaran tidak ditemukan.');
    }
    return response()->file(storage_path("app/public/{$laporanPenagihan->uploadbuktipembayaran}"));
}     
}