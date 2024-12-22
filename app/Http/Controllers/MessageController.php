<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Menampilkan daftar pesan
    public function index()
    {
        $messages = Message::all();
        return view('messages.index', compact('messages'));
    }

    // Menampilkan form untuk membuat pesan baru
    public function create()
    {
        return view('messages.create');
    }

    // Menyimpan pesan baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Message::create($request->all());

        return redirect()->route('messages.index')->with('success', 'Pesan berhasil ditambahkan!');
    }

    // Menampilkan form untuk mengedit pesan
    public function edit($id)
    {
        $message = Message::findOrFail($id);
        return view('messages.edit', compact('message'));
    }

    // Memperbarui pesan
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $message = Message::findOrFail($id);
        $message->update($request->all());

        return redirect()->route('messages.index')->with('success', 'Pesan berhasil diperbarui!');
    }

    // Menghapus pesan
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return redirect()->route('messages.index')->with('success', 'Pesan berhasil dihapus!');
    }
}