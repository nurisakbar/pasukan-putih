<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Kunjungan</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
               <th>No</th>
               <th>Name Pasien</th>
               <th>NIK</th>
               <th>Jenis Kelamin</th>
               <th>Nomor Hp</th>
               <th>Tanggal Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kunjungans as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td width="auto">{{ $data->pasien->name ?? '-' }}</td>
                    <td width="auto">{{ $data->pasien->nik ?? '-' }}</td>
                    <td width="auto">{{ $data->pasien->jenis_kelamin ?? '-' }}</td>
                    <td width="auto">{{ $data->pasien->nomor_hp ?? '-' }}</td>
                    <td width="auto">{{ $data->tanggal ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
