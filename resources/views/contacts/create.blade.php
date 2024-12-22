<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kontak</title>
</head>
<body>
    <h1>Tambah Kontak</h1>

    <form action="{{ route('contacts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="nama">Nama:</label>
        <input type="text" name="nama" id="nama" required>
        <br>
    
        <label for="nomor_telepon">Nomor Telepon:</label>
        <input type="text" name="nomor_telepon" id="nomor_telepon" required>
        <br>
    
        <label for="gambar">Upload Gambar:</label>
        <input type="file" name="gambar" id="gambar">
        <br>
    
        <button type="submit">Simpan</button>
    </form>
</body>
</html>