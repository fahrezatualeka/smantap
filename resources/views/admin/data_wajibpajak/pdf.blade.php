<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Wajib Pajak BAPENDA Kota Ambon</title>
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
    <h2>Data Wajib Pajak BAPENDA Kota Ambon</h2>
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
                {{-- <th >Periode</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($dataWajibPajak as $index => $datawajibpajak)
                <tr>
                    <td >{{ $index + 1 }}</td>
                    <td >{{ $datawajibpajak->nama_pajak }}</td>
                    <td >{{ $datawajibpajak->alamat }}</td>
                    <td >{{ $datawajibpajak->npwpd }}</td>
                    <td >{{ $datawajibpajak->jenisPajak->jenispajak ?? '-' }}</td>
                    <td >{{ $datawajibpajak->telepon }}</td>
                    <td >{{ $datawajibpajak->zona }}</td>
                    {{-- <td >{{ $datawajibpajak->periode }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
