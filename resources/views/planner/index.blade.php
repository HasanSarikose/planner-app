<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Planner</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Planner">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="theme-color" content="#2c3e50">

    <link rel="stylesheet" href="/css/planner.css">
</head>
<body>

{{-- ── SIDEBAR ── --}}
<div class="sidebar">

    {{-- Sekme içerikleri --}}
    @include('planner.partials.sidebar')
    @include('planner.partials.notes')

    {{-- Alt sekme navigasyonu --}}
    <div class="tab-nav">
        <button class="tab-btn active" onclick="switchTab('takvim', this)">
            <span class="tab-icon">🗓️</span>
            <span>Takvim</span>
        </button>
        <button class="tab-btn" onclick="switchTab('notlar', this)">
            <span class="tab-icon">💡</span>
            <span>Aklımdakiler</span>
        </button>
    </div>

</div>

{{-- ── TAKVİM ALANI ── --}}
@include('planner.partials.calendar')

{{-- ── MODALLER ── --}}
@include('planner.modals.emergency')

{{-- JS dosyaları --}}
<script>
    // Global CSRF token — tüm JS dosyaları kullanır
    window.CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Sekme yönetimi
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    // Çıkış
    function handleLogout() {
        fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF } })
            .then(res => res.json())
            .then(data => window.location.href = data.redirect || '/login');
    }
</script>

<script src="/js/calendar.js"></script>
<script src="/js/tasks.js"></script>
<script src="/js/notes.js"></script>

<script>
    // Başlat
    window.onload = () => {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('taskStartDate').value = today;
        document.getElementById('taskEndDate').value = today;

        document.getElementById('taskTitle')
            .addEventListener('keypress', e => { if (e.key === 'Enter') saveTask(); });
        document.getElementById('noteInput')
            .addEventListener('keypress', e => { if (e.key === 'Enter') addNote(); });

        loadTasks();
        loadNotes();
    };

    // PWA Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('✅ SW aktif', reg.scope))
                .catch(err => console.error('❌ SW hata', err));
        });
    }
</script>

</body>
</html>
