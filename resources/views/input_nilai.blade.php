<!DOCTYPE html>
<html>
<head>
    <title>Input Nilai</title>
</head>
<body>

<h2>Form Input Nilai</h2>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<form action="/simpan-nilai" method="POST">
    @csrf

    <label>Siswa:</label><br>
    <select name="id_siswa">
        @foreach($siswa as $s)
            <option value="{{ $s->id }}">{{ $s->nama }}</option>
        @endforeach
    </select>
    <br><br>

    <label>Kategori:</label><br>
    <select name="id_kategori">
        @foreach($kategori as $k)
            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
        @endforeach
    </select>
    <br><br>

    <label>Nilai:</label><br>
    <input type="number" name="nilai">
    <br><br>

    <button type="submit">Simpan</button>
</form>

</body>
</html>