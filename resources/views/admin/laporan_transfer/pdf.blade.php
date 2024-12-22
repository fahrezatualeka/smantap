<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transfer BAPENDA Kota Ambon</title>
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
            vertical-align: middle; 
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Laporan Transfer BAPENDA Kota Ambon</h2>
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
                <th >Zona</th>
                {{-- <th >Jumlah Penagihan</th> --}}
                <th >Periode</th>
                <th >Tanggal Pembayaran  </th>
                <th >Jumlah Pembayaran</th>
                {{-- <th >Bukti Pembayaran</th> --}}
                {{-- <th >Bukti SSPD</th> --}}
                <th >Keterangan</th>
                <th >Pengirim</th>
                {{-- <th>Status</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($laporanTransfer as $index => $transfer)
                <tr>
                    <td >{{ $index + 1 }}</td>
                    <td >{{ $transfer->nama_pajak }}</td>
                    <td >{{ $transfer->alamat }}</td>
                    <td >{{ $transfer->npwpd }}</td>
                    <td >{{ $transfer->jenisPajak->jenispajak ?? '-' }}</td>
                    {{-- <td >{{ $pelunasan->kategoriPajak->kategoripajak ?? '-' }}</td> --}}
                    <td >{{ $transfer->telepon }}</td>
                    <td >{{ $transfer->zona }}</td>
                    <td >{{ $transfer->periode }}</td>
                    <td>{{ \Carbon\Carbon::parse($transfer->tanggal_pembayaran)->locale('id')->isoFormat('D MMMM YYYY') }}</td>


                    <td >Rp{{ number_format($transfer->jumlah_pembayaran, 0, ',', '.') }}</td>
                    {{-- <td >{{ $transfer->buktipembayaran }}</td> --}}
                    {{-- <td >{{ $transfer->buktisspd }}</td> --}}
                    <td>{{ $transfer->keterangan ?? 'Tidak ada' }}</td>

                    <td >{{ $transfer->pengirim }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>