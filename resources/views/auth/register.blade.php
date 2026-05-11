<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Planner — Kayıt Ol</title>
    <style>
        :root { --primary: #4a90e2; --bg: #f4f7f6; --dark: #2c3e50; --accent: #27ae60; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, 'SF Pro Display', 'Helvetica Neue', sans-serif; background: var(--bg); height: 100vh; display: flex; align-items: center; justify-content: center; }
        #register-card { background: white; padding: 35px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 360px; margin: 20px; }
        .card-title { color: var(--dark); margin: 0 0 6px 0; font-size: 24px; font-weight: 700; text-align: center; }
        .card-subtitle { color: #95a5a6; font-size: 13px; text-align: center; margin: 0 0 25px 0; }
        .input-group { margin-bottom: 18px; }
        label { display: block; font-weight: 600; font-size: 12px; margin-bottom: 6px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.4px; }
        input { width: 100%; padding: 12px 14px; border: 1.5px solid #eee; border-radius: 8px; font-size: 15px; transition: border-color 0.2s; font-family: inherit; }
        input:focus { border-color: var(--primary); outline: none; }
        .btn-register { width: 100%; padding: 14px; background: var(--accent); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 15px; transition: 0.2s; margin-top: 5px; }
        .btn-register:active { transform: scale(0.98); }
        .btn-register:disabled { opacity: 0.7; }
        #error-msg { color: #e74c3c; font-size: 13px; margin-top: 12px; display: none; background: #fdf2f2; padding: 10px 12px; border-radius: 8px; border: 1px solid #fadbd8; }
        .login-link { text-align: center; margin-top: 18px; font-size: 13px; color: #95a5a6; }
        .login-link a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }
        .divider { display: flex; align-items: center; gap: 10px; margin: 20px 0; }
        .divider hr { flex: 1; border: none; border-top: 1px solid #eee; }
        .divider span { color: #bdc3c7; font-size: 12px; }
    </style>
</head>
<body>
<div id="register-card">
    <h2 class="card-title">🗓️ Planner</h2>
    <p class="card-subtitle">Hesap oluştur, planlamaya başla!</p>

    <div class="input-group">
        <label>Kullanıcı Adı</label>
        <input type="text" id="username" placeholder="Kullanıcı adınız" autocomplete="username">
    </div>
    <div class="input-group">
        <label>Şifre</label>
        <input type="password" id="password" placeholder="En az 6 karakter" autocomplete="new-password">
    </div>
    <div class="input-group">
        <label>Şifre (Tekrar)</label>
        <input type="password" id="password_confirmation" placeholder="Şifrenizi tekrar girin" autocomplete="new-password">
    </div>

    <button class="btn-register" onclick="handleRegister()" id="registerBtn">Kayıt Ol</button>
    <div id="error-msg"></div>

    <div class="divider"><hr><span>veya</span><hr></div>
    <div class="login-link">Zaten hesabın var mı? <a href="/login">Giriş Yap</a></div>
</div>

<script>
    document.getElementById('password_confirmation').addEventListener('keypress', e => {
        if (e.key === 'Enter') handleRegister();
    });

    function handleRegister() {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        const btn = document.getElementById('registerBtn');
        const errEl = document.getElementById('error-msg');

        errEl.style.display = 'none';

        if (!username || !password || !confirmation) {
            showError('Lütfen tüm alanları doldurun!');
            return;
        }

        if (password !== confirmation) {
            showError('Şifreler eşleşmiyor!');
            return;
        }

        if (password.length < 6) {
            showError('Şifre en az 6 karakter olmalı!');
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Kayıt yapılıyor...';

        fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ username, password, password_confirmation: confirmation })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || '/planner';
                } else {
                    showError(data.message || 'Bir hata oluştu!');
                    btn.disabled = false;
                    btn.innerText = 'Kayıt Ol';
                }
            })
            .catch(() => {
                showError('Sunucu hatası!');
                btn.disabled = false;
                btn.innerText = 'Kayıt Ol';
            });
    }

    function showError(msg) {
        const el = document.getElementById('error-msg');
        el.innerText = '❌ ' + msg;
        el.style.display = 'block';
    }
</script>
</body>
</html>
