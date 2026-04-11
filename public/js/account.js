function loadAccountInfo() {
    fetch('/account/info', { headers: { 'X-CSRF-TOKEN': window.CSRF } })
        .then(res => res.json())
        .then(data => {
            document.getElementById('accountName').innerText = data.name;
            document.getElementById('accountAvatar').innerText = data.name.charAt(0).toUpperCase();
            document.getElementById('accountSince').innerText = data.created_at;
            document.getElementById('statTasks').innerText = data.task_count;
            document.getElementById('statNotes').innerText = data.note_count;
            document.getElementById('statDone').innerText = data.done_count;
            const pct = data.note_count > 0
                ? Math.round((data.done_count / data.note_count) * 100)
                : 0;
            document.getElementById('statProgress').innerText = '%' + pct;
        })
        .catch(() => console.error('Hesap bilgileri yüklenemedi'));
}

function changePassword() {
    const current  = document.getElementById('currentPassword').value;
    const newPw    = document.getElementById('newPassword').value;
    const confirm  = document.getElementById('newPasswordConfirm').value;
    const msgEl    = document.getElementById('pwMessage');

    if (!current || !newPw || !confirm) {
        showPwMessage('Lütfen tüm alanları doldurun!', 'error');
        return;
    }

    if (newPw !== confirm) {
        showPwMessage('Yeni şifreler eşleşmiyor!', 'error');
        return;
    }

    if (newPw.length < 6) {
        showPwMessage('Şifre en az 6 karakter olmalı!', 'error');
        return;
    }

    fetch('/account/password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF },
        body: JSON.stringify({
            current_password:      current,
            new_password:          newPw,
            new_password_confirmation: confirm
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showPwMessage(data.message, 'success');
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('newPasswordConfirm').value = '';
            } else {
                showPwMessage(data.message || 'Bir hata oluştu!', 'error');
            }
        })
        .catch(() => showPwMessage('Bağlantı hatası!', 'error'));
}

function showPwMessage(msg, type) {
    const el = document.getElementById('pwMessage');
    el.style.display = 'block';
    el.innerText = msg;
    el.style.background = type === 'success' ? '#eafaf1' : '#fdf2f2';
    el.style.color       = type === 'success' ? '#27ae60' : '#e74c3c';
    el.style.border      = `1px solid ${type === 'success' ? '#27ae60' : '#e74c3c'}`;
    setTimeout(() => { el.style.display = 'none'; }, 3000);
}
