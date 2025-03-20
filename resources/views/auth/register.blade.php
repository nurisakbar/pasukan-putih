<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rekam Medis Elektronik</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('{{ asset('assets/dist/assets/img/bg-login-min.jpg') }}') no-repeat center center/cover;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .container h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .textbox {
            position: relative;
            margin-bottom: 20px;
        }

        .textbox input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }

        .textbox input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.5);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #45a049;
            transform: scale(1.05);
        }

        .register-text {
            margin-top: 10px;
            color: #333;
        }

        .register-text a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        /* Responsif */
        @media (max-width: 480px) {
            .container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Rekam Medis Elektronik</h2>
        <h2>Register</h2>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="textbox">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Name">

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="textbox">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="textbox">
                <input id="no_wa" type="number" class="form-control @error('no_wa') is-invalid @enderror" name="no_wa" value="{{ old('no_wa') }}" required autocomplete="no_wa" placeholder="No Whatsapp">

                @error('no_wa')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="textbox">
                <input id="keterangan" type="text" class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" value="{{ old('keterangan') }}" required autocomplete="keterangan" placeholder="Keterangan">

                @error('keterangan')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <input type="hidden" name="role" value="superadmin">
            <div class="textbox">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="textbox">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Password Confirm">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        {{-- <p class="register-text">Belum punya akun? <a href="#">Daftar</a></p> --}}
    </div>
</body>
</html>
