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
    <link rel="stylesheet" href="/css/notes-area.css">
    <link rel="stylesheet" href="/css/account-area.css">
</head>
<body>

{{-- ── SIDEBAR ── --}}
<div class="sidebar">

    {{-- Sekme içerikleri --}}
    @include('planner.partials.sidebar')
    @include('planner.partials.notes')

    {{-- Alt sekme navigasyonu --}}
    <div class="tab-nav">
        <button class="tab-btn active" id="nav-takvim" onclick="switchTab('takvim', this)">
            <span class="tab-icon">🗓️</span>
            <span>Takvim</span>
        </button>
        <button class="tab-btn" id="nav-notlar" onclick="switchTab('notlar', this)">
            <span class="tab-icon">💡</span>
            <span>Aklımdakiler</span>
        </button>
    </div>

    {{-- Hesap butonu --}}
    <button onclick="switchMain('account')" class="account-strip">
        👤 Hesabım
    </button>

    {{-- Çıkış şeridi --}}
    <button onclick="handleLogout()" class="logout-strip">
        🚪 Çıkış Yap
    </button>

</div>

{{-- ── ANA ALANLAR ── --}}
@include('planner.partials.calendar')
@include('planner.partials.notes-area')
@include('planner.partials.account-area')

{{-- ── MODALLER ── --}}
@include('planner.modals.emergency')

<script>
    window.CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Aktif ana alan
    let activeMain = 'calendar';

    // Sidebar sekme yönetimi
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');

        // Takvim sekmesine geçince takvimi göster
        if (name === 'takvim') {
            switchMain('calendar');
        } else if (name === 'notlar') {
            switchMain('notes');
        }
    }

    // Ana alan geçişi
    function switchMain(area) {
        activeMain = area;
        document.querySelector('.main-content').style.display     = area === 'calendar' ? 'flex' : 'none';
        document.getElementById('notes-area').style.display       = area === 'notes'    ? 'flex' : 'none';
        document.getElementById('account-area').style.display     = area === 'account'  ? 'flex' : 'none';

        if (area === 'notes') renderNotesArea();
        if (area === 'account') loadAccountInfo();
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
<script src="/js/account.js"></script>

<script>
    window.onload = () => {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('taskStartDate').value = today;
        document.getElementById('taskEndDate').value = today;

        document.getElementById('taskTitle')
            .addEventListener('keypress', e => { if (e.key === 'Enter') saveTask(); });
        document.getElementById('noteInput')
            .addEventListener('keypress', e => { if (e.key === 'Enter') addNote(); });

        document.getElementById('note-edit-modal')
            .addEventListener('click', function(e) {
                if (e.target === this) closeNoteEditModal();
            });

        loadTasks();
        loadNotes();
    };

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
