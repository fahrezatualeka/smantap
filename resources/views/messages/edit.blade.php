<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesan</title>
</head>
<body>

    <h1>Edit Pesan</h1>

    <form action="{{ route('messages.update', $message->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="title">Judul Pesan:</label>
        <input type="text" name="title" id="title" value="{{ old('title', $message->title) }}" required>
        <br><br>

        <label for="content">Isi Pesan:</label>
        <textarea name="content" id="content" required>{{ old('content', $message->content) }}</textarea>
        <br><br>

        <button type="submit">Update</button>
    </form>

</body>
</html>