<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataWajibPajak;
use App\Models\JenisPajak;
use App\Models\KategoriPajak;
use App\Models\DataPelunasan;
use App\Models\DataPenetapan;
use App\Models\DataZonasi;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataWajibPajakImport;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Log;
use App\Exports\DataWajibPajakExport;
use Barryvdh\DomPDF\Facade\Pdf;

class DataWajibPajakController extends Controller
{

    public function index()
    {
        // Ambil data dengan relasi dan urutkan berdasarkan data terbaru
        $data = DataWajibPajak::with(['jenisPajak', 'kategoriPajak'])
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at secara menurun
            ->get();
    
        $jumlahData = $data->count(); // Hitung jumlah data setelah filter diterapkan
    
        return view('admin.data_wajibpajak.data', compact('data', 'jumlahData'));
    }

    public function import(Request $request)
    {
        // Validasi file yang diupload
        $request->validate([
            'import_data_wajibpajak' => 'required|file|mimes:xlsx,xls', // Membatasi tipe file ke xlsx dan xls
        ]);
    
        try {
            // Proses impor data
            Excel::import(new DataWajibPajakImport, $request->file('import_data_wajibpajak'));
    
            // Redirect kembali dengan pesan sukses
            return redirect()->back()->with('success', 'Data berhasil diimpor!');
        } catch (\Exception $e) {
            // Tangkap dan log error
            Log::error('Error during import: ' . $e->getMessage());
    
            // Redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat impor data!');
        }
    }

    public function create()
    {
        $jenisPajak = JenisPajak::all();
        $kategoriPajak = KategoriPajak::all(); // Tambahkan ini
        return view('admin.data_wajibpajak.add', compact('jenisPajak', 'kategoriPajak'));
    }
    
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_pajak' => 'required',
            'alamat' => 'required',
            'npwpd' => 'nullable',
            // 'nomor_telepon' => 'nullable',
            'jenis_pajak_id' => 'required|exists:jenispajak,id',
            'kategori_pajak_id' => 'required|exists:kategoripajak,id',
            // 'jumlah_piutang' => 'nullable|numeric',  // pastikan itu angka
        ]);

        // Pastikan jumlah_piutang adalah angka setelah dihapus simbol non-numerik
        // $jumlahPiutang = preg_replace('/[^0-9.]/', '', $request->jumlah_piutang);  // menghapus karakter non-numerik
        // $jumlahPiutang = floatval($jumlahPiutang);  // konversi menjadi float
        
        // Simpan data baru
        DataWajibPajak::create([
            'nama_pajak' => $request->nama_pajak,
            'alamat' => $request->alamat,
            'npwpd' => $request->npwpd,
            // 'nomor_telepon' => $request->nomor_telepon,
            'jenis_pajak_id' => $request->jenis_pajak_id,
            'kategori_pajak_id' => $request->kategori_pajak_id,
            // 'jumlah_piutang' => $jumlahPiutang,  // simpan sebagai angka
        ]);
        
        // Redirect dengan pesan sukses
        return redirect()->route('admin.data_wajibpajak.data')->with('success', 'Data berhasil ditambahkan!');
    }
    
    



    public function edit($id)
    {
        $dataWajibPajak = DataWajibPajak::findOrFail($id);
        $jenisPajak = JenisPajak::all();
        $kategoriPajak = KategoriPajak::where('jenis_pajak_id', $dataWajibPajak->jenis_pajak_id)->get();
    
        return view('admin.data_wajibpajak.edit', compact('dataWajibPajak', 'jenisPajak', 'kategoriPajak'));
    }
    
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama_pajak' => 'required',
            'alamat' => 'required',
            'npwpd' => 'nullable',
            'jenis_pajak_id' => 'required|exists:jenispajak,id',
            'kategori_pajak_id' => 'required|exists:kategoripajak,id',
        ]);
        
        // Cari data berdasarkan ID
        $dataWajibPajak = DataWajibPajak::findOrFail($id);
        
        // Simpan perubahan pada DataWajibPajak
        $dataWajibPajak->update([
            'nama_pajak' => $request->nama_pajak,
            'alamat' => $request->alamat,
            'npwpd' => $request->npwpd,
            'jenis_pajak_id' => $request->jenis_pajak_id,
            'kategori_pajak_id' => $request->kategori_pajak_id,
        ]);
    
        // Update data di tabel DataPenetapan jika ada perubahan pada DataWajibPajak
        // Pastikan npwpd atau kolom terkait diperbarui di DataPenetapan
        $dataPenetapan = DataPenetapan::where('npwpd', $dataWajibPajak->npwpd)->first();
        if ($dataPenetapan) {
            $dataPenetapan->update([
                'nama_pajak' => $dataWajibPajak->nama_pajak,
                'alamat' => $dataWajibPajak->alamat,
                'jenis_pajak_id' => $dataWajibPajak->jenis_pajak_id,
                'kategori_pajak_id' => $dataWajibPajak->kategori_pajak_id,
            ]);
        }
    
        // Redirect dengan pesan sukses
        return redirect()->route('admin.data_wajibpajak.data')->with('success', 'Data berhasil diperbarui!');
    }
    
    
    
    
    

        // public function getKategoriPajak($jenisPajakId)
        // {
        //     $kategoriPajak = KategoriPajak::where('jenis_pajak_id', $jenisPajakId)->get(['id', 'nama']);
            
        //     // Pastikan data ditemukan
        //     if ($kategoriPajak->isEmpty()) {
        //         return response()->json(['message' => 'Tidak ada kategori pajak untuk jenis pajak ini.'], 404);
        //     }
            
        //     // Log untuk memastikan data kategori pajak ditemukan
        //     Log::info('Kategori Pajak:', $kategoriPajak->toArray());
            
        //     return response()->json($kategoriPajak, 200);
        // }



    // PIUTANG
    // public function updateTagihanPiutang(Request $request)
    // {
    //     // Melakukan validasi
    //     $validator = \Validator::make($request->all(), [
    //         'tanggal_tagihan' => 'required|array',
    //         'tanggal_tagihan.*' => 'required|date',
    //         'jumlah_piutang' => 'required|array',
    //         'jumlah_piutang.*' => 'required|numeric',
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Ada kesalahan dalam validasi',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }
    
    //     foreach ($request->tanggal_tagihan as $id => $tanggal) {
    //         $dataWajibPajak = DataWajibPajak::find($id);
    //         if ($dataWajibPajak) {
    //             $dataWajibPajak->tanggal_tagihan = $tanggal;
    //             $dataWajibPajak->jumlah_piutang = $request->jumlah_piutang[$id];
    //             $dataWajibPajak->is_readonly = true; // Set readonly setelah perubahan
    //             $dataWajibPajak->save();
    //         }
    //     }
    
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Perubahan berhasil disimpan',
    //     ]);
    // } 
    
    public function filter(Request $request)
    {
        $validated = $request->validate([
            'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id', // Sesuaikan nama tabel
            'kategori_pajak_id' => 'nullable|integer|exists:kategoripajak,id', // Sesuaikan nama tabel
            'search' => 'nullable|string|max:255',
        ]);
    
        $query = DataWajibPajak::query();
    
        if ($request->filled('jenis_pajak_id')) {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }
    
        if ($request->filled('kategori_pajak_id')) {
            $query->where('kategori_pajak_id', $request->kategori_pajak_id);
        }
    
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pajak', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
            });
        }
    
        // Tambahkan pengurutan berdasarkan data terbaru
        $data = $query->orderBy('created_at', 'desc')->get();
    
        return view('admin.data_wajibpajak.data', compact('data'));
    }
    
    

    // public function getDataByNpwpd($npwpd)
    // {
    //     $data = DataWajibPajak::where('npwpd', $npwpd)->first();
    //     return response()->json($data);
    // }
    
    // public function markAsLunas(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required|exists:datawajibpajak,id',
    //     ]);
    
    //     $dataWajibPajak = DataWajibPajak::find($request->id);
    
    //     if (!$dataWajibPajak) {
    //         return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    //     }
    
    //     // Cek jika nomor telepon kosong sebelum melanjutkan
    //     if (empty($dataWajibPajak->nomor_telepon)) {
    //         return response()->json(['success' => false, 'message' => 'Nomor telepon harus diisi terlebih dahulu']);
    //     }
    
    //     if ($dataWajibPajak->status_lunas == 'Lunas') {
    //         return response()->json(['success' => false, 'message' => 'Data sudah lunas']);
    //     }
    
    //     // Update status pelunasan
    //     $dataWajibPajak->status_lunas = 'Lunas';
    //     $dataWajibPajak->save();
    
    //     // Menghapus data terkait di DataZonasi
    //     DataZonasi::where('npwpd', $dataWajibPajak->npwpd)->delete();
    
    //     // Memindahkan data ke tabel DataPelunasan
    //     $dataPelunasan = new DataPelunasan();
    //     $dataPelunasan->fill([
    //         'nama_pajak' => $dataWajibPajak->nama_pajak,
    //         'alamat' => $dataWajibPajak->alamat,
    //         'npwpd' => $dataWajibPajak->npwpd,
    //         // 'nomor_telepon' => $dataWajibPajak->nomor_telepon,
    //         'jenis_pajak_id' => $dataWajibPajak->jenis_pajak_id,
    //         'kategori_pajak_id' => $dataWajibPajak->kategori_pajak_id,
    //         'tanggal_pelunasan' => now(),
    //     ]);
    //     $dataPelunasan->save();
    
    //     return response()->json(['success' => true, 'message' => 'Status berhasil diubah menjadi Lunas dan data Zonasi dihapus.']);
    // }
    
    
    
    



    public function destroy($id)
    {
        // Cari data yang akan dihapus
        $dataWajibPajak = DataWajibPajak::findOrFail($id);
    
        // Hapus data terkait di tabel DataPenetapan berdasarkan npwpd
        DataPenetapan::where('npwpd', $dataWajibPajak->npwpd)->delete();
    
        // Hapus data wajib pajak itu sendiri
        $dataWajibPajak->delete();
    
        // Redirect dengan pesan sukses
        return redirect()->route('admin.data_wajibpajak.data')->with('success', 'Data berhasil dihapus!');
    }



    // protected $fonnteService;

    // // Menyuntikkan FonnteService ke dalam controller
    // public function __construct(FonnteService $fonnteService)
    // {
    //     $this->fonnteService = $fonnteService;
    // }

    // public function sendWhatsApp(Request $request)
    // {
    //     $request->validate([
    //         'to' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
    //         'message' => 'required|string',
    //     ]);
    
    //     \Log::info('Nomor Telepon dan Pesan:', ['to' => $request->to, 'message' => $request->message]);
    
    //     // Mengirim pesan menggunakan FonnteService
    //     $response = $this->fonnteService->sendMessage($request->to, $request->message);
    
    //     // Menangani respons dari API
    //     if ($response['status'] === 'success') {
    //         return redirect()->back()->with('success', $response['message']);
    //     } else {
    //         \Log::error('Fonnte API Error Response:', ['error_message' => $response['message']]);
    //         return redirect()->back()->with('error', $response['message']);
    //     }
    // }

    public function exportExcel()
    {
        return Excel::download(new DataWajibPajakExport, 'data_wajibpajak.xlsx');
    }
    
    public function exportPdf()
    {
        $dataWajibPajak = DataWajibPajak::with(['jenisPajak', 'kategoriPajak'])
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at secara menurun
            ->get();
    
        $jumlahData = $dataWajibPajak->count(); // Hitung jumlah data setelah filter diterapkan
    
        // Kirim data ke view khusus untuk PDF
        $pdf = Pdf::loadView('admin.data_wajibpajak.pdf', compact('dataWajibPajak'));
    
        // Unduh file PDF
        return $pdf->download('data_wajibpajak.pdf');
    }
}