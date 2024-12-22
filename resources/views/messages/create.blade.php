<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pesan</title>
</head>
<body>

    <h1>Tambah Pesan</h1>

    <form action="{{ route('messages.store') }}" method="POST">
        @csrf
        <label for="title">Judul Pesan:</label>
        <input type="text" name="title" id="title" required>
        <br><br>

        <label for="content">Isi Pesan:</label>
        <textarea name="content" id="content" required></textarea>
        <br><br>

        <button type="submit">Simpan</button>
    </form>

</body>
</html>