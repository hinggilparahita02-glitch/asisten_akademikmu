<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Asisten Akademik Harian</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <h1 class="login-title">Asisten Akademikmu</h1>

            @if(session('error'))
                <div class="error-message" style="color: red; margin-bottom: 10px;">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="/login" class="login-form">
                @csrf <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" placeholder="Masukkan nama Anda" value="{{ old('name') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label for="class">Kelas</label>
                    <input type="text" id="class" name="class" placeholder="Masukkan kelas Anda" value="{{ old('class') }}" required>
                </div>

                <button type="submit" class="login-btn">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>