<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesan</title>
</head>
<body>

    <h1>Daftar Pesan</h1>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <a href="{{ route('messages.create') }}">Tambah Pesan</a>

    <table border="1">
        <tr>
            <th>Judul Pesan</th>
            <th>Isi Pesan</th>
            <th>Aksi</th>
        </tr>
        @foreach ($messages as $message)
        <tr>
            <td>{{ $message->title }}</td>
            <td>{{ \Str::limit($message->content, 50) }}</td>
            <td>
                <a href="{{ route('messages.edit', $message->id) }}">Edit</a>
                <form action="{{ route('messages.destroy', $message->id) }}" method="POST" style="display:inline;">
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