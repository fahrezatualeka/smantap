<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPiutang;
use App\Models\DataPenetapan;
use App\Models\LaporanPelunasan;
use App\Models\DataPenagihan;
use App\Models\JenisPajak;
use App\Models\KategoriPajak;
use App\Models\DataZonasi;
use App\Imports\DataPiutangImport;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\StorageAttributes;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Exports\DataPiutangExport;
use Barryvdh\DomPDF\Facade\Pdf;


class DataPiutangController extends Controller
{
    public function index()
    {
        $data = DataPiutang::orderBy('created_at', 'desc')->get();
        
        return view('admin.data_piutang.data', compact('data'));
    }
    
    
    public function import(Request $request)
    {
        $request->validate([
            'import_data_piutang' => 'required|file|mimes:xlsx,xls', 
        ]);
    
        // Mendapatkan bulan yang dipilih dari form
        $bulan = $request->input('bulan');
    
        // Mengimpor data Piutang, dengan bulan yang dipilih
        Excel::import(new DataPiutangImport($bulan), $request->file('import_data_piutang'));
        
        return redirect()->back()->with('success', 'Data berhasil diimpor!');
    }

    public function filter(Request $request)
    {
        $query = DataPiutang::query();
    
        // Filter berdasarkan jenis pajak
        if ($request->has('jenis_pajak_id') && $request->jenis_pajak_id != '') {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }
    
        // Filter berdasarkan pencarian (nama pajak atau alamat)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pajak', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%');
            });
        }
    
        // Urutkan berdasarkan data terbaru
        $data = $query->orderBy('created_at', 'desc')->get();
    
        return view('admin.data_piutang.data', compact('data'));
    }
    

    public function importFromDataPenetapan(Request $request)
    {
        Log::info('Tombol Import Piutang ditekan.');
        
        // Ambil data dengan status 'Belum Bayar' dari DataPenetapan yang diperbarui dalam 24 jam terakhir
        $dataPenetapan = DataPenetapan::where('status', 'Belum Bayar')
            ->where('updated_at', '>', now()->subDay())  // Ambil data yang diperbarui dalam 24 jam terakhir
            ->get();
        
        Log::info('Data Penetapan dengan status belum bayar:', ['data' => $dataPenetapan->toArray()]);
        
        // Jika data kosong, beri pesan error dan hentikan eksekusi
        if ($dataPenetapan->isEmpty()) {
            Log::info('Tidak ada data terbaru dengan status belum bayar.');
            return redirect()->back()->with('error', 'Tidak ada data terbaru dengan status "Belum Bayar" untuk diimpor.');
        }
        
        $dataImported = false; // Flag untuk cek apakah ada data yang berhasil diimpor
        
        try {
            // Proses import atau update data jika ada data terbaru
            foreach ($dataPenetapan as $penetapan) {
                // Cek jika data sudah ada di DataPiutang berdasarkan NPWPD dan Periode
                $existingData = DataPiutang::where('npwpd', $penetapan->npwpd)
                    ->where('periode', $penetapan->periode)
                    ->first();
        
                Log::info('Pengecekan data di DataPiutang untuk NPWPD: ' . $penetapan->npwpd . ' dan Periode: ' . $penetapan->periode);
        
                if ($existingData) {
                    // Cek jika data yang ada lebih lama dari data terbaru di DataPenetapan
                    if ($existingData->updated_at < $penetapan->updated_at) {
                        // Update data di DataPiutang jika lebih lama
                        $existingData->update([
                            'nama_pajak' => $penetapan->nama_pajak,
                            'alamat' => $penetapan->alamat,
                            'jumlah_penagihan' => $penetapan->jumlah_penagihan,
                            'jenis_pajak_id' => $penetapan->jenis_pajak_id,
                            'kategori_pajak_id' => $penetapan->kategori_pajak_id,
                            'status' => 'Belum Bayar',
                        ]);
                        Log::info('Data Piutang diupdate untuk NPWPD: ' . $penetapan->npwpd);
                        $dataImported = true; // Set flag ke true
                    }
                } else {
                    // Jika belum ada data, lakukan import ke DataPiutang
                    DataPiutang::create([
                        'npwpd' => $penetapan->npwpd,
                        'nama_pajak' => $penetapan->nama_pajak,
                        'alamat' => $penetapan->alamat,
                        'jumlah_penagihan' => $penetapan->jumlah_penagihan,
                        'jenis_pajak_id' => $penetapan->jenis_pajak_id,
                        'kategori_pajak_id' => $penetapan->kategori_pajak_id,
                        'status' => 'Belum Bayar',
                        'periode' => $penetapan->periode,  // Menyimpan periode yang tepat
                    ]);
                    Log::info('Data Piutang berhasil disimpan untuk NPWPD: ' . $penetapan->npwpd);
                    $dataImported = true; // Set flag ke true
                }
            }
        
            // Jika ada data yang diimpor atau diupdate, beri pesan sukses
            if ($dataImported) {
                return redirect()->back()->with('success', 'Data berhasil diimpor dari Data Penetapan ke Data Piutang!');
            } else {
                return redirect()->back()->with('error', 'Tidak ada data yang diperbarui atau diimpor.');
            }
        } catch (QueryException $e) {
            // Tangani pengecualian SQL dan tampilkan detail kesalahan
            Log::error('Error saat menyimpan data: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'], 500);
        }
    }
    
    
    
    
    
       

    public function saveZonasi(Request $request)
    {
        $zonasiData = $request->input('pembagian_zonasi'); // Array of zonasi data
    
        // Cek apakah ada data yang dipilih
        if (!$zonasiData || empty(array_filter($zonasiData))) {
            return redirect()->back()->with('error', 'Pemilihan pembagian zonasi belum dipilih.');
        }
    
        try {
            $success = false; // Flag untuk mengecek apakah ada data yang berhasil disimpan
    
            foreach ($zonasiData as $id => $zonasi) {
                if (!empty($zonasi)) {
                    // Cari data Piutang berdasarkan ID
                    $dataPiutang = DataPiutang::find($id);
                    if ($dataPiutang) {
                        // Update pembagian zonasi
                        $dataPiutang->pembagian_zonasi = $zonasi;
                        if ($dataPiutang->save()) {
                            // Log jika pembagian zonasi berhasil disimpan
                            Log::info('Zonasi berhasil disimpan untuk ID: ' . $id);
                            $success = true; // Set flag ke true jika ada yang berhasil disimpan
                            
                            // Pindahkan data ke DataPenagihan jika belum ada
                            $existingPenagihan = DataPenagihan::where('npwpd', $dataPiutang->npwpd)
                                ->where('periode', $dataPiutang->periode)
                                ->first();
    
                            if (!$existingPenagihan) {
                                DataPenagihan::create([
                                    'npwpd' => $dataPiutang->npwpd,
                                    'nama_pajak' => $dataPiutang->nama_pajak,
                                    'alamat' => $dataPiutang->alamat,
                                    'jenis_pajak_id' => $dataPiutang->jenis_pajak_id,
                                    'kategori_pajak_id' => $dataPiutang->kategori_pajak_id,
                                    'jumlah_penagihan' => $dataPiutang->jumlah_penagihan,
                                    'pembagian_zonasi' => $dataPiutang->pembagian_zonasi,
                                    'status' => 'Belum Bayar',
                                    'periode' => $dataPiutang->periode, // Menambahkan periode yang tepat
                                ]);
                                
                                Log::info('Data Penagihan berhasil dibuat untuk NPWPD: ' . $dataPiutang->npwpd);
                            }
                        } else {
                            Log::error('Gagal menyimpan pembagian zonasi untuk ID: ' . $id);
                        }
                    } else {
                        Log::warning('Data Piutang tidak ditemukan untuk ID: ' . $id);
                    }
                }
            }
    
            // Jika ada yang berhasil disimpan, beri pesan sukses
            if ($success) {
                return redirect()->back()->with('success', 'Pembagian zonasi berhasil disimpan dan dipindahkan ke Data Penagihan.');
            } else {
                return redirect()->back()->with('error', 'Tidak ada perubahan yang disimpan.');
            }
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat menyimpan zonasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan zonasi: ' . $e->getMessage());
        }
    }
    

// PROSES LAPORANPELUNASAN
    // public function saveZonasi(Request $request)
    // {
    //     $zonasiData = $request->input('pembagian_zonasi'); // Array of zonasi data
        
    //     // Cek apakah ada data yang dipilih
    //     if (!$zonasiData || empty(array_filter($zonasiData))) {
    //         return redirect()->back()->with('error', 'Pemilihan pembagian zonasi belum dipilih.');
    //     }
        
    //     try {
    //         $success = false; // Flag untuk mengecek apakah ada data yang berhasil disimpan
        
    //         foreach ($zonasiData as $id => $zonasi) {
    //             if (!empty($zonasi)) {
    //                 // Cari data Piutang berdasarkan ID
    //                 $dataPiutang = DataPiutang::find($id);
    //                 if ($dataPiutang) {
    //                     // Cek jika data sudah ada di LaporanPelunasan
    //                     $existingLaporanPelunasan = LaporanPelunasan::where('npwpd', $dataPiutang->npwpd)
    //                         ->where('periode', $dataPiutang->periode)
    //                         ->first();
    
    //                     if ($existingLaporanPelunasan) {
    //                         // Jika sudah ada di LaporanPelunasan, hapus dari DataPenagihan
    //                         DataPenagihan::where('npwpd', $dataPiutang->npwpd)
    //                             ->where('periode', $dataPiutang->periode)
    //                             ->delete();
    //                     }
                        
    //                     // Update pembagian zonasi
    //                     $dataPiutang->pembagian_zonasi = $zonasi;
    //                     if ($dataPiutang->save()) {
    //                         // Log jika pembagian zonasi berhasil disimpan
    //                         Log::info('Zonasi berhasil disimpan untuk ID: ' . $id);
    //                         $success = true; // Set flag ke true jika ada yang berhasil disimpan
                            
    //                         // Pindahkan data ke DataPenagihan jika belum ada
    //                         $existingPenagihan = DataPenagihan::where('npwpd', $dataPiutang->npwpd)
    //                             ->where('periode', $dataPiutang->periode)
    //                             ->first();
    
    //                         if (!$existingPenagihan) {
    //                             DataPenagihan::create([
    //                                 'npwpd' => $dataPiutang->npwpd,
    //                                 'nama_pajak' => $dataPiutang->nama_pajak,
    //                                 'alamat' => $dataPiutang->alamat,
    //                                 'jenis_pajak_id' => $dataPiutang->jenis_pajak_id,
    //                                 'kategori_pajak_id' => $dataPiutang->kategori_pajak_id,
    //                                 'jumlah_penagihan' => $dataPiutang->jumlah_penagihan,
    //                                 'pembagian_zonasi' => $dataPiutang->pembagian_zonasi,
    //                                 'status' => 'Belum Bayar',
    //                                 'periode' => $dataPiutang->periode, // Menambahkan periode yang tepat
    //                             ]);
                                
    //                             Log::info('Data Penagihan berhasil dibuat untuk NPWPD: ' . $dataPiutang->npwpd);
    //                         }
    //                     } else {
    //                         Log::error('Gagal menyimpan pembagian zonasi untuk ID: ' . $id);
    //                     }
    //                 } else {
    //                     Log::warning('Data Piutang tidak ditemukan untuk ID: ' . $id);
    //                 }
    //             }
    //         }
        
    //         // Jika ada yang berhasil disimpan, beri pesan sukses
    //         if ($success) {
    //             return redirect()->back()->with('success', 'Pembagian zonasi berhasil disimpan dan dipindahkan ke Data Penagihan.');
    //         } else {
    //             return redirect()->back()->with('error', 'Tidak ada perubahan yang disimpan.');
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Terjadi kesalahan saat menyimpan zonasi: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan zonasi: ' . $e->getMessage());
    //     }
    // }
    
    public function exportExcel()
{
    return Excel::download(new DataPiutangExport, 'data_piutang.xlsx');
}

public function exportPdf()
{
    $dataPiutang = DataPiutang::orderBy('created_at', 'desc')->get();


    // Kirim data ke view khusus untuk PDF
    $pdf = Pdf::loadView('admin.data_piutang.pdf', compact('dataPiutang'));

    // Unduh file PDF
    return $pdf->download('data_piutang.pdf');
}

public function filter(Request $request)
{
    $query = LaporanPiutang::query();
    
    if ($request->has('jenis_pajak_id') && $request->jenis_pajak_id != '') {
        $query->where('jenis_pajak_id', $request->jenis_pajak_id);
    }

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nama_pajak', 'LIKE', '%' . $search . '%')
              ->orWhere('alamat', 'LIKE', '%' . $search . '%');
        });
    }
    
    // $dataPelunasan = $query->get();
    $laporanPiutang = $query->orderBy('created_at', 'desc')->get();

    
    return view('admin.laporan_piutang.data', compact('laporanPiutang'));
}
}