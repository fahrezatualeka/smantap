<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penetapan Pajak Pemerintah Kota Ambon</title>
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
            padding: 0px;
            vertical-align: middle; /* Menyelaraskan teks ke tengah secara vertikal */
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Data Penetapan Pajak Pemerintah Kota Ambon</h2>
    <table>
        <thead>
            <tr>
                <th style="text-align: center">No</th>
                <th style="text-align: center">Nama Pajak</th>
                <th style="text-align: center">Alamat</th>
                <th style="text-align: center">NPWPD</th>
                <th style="text-align: center">Jenis Pajak</th>
                <th style="text-align: center">Kategori Pajak</th>
                <th style="text-align: center">Nomor Telepon</th>
                <th style="text-align: center">Pembagian Zonasi</th>
                <th style="text-align: center">Jumlah Penagihan</th>
                <th style="text-align: center">Periode</th>
                <th style="text-align: center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataPenetapan as $index => $penetapan)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td style="text-align: center">{{ $penetapan->nama_pajak }}</td>
                    <td style="text-align: center">{{ $penetapan->alamat }}</td>
                    <td style="text-align: center">{{ $penetapan->npwpd }}</td>
                    <td style="text-align: center">{{ $penetapan->jenisPajak->jenispajak ?? '-' }}</td>
                    <td style="text-align: center">{{ $penetapan->kategoriPajak->kategoripajak ?? '-' }}</td>
                    <td style="text-align: center">{{ $penetapan->nomor_telepon }}</td>
                    <td style="text-align: center">{{ $penetapan->pembagian_zonasi }}</td>
                    <td style="text-align: center">Rp{{ number_format($penetapan->jumlah_penagihan, 0, ',', '.') }}</td>
                    <td style="text-align: center">{{ $penetapan->periode }}</td>
                    <td style="text-align: center">{{ $penetapan->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
