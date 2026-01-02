<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Login - SIPASPUTIH</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            height: 100%;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            height: 100%;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            position: relative;
            overflow: auto;
            padding: 20px;
        }

        /* Animated Background Elements */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -250px;
            right: -250px;
            animation: float 6s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            bottom: -200px;
            left: -200px;
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(20px); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .container {
            width: 100%;
            max-width: 450px;
            padding: 50px 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
            z-index: 10;
            animation: slideIn 0.6s ease-out;
            margin: auto;
        }

        .logo-container {
            margin-bottom: 30px;
            animation: pulse 2s ease-in-out infinite;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(17, 153, 142, 0.4);
        }

        .logo i {
            color: white;
            font-size: 40px;
        }

        .container h2 {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .container h4 {
            color: #666;
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 40px;
            letter-spacing: 0.5px;
        }

        .textbox {
            position: relative;
            margin-bottom: 25px;
        }

        .textbox i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #11998e;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .textbox input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
            background: white;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            touch-action: manipulation;
        }

        .textbox input:focus {
            border-color: #11998e;
            box-shadow: 0 0 20px rgba(17, 153, 142, 0.2);
            outline: none;
        }

        .textbox input:focus + i {
            color: #38ef7d;
            transform: translateY(-50%) scale(1.1);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(17, 153, 142, 0.3);
            margin-top: 10px;
            position: relative;
            overflow: hidden;
            touch-action: manipulation;
            -webkit-user-select: none;
            user-select: none;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%);
            transition: all 0.4s ease;
        }

        .btn span {
            position: relative;
            z-index: 1;
        }

        .btn:hover::before {
            left: 0;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(17, 153, 142, 0.4);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.3);
        }

        /* Touch feedback for mobile */
        @media (hover: none) and (pointer: coarse) {
            .btn:active {
                transform: scale(0.98);
            }
        }

        .register-text {
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }

        .register-text a {
            color: #11998e;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-text a:hover {
            color: #38ef7d;
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #999;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }

        .divider::before {
            margin-right: 10px;
        }

        .divider::after {
            margin-left: 10px;
        }

        .error-message {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.4s ease-out;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
            border-left: 4px solid #ff4757;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .error-message::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shimmer 2s infinite;
        }

        .error-message i {
            font-size: 16px;
            flex-shrink: 0;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Responsif untuk Tablet */
        @media (max-width: 768px) and (min-width: 481px) {
            body {
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .container {
                max-width: 420px;
                padding: 45px 35px;
                margin: auto;
            }

            body::before,
            body::after {
                opacity: 0.5;
            }
        }

        /* Responsif untuk Mobile */
        @media (max-width: 480px) {
            html {
                height: 100%;
                height: -webkit-fill-available;
            }

            body {
                height: 100vh;
                height: -webkit-fill-available;
                min-height: 100vh;
                min-height: -webkit-fill-available;
                padding: max(15px, env(safe-area-inset-top)) max(15px, env(safe-area-inset-right)) max(15px, env(safe-area-inset-bottom)) max(15px, env(safe-area-inset-left));
                display: flex;
                justify-content: center;
                align-items: center;
                overflow-y: auto;
            }

            body::before {
                width: 300px;
                height: 300px;
                top: -150px;
                right: -150px;
            }

            body::after {
                width: 250px;
                height: 250px;
                bottom: -125px;
                left: -125px;
            }

            .container {
                width: 100%;
                max-width: 100%;
                padding: 35px 25px;
                margin: auto;
                border-radius: 16px;
                flex-shrink: 0;
            }

            .logo-container {
                margin-bottom: 25px;
            }

            .logo {
                width: 65px;
                height: 65px;
            }

            .logo i {
                font-size: 32px;
            }

            .container h2 {
                font-size: 26px;
                margin-bottom: 6px;
            }

            .container h4 {
                font-size: 13px;
                margin-bottom: 30px;
            }

            .textbox {
                margin-bottom: 20px;
            }

            .textbox input {
                padding: 14px 14px 14px 45px;
                font-size: 16px; /* Minimum 16px untuk mencegah auto-zoom di iOS */
                min-height: 48px; /* Touch target minimum */
            }

            .textbox i {
                font-size: 16px;
                left: 14px;
            }

            .btn {
                padding: 14px;
                font-size: 17px;
                min-height: 48px; /* Touch target minimum */
            }

            .register-text {
                margin-top: 20px;
                font-size: 13px;
            }

            .error-message {
                font-size: 13px;
                padding: 12px 15px;
                margin-bottom: 20px;
            }

            .error-message i {
                font-size: 14px;
            }
        }

        /* Responsif untuk Mobile Kecil */
        @media (max-width: 380px) {
            body {
                padding: 12px;
            }

            .container {
                padding: 30px 20px;
            }

            .logo {
                width: 60px;
                height: 60px;
            }

            .logo i {
                font-size: 28px;
            }

            .container h2 {
                font-size: 24px;
            }

            .container h4 {
                font-size: 12px;
            }

            .textbox input {
                padding: 13px 13px 13px 42px;
                font-size: 16px; /* Keep 16px minimum */
                min-height: 46px;
            }

            .textbox i {
                font-size: 15px;
                left: 12px;
            }

            .btn {
                padding: 13px;
                font-size: 16px;
                min-height: 46px;
            }
        }

        /* Landscape Mode untuk Mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            html, body {
                height: 100%;
                min-height: 100vh;
            }

            body {
                padding: 15px 10px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .container {
                padding: 20px 30px;
                margin: auto;
                max-width: 500px;
            }

            .logo-container {
                margin-bottom: 15px;
            }

            .logo {
                width: 50px;
                height: 50px;
            }

            .logo i {
                font-size: 24px;
            }

            .container h2 {
                font-size: 22px;
                margin-bottom: 5px;
            }

            .container h4 {
                font-size: 11px;
                margin-bottom: 20px;
            }

            .textbox {
                margin-bottom: 15px;
            }

            .textbox input {
                padding: 11px 11px 11px 40px;
                font-size: 14px;
            }

            .btn {
                padding: 11px;
                font-size: 15px;
                margin-top: 5px;
            }
        }

        /* Loading animation for button */
        .btn.loading {
            pointer-events: none;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-user-md"></i>
            </div>
        </div>
        {{-- <h2>SIPASPUTIH</h2> --}}
        <h4>Sistem Pelaporan Pasukan Putih</h4>
        
        @if(session('error'))
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            @foreach($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf
            <div class="textbox">
                <input id="email" type="email" name="email" placeholder="Masukkan Email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="textbox">
                <input id="password" type="password" name="password" placeholder="Masukkan Password" required autocomplete="current-password">
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="btn" id="loginBtn">
                <span>Masuk</span>
            </button>
        </form>
        {{-- <div class="divider">atau</div> --}}
        {{-- <p class="register-text">Belum punya akun? <a href="#">Daftar Sekarang</a></p> --}}
    </div>

    <script>
        // Add loading animation on form submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.querySelector('span').style.opacity = '0';
        });

        // Add focus animation to inputs
        const inputs = document.querySelectorAll('.textbox input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
