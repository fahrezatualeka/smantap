<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelunasan Pajak Pemerintah Kota Ambon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
                {{-- <th>Jumlah Pembayaran</th> --}}
                <th style="text-align: center">Tanggal Pembayaran  </th>
                <th style="text-align: center">Tempat Pembayaran</th>
                {{-- <th>Status</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($laporanPelunasan as $index => $pelunasan)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td style="text-align: center">{{ $pelunasan->nama_pajak }}</td>
                    <td style="text-align: center">{{ $pelunasan->alamat }}</td>
                    <td style="text-align: center">{{ $pelunasan->npwpd }}</td>
                    <td style="text-align: center">{{ $pelunasan->jenisPajak->jenispajak ?? '-' }}</td>
                    <td style="text-align: center">{{ $pelunasan->kategoriPajak->kategoripajak ?? '-' }}</td>
                    <td style="text-align: center">{{ $pelunasan->nomor_telepon }}</td>
                    <td style="text-align: center">{{ $pelunasan->pembagian_zonasi }}</td>
                    <td style="text-align: center">Rp{{ number_format($pelunasan->jumlah_penagihan, 0, ',', '.') }}</td>
                    <td style="text-align: center">{{ $pelunasan->periode }}</td>
                    {{-- <td>Rp{{ number_format($pelunasan->jumlah_pembayaran, 0, ',', '.') }}</td> --}}
                    <td style="text-align: center">{{ $pelunasan->tanggal_pembayaran }}</td>
                    <td style="text-align: center">{{ $pelunasan->tempat_pembayaran }}</td>
                    {{-- <td>{{ $penetapan->status }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
