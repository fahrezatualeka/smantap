<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Models\Message;

class ContactController extends Controller
{
    // Menampilkan semua kontak
    public function index()
    {
        $contacts = Contact::all();
        return view('contacts.index', compact('contacts'));
    }

    // Menampilkan form tambah kontak
    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nomor_telepon' => 'required|regex:/^\+?\d{10,15}$/',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $data = $request->all();
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $path = $file->store('uploads', 'public');
            $data['gambar'] = $path;
        }
    
        Contact::create($data);
        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil ditambahkan!');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'nomor_telepon' => 'required|regex:/^\+?\d{10,15}$/',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $contact = Contact::findOrFail($id);
        $data = $request->all();
    
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $path = $file->store('uploads', 'public');
            $data['gambar'] = $path;
    
            // Hapus gambar lama jika ada
            if ($contact->gambar) {
                \Storage::disk('public')->delete($contact->gambar);
            }
        }
    
        $contact->update($data);
        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil diperbarui!');
    }

    // Mengirim pesan WhatsApp
    public function sendMessage($id)
    {
        $contact = Contact::findOrFail($id);
        $message = Message::first(); // Ambil pesan pertama dari database
    
        $apiUrl = 'https://api.fonnte.com/send';
        $apiKey = 'NehkJetr9zN3JaXXXqJb';
        $content = 'Halo, ' . $contact->nama . '! ' . $message->content . ', ' . asset('storage/' . $contact->gambar);
    
        $client = new Client();
        try {
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => $apiKey,
                ],
                'multipart' => [
                    [
                        'name' => 'target',
                        'contents' => $contact->nomor_telepon,
                    ],
                    [
                        'name' => 'message',
                        'contents' => $content,
                    ],
                    [
                        'name' => 'schedule',
                        'contents' => 0,
                    ],
                    [
                        'name' => 'typing',
                        'contents' => false,
                    ],
                    [
                        'name' => 'delay',
                        'contents' => 2,
                    ],
                    [
                        'name' => 'countryCode',
                        'contents' => '62',
                    ],
                ],
            ]);
    
            $status = json_decode($response->getBody()->getContents(), true);
            Log::info('Fonnte API Response:', $status);
    
            if (isset($status['status']) && $status['status'] == 'success') {
                return redirect()->route('contacts.index')->with('success', 'Pesan berhasil dikirim ke WhatsApp!');
            } else {
                return redirect()->route('contacts.index')->with('error', 'Gagal mengirim pesan: ' . $status['message'] ?? 'Tidak ada pesan error.');
            }
        } catch (\Exception $e) {
            Log::error('Fonnte API Error:', ['message' => $e->getMessage()]);
            return redirect()->route('contacts.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Menampilkan form edit kontak
    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        return view('contacts.edit', compact('contact'));
    }

    // Menghapus kontak
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil dihapus!');
    }
}