<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penutupan BAPENDA Kota Ambon</title>
    <style>
                @page {
            size: A4 landscape;
            margin: 5mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            padding: 5px;
            vertical-align: middle; /* Menyelaraskan teks secara vertikal */
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Data Penutupan Petugas Penagihan Zona {{ Auth::user()->zona ?? 'Tidak Ditentukan' }} BAPENDA Kota Ambon</h2>
    <table>
        <thead>
            <tr>
                <th >No</th>
                <th >Nama Pajak</th>
                <th >Alamat</th>
                <th >NPWPD</th>
                <th >Jenis Pajak</th>
                {{-- <th >Kategori Pajak</th> --}}
                <th >Telepon</th>
                {{-- <th >Zona</th> --}}
                {{-- <th >Jumlah Penagihan</th> --}}
                <th >Periode</th>
                {{-- <th>Jumlah Pembayaran</th> --}}
                <th >Tanggal Kunjungan  </th>
                {{-- <th >Jumlah Pembayaran</th> --}}
                {{-- <th >Bukti Pembayaran</th> --}}
                {{-- <th >Bukti SSPD</th> --}}
                <th >Keterangan</th>
                {{-- <th >Pengirim</th> --}}
                {{-- <th>Status</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($dataPenutupan as $index => $penutupan)
                <tr>
                    <td >{{ $index + 1 }}</td>
                    <td >{{ $penutupan->nama_pajak }}</td>
                    <td >{{ $penutupan->alamat }}</td>
                    <td >{{ $penutupan->npwpd }}</td>
                    <td >{{ $penutupan->jenisPajak->jenispajak ?? '-' }}</td>
                    {{-- <td >{{ $pelunasan->kategoriPajak->kategoripajak ?? '-' }}</td> --}}
                    <td >{{ $penutupan->telepon }}</td>
                    {{-- <td >{{ $penutupan->zona }}</td> --}}
                    <td >{{ $penutupan->periode }}</td>
                    <td>{{ \Carbon\Carbon::parse($penutupan->tanggal_kunjungan)->locale('id')->isoFormat('D MMMM YYYY') }}</td>

                    {{-- <td >Rp{{ number_format($penutupan->jumlah_pembayaran, 0, ',', '.') }}</td> --}}
                    {{-- <td >{{ $penutupan->buktipembayaran }}</td> --}}
                    {{-- <td >{{ $penutupan->buktisspd }}</td> --}}
                    <td>{{ $penutupan->keterangan ?? 'Tidak ada' }}</td>

                    {{-- <td >{{ $penutupan->pengirim }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
