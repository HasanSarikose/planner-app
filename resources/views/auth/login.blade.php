<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Planner Girişi</title>
    <style>
        :root { --primary: #4a90e2; --bg: #f4f7f6; --dark: #2c3e50; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: var(--bg); height: 100vh; display: flex; align-items: center; justify-content: center; }
        #login-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 350px; margin: 20px; text-align: center; }
        h2 { color: var(--dark); margin-bottom: 25px; font-size: 24px; }
        .input-group { text-align: left; margin-bottom: 20px; }
        label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; color: var(--dark); }
        input { width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box; font-size: 16px; transition: 0.3s; }
        input:focus { border-color: var(--primary); outline: none; }
        button { width: 100%; padding: 14px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; }
        button:active { transform: scale(0.98); }
        #error-msg { color: #e74c3c; font-size: 14px; margin-top: 15px; display: none; background: #fdf2f2; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
<div id="login-card">
    <h2>🗓️ Planner Girişi</h2>
    <div class="input-group">
        <label>Kullanıcı Adı</label>
        <input type="text" id="username" placeholder="Kullanıcı adınız">
    </div>
    <div class="input-group">
        <label>Şifre</label>
        <input type="password" id="password" placeholder="••••••••">
    </div>
    <button onclick="handleLogin()">Giriş Yap</button>
    <div id="error-msg">❌ Bilgiler hatalı, tekrar dene.</div>
</div>

<script>
    function handleLogin() {
        const btn = document.querySelector('button');
        btn.innerText = "Giriş Yapılıyor...";

        fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                username: document.getElementById('username').value.trim(),
                password: document.getElementById('password').value.trim()
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || '/planner'; // ← /planner'a yönlendir
                } else {
                    document.getElementById('error-msg').style.display = 'block';
                    btn.innerText = "Giriş Yap";
                }
            })
            .catch(() => {
                alert("Sunucu hatası!");
                btn.innerText = "Giriş Yap";
            });
    }
</script>
</body>
</html>
