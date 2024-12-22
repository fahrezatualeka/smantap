<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak</title>
</head>
<body>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <h1>Daftar Kontak</h1>
    <a href="{{ route('contacts.create') }}">Tambah Kontak</a>

    <table border="1">
        <tr>
            <th>Nama</th>
            <th>Nomor Telepon</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
        @foreach ($contacts as $contact)
        <tr>
            <td>{{ $contact->nama }}</td>
            <td>{{ $contact->nomor_telepon }}</td>
            <td>
                @if ($contact->gambar)
                    <img src="{{ asset('storage/' . $contact->gambar) }}" alt="Gambar" width="100">
                @else
                    <em>Tidak ada gambar</em>
                @endif
            </td>
            <td>
                <a href="{{ route('contacts.edit', $contact->id) }}">Edit</a>
                <a href="{{ route('contacts.sendMessage', $contact->id) }}">Kirim Pesan</a>
                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>