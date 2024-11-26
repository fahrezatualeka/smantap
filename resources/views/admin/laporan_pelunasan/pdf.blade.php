<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelunasan Pajak Pemerintah Kota Ambon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            vertical-align: middle; /* Menyelaraskan teks secara vertikal */
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Laporan Pelunasan Pajak Pemerintah Kota Ambon</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pajak</th>
                <th>Alamat</th>
                <th>NPWPD</th>
                <th>Jenis Pajak</th>
                <th>Kategori Pajak</th>
                <th>Jumlah Penagihan</th>
                {{-- <th>Jumlah Pembayaran</th> --}}
                <th>Tanggal Pembayaran  </th>
                <th>Tempat Pembayaran</th>
                {{-- <th>Periode</th> --}}
                {{-- <th>Status</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($laporanPelunasan as $index => $pelunasan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pelunasan->nama_pajak }}</td>
                    <td>{{ $pelunasan->alamat }}</td>
                    <td>{{ $pelunasan->npwpd }}</td>
                    <td>{{ $pelunasan->jenisPajak->jenispajak ?? '-' }}</td>
                    <td>{{ $pelunasan->kategoriPajak->kategoripajak ?? '-' }}</td>
                    <td>Rp{{ number_format($pelunasan->jumlah_penagihan, 0, ',', '.') }}</td>
                    {{-- <td>Rp{{ number_format($pelunasan->jumlah_pembayaran, 0, ',', '.') }}</td> --}}
                    <td>{{ $pelunasan->tanggal_pembayaran }}</td>
                    <td>{{ $pelunasan->tempat_pembayaran }}</td>
                    {{-- <td>{{ $pelunasan->periode }}</td> --}}
                    {{-- <td>{{ $penetapan->status }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
