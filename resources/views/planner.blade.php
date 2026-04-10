<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Planner</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Planner">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="theme-color" content="#2c3e50">

    <style>
        :root {
            --primary: #4a90e2;
            --bg: #f4f7f6;
            --sidebar: #2c3e50;
            --sidebar-dark: #1a252f;
            --text: #ecf0f1;
            --accent: #27ae60;
            --danger: #e74c3c;
            --purple: #8e44ad;
            --orange: #e67e22;
            --border: #eee;
            --cell-bg: #fafafa;
            --nav-h: 65px;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        body {
            margin: 0;
            font-family: -apple-system, 'SF Pro Display', 'Helvetica Neue', sans-serif;
            background: var(--bg);
            display: flex;
            overflow: hidden;
            height: 100dvh;
            padding-top: env(safe-area-inset-top);
            padding-left: env(safe-area-inset-left);
            padding-right: env(safe-area-inset-right);
        }

        @supports (-webkit-touch-callout: none) {
            body { height: -webkit-fill-available; }
        }

        /* ════════════════════════════
           DESKTOP: SIDEBAR + TAKVİM
        ════════════════════════════ */
        .sidebar {
            width: 320px;
            min-width: 320px;
            background: var(--sidebar);
            color: var(--text);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Üst nav sekmeleri */
        .tab-nav {
            display: flex;
            background: var(--sidebar-dark);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }

        .tab-btn {
            flex: 1;
            padding: 14px 8px;
            background: none;
            border: none;
            color: rgba(255,255,255,0.45);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.3px;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-color 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .tab-btn .tab-icon { font-size: 18px; }
        .tab-btn.active {
            color: white;
            border-bottom-color: var(--primary);
        }

        /* Sekme içerikleri */
        .tab-content { display: none; flex-direction: column; flex: 1; overflow: hidden; }
        .tab-content.active { display: flex; }

        /* ── TAKVİM SEKMESİ (sidebar içi) ── */
        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 16px;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .sidebar-title { font-size: 16px; font-weight: 700; margin: 0; }

        .btn-logout {
            background: var(--danger);
            border: none;
            color: white;
            padding: 7px 11px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-logout:active { opacity: 0.7; }

        .form-section {
            background: rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 14px;
        }

        .form-section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            opacity: 0.45;
            margin: 0 0 10px 0;
        }

        .form-group { margin-bottom: 10px; }
        .form-group:last-child { margin-bottom: 0; }

        .form-group label {
            display: block;
            font-size: 11px;
            margin-bottom: 4px;
            opacity: 0.65;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 9px 11px;
            border-radius: 7px;
            border: 1.5px solid transparent;
            font-size: 14px;
            background: rgba(255,255,255,0.11);
            color: white;
            transition: border-color 0.2s;
            -webkit-appearance: none;
        }

        .form-group input::placeholder { color: rgba(255,255,255,0.3); }
        .form-group input:focus { outline: none; border-color: var(--primary); background: rgba(255,255,255,0.17); }
        .form-group input[type="color"] { height: 40px; padding: 3px 7px; cursor: pointer; }
        .form-group input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 0.6; }

        .date-row { display: flex; gap: 8px; }
        .date-row .form-group { flex: 1; }

        .btn-row { display: flex; gap: 7px; margin-top: 4px; }

        .btn {
            border: none;
            color: white;
            padding: 11px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            transition: transform 0.1s, opacity 0.2s;
            flex: 1;
        }
        .btn:active { transform: scale(0.97); opacity: 0.85; }
        .btn-save { background: var(--accent); }
        .btn-cancel { background: #566573; flex: 0 0 auto; padding: 11px 14px; }
        .btn-emergency { background: var(--purple); width: 100%; margin-top: 8px; }

        /* Görev listesi (sidebar) */
        .task-list-header {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            opacity: 0.45;
            margin: 0 0 8px 0;
        }

        .task-item {
            background: rgba(255,255,255,0.08);
            padding: 9px 11px;
            margin-bottom: 7px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #ccc;
        }

        .task-info { display: flex; flex-direction: column; flex: 1; min-width: 0; margin-right: 7px; }
        .task-title-text { font-weight: 600; font-size: 12px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .task-date-text { font-size: 10px; color: #bdc3c7; }

        .task-actions { display: flex; gap: 4px; flex-shrink: 0; }
        .task-btn {
            border: none; color: white; padding: 5px 8px; border-radius: 5px;
            cursor: pointer; font-size: 12px; width: auto;
        }
        .task-btn:active { opacity: 0.7; }
        .task-btn-edit { background: #2980b9; }
        .task-btn-delete { background: var(--danger); }

        /* ════════════════════════════
           AKLIMDAKİLER SEKMESİ
        ════════════════════════════ */
        .notes-container {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .note-input-area {
            background: rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 14px;
        }

        .note-input-row {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }

        .note-input {
            flex: 1;
            padding: 9px 11px;
            border-radius: 7px;
            border: 1.5px solid transparent;
            font-size: 14px;
            background: rgba(255,255,255,0.11);
            color: white;
            -webkit-appearance: none;
        }
        .note-input::placeholder { color: rgba(255,255,255,0.3); }
        .note-input:focus { outline: none; border-color: var(--primary); background: rgba(255,255,255,0.17); }

        .priority-select {
            padding: 9px 8px;
            border-radius: 7px;
            border: 1.5px solid transparent;
            font-size: 12px;
            background: rgba(255,255,255,0.11);
            color: white;
            cursor: pointer;
            -webkit-appearance: none;
            width: 80px;
            text-align: center;
        }
        .priority-select:focus { outline: none; border-color: var(--primary); }
        .priority-select option { background: var(--sidebar); color: white; }

        .btn-note-add {
            width: 100%;
            padding: 10px;
            background: var(--primary);
            border: none;
            color: white;
            border-radius: 7px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-note-add:active { opacity: 0.8; }

        /* Filtre butonları */
        .note-filters {
            display: flex;
            gap: 5px;
        }

        .filter-btn {
            flex: 1;
            padding: 6px 4px;
            border: 1.5px solid rgba(255,255,255,0.15);
            background: transparent;
            color: rgba(255,255,255,0.5);
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.2s;
        }
        .filter-btn.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-color: rgba(255,255,255,0.35);
        }

        /* Not kartları */
        .note-list { display: flex; flex-direction: column; gap: 7px; }

        .note-card {
            background: rgba(255,255,255,0.08);
            border-radius: 9px;
            padding: 10px 12px;
            display: flex;
            align-items: flex-start;
            gap: 9px;
            border-left: 3px solid #ccc;
            transition: opacity 0.2s;
        }

        .note-card.done { opacity: 0.45; }
        .note-card.done .note-text { text-decoration: line-through; }

        .note-check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3);
            cursor: pointer;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            margin-top: 1px;
            transition: all 0.2s;
        }
        .note-card.done .note-check {
            background: var(--accent);
            border-color: var(--accent);
        }

        .note-body { flex: 1; min-width: 0; }
        .note-text { font-size: 13px; font-weight: 500; line-height: 1.4; word-break: break-word; }
        .note-priority {
            font-size: 10px;
            font-weight: 700;
            margin-top: 3px;
            letter-spacing: 0.5px;
            opacity: 0.7;
        }

        .note-delete {
            background: none;
            border: none;
            color: rgba(255,255,255,0.3);
            cursor: pointer;
            font-size: 15px;
            padding: 2px 4px;
            border-radius: 4px;
            flex-shrink: 0;
            transition: color 0.2s;
        }
        .note-delete:active { color: var(--danger); }

        .notes-empty {
            text-align: center;
            opacity: 0.3;
            font-size: 13px;
            margin-top: 20px;
        }

        /* ════════════════════════════
           TAKVİM ALANI
        ════════════════════════════ */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            background: white;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .calendar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .month-title { font-size: 20px; font-weight: 700; color: #2c3e50; margin: 0; }

        .month-nav { display: flex; gap: 7px; }

        .nav-btn {
            background: var(--bg);
            border: 1px solid var(--border);
            color: #2c3e50;
            width: 34px; height: 34px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .nav-btn:active { background: #dde; }

        .day-names {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 4px;
        }

        .day-name {
            text-align: center;
            font-weight: 600;
            color: #95a5a6;
            font-size: 11px;
            padding: 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            flex-grow: 1;
        }

        .calendar-cell {
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 4px;
            min-height: 90px;
            background: var(--cell-bg);
            overflow: hidden;
        }

        .calendar-cell.empty { border: none; background: transparent; }
        .calendar-cell.today { border-color: var(--primary); background: #f0f6ff; }

        .date-number {
            font-weight: 700;
            text-align: right;
            color: #d5d8dc;
            font-size: 12px;
            margin-bottom: 2px;
            padding-right: 2px;
        }
        .calendar-cell.today .date-number { color: var(--primary); font-size: 13px; }

        .task-tag {
            color: white;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 10px;
            margin-top: 2px;
            font-weight: 600;
            cursor: pointer;
            word-break: break-word;
            line-height: 1.3;
            display: block;
        }
        .task-tag:active { opacity: 0.75; }

        /* ── MODAL ── */
        #emergency-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 999;
            justify-content: center;
            align-items: center;
            padding: 20px;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        .modal-body {
            background: white;
            padding: 25px;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-title { color: var(--danger); margin-top: 0; font-size: 20px; }

        .emergency-item {
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .btn-modal-close {
            margin-top: 15px; width: 100%;
            background: #34495e; color: white;
            border: none; padding: 12px;
            border-radius: 8px; cursor: pointer; font-weight: 600;
        }

        /* ════════════════════════════
           MOBİL
        ════════════════════════════ */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
                overflow: hidden;
                padding-bottom: 0;
            }

            /* Takvim tam üstte */
            .main-content {
                width: 100%;
                flex: 1 1 0;
                min-height: 0;
                order: 1;
                padding: 10px 10px 5px;
                padding-top: calc(10px + env(safe-area-inset-top));
            }

            /* Sidebar → alt panel */
            .sidebar {
                width: 100%;
                min-width: unset;
                flex: 0 0 auto;
                order: 2;
                /* Yükseklik tab'a göre JS ile ayarlanır */
                height: auto;
                max-height: 55%;
                transition: max-height 0.3s ease;
            }

            /* Alt bottom-nav */
            .tab-nav {
                order: 3;
                position: relative;
                padding-bottom: env(safe-area-inset-bottom);
                background: var(--sidebar-dark);
                border-top: 1px solid rgba(255,255,255,0.1);
                border-bottom: none;
            }

            .tab-btn { border-bottom: none; border-top: 2px solid transparent; }
            .tab-btn.active { border-top-color: var(--primary); border-bottom-color: transparent; }

            .calendar-cell { min-height: 50px; }
            .calendar-grid { gap: 3px; }
            .day-names { gap: 3px; }
            .date-number { font-size: 10px; }
            .task-tag { font-size: 9px; padding: 1px 3px; }
            .month-title { font-size: 16px; }
        }

        @media (max-width: 380px) {
            .day-name { font-size: 9px; }
            .calendar-cell { min-height: 42px; }
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<div class="sidebar">

    <!-- Sekme içerikleri -->

    <!-- 1) TAKVİM SEKMESİ -->
    <div class="tab-content active" id="tab-takvim">
        <div class="sidebar-scroll">
            <div class="sidebar-header">
                <h3 id="formTitle" class="sidebar-title">Yeni Görev</h3>
                <button onclick="handleLogout()" class="btn-logout">🚪 Çıkış</button>
            </div>

            <input type="hidden" id="editingTaskId">

            <div class="form-section">
                <p class="form-section-title">Görev Bilgileri</p>
                <div class="form-group">
                    <label>Görev Adı</label>
                    <input type="text" id="taskTitle" placeholder="Görev adı...">
                </div>
                <div class="date-row">
                    <div class="form-group">
                        <label>Başlangıç</label>
                        <input type="date" id="taskStartDate">
                    </div>
                    <div class="form-group">
                        <label>Bitiş</label>
                        <input type="date" id="taskEndDate">
                    </div>
                </div>
                <div class="form-group">
                    <label>Renk</label>
                    <input type="color" id="taskColor" value="#4a90e2">
                </div>
                <div class="btn-row">
                    <button onclick="saveTask()" id="saveBtn" class="btn btn-save">+ Kaydet</button>
                    <button onclick="cancelEdit()" id="cancelBtn" class="btn btn-cancel" style="display:none;">✕</button>
                </div>
                <button onclick="generateEmergencyPlan()" class="btn btn-emergency">🚨 Acil Eylem Planı</button>
            </div>

            <p class="task-list-header">Görev Listesi</p>
            <div id="sidebarTaskList"></div>
        </div>
    </div>

    <!-- 2) AKLIMDAKİLER SEKMESİ -->
    <div class="tab-content" id="tab-notlar">
        <div class="notes-container">

            <!-- Hızlı not ekle -->
            <div class="note-input-area">
                <div class="note-input-row">
                    <input type="text" class="note-input" id="noteInput" placeholder="Aklına gelen bir şey...">
                    <select class="priority-select" id="notePriority">
                        <option value="high">🔴 Yüksek</option>
                        <option value="medium" selected>🟡 Orta</option>
                        <option value="low">🟢 Düşük</option>
                    </select>
                </div>
                <button class="btn-note-add" onclick="addNote()">+ Ekle</button>
            </div>

            <!-- Filtreler -->
            <div class="note-filters">
                <button class="filter-btn active" onclick="filterNotes('all', this)">Tümü</button>
                <button class="filter-btn" onclick="filterNotes('active', this)">Bekliyor</button>
                <button class="filter-btn" onclick="filterNotes('done', this)">Tamamlandı</button>
                <button class="filter-btn" onclick="filterNotes('high', this)">🔴 Yüksek</button>
            </div>

            <!-- Not listesi -->
            <div class="note-list" id="noteList"></div>
        </div>
    </div>

    <!-- Sekme navigasyonu -->
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

<!-- ══ TAKVİM ALANI ══ -->
<div class="main-content">
    <div class="calendar-top">
        <h2 id="monthYearDisplay" class="month-title">Takvim</h2>
        <div class="month-nav">
            <button class="nav-btn" onclick="changeMonth(-1)">‹</button>
            <button class="nav-btn" onclick="changeMonth(1)">›</button>
        </div>
    </div>

    <div class="day-names">
        <div class="day-name">Pzt</div>
        <div class="day-name">Sal</div>
        <div class="day-name">Çar</div>
        <div class="day-name">Per</div>
        <div class="day-name">Cum</div>
        <div class="day-name">Cmt</div>
        <div class="day-name">Paz</div>
    </div>

    <div class="calendar-grid" id="calendarGrid"></div>
</div>

<!-- ACİL MODAL -->
<div id="emergency-modal">
    <div class="modal-body">
        <h2 class="modal-title">🚨 Acil Eylem Planı</h2>
        <div id="emergency-list"></div>
        <button class="btn-modal-close" onclick="document.getElementById('emergency-modal').style.display='none'">Kapat</button>
    </div>
</div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let tasks = [];
    let notes = JSON.parse(localStorage.getItem('plannerNotes') || '[]');
    let currentFilter = 'all';

    // ── BAŞLAT ──
    window.onload = () => {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('taskStartDate').value = today;
        document.getElementById('taskEndDate').value = today;
        document.getElementById('taskTitle').addEventListener('keypress', e => { if (e.key === 'Enter') saveTask(); });
        document.getElementById('noteInput').addEventListener('keypress', e => { if (e.key === 'Enter') addNote(); });
        loadTasks();
        loadNotes();
    };

    // ════════════════════════════
    // SEKME YÖNETİMİ
    // ════════════════════════════
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    // ── AY DEĞİŞTİR ──
    function changeMonth(dir) {
        currentMonth += dir;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
        renderCalendar(currentMonth, currentYear);
    }

    // ════════════════════════════
    // GÖREV API
    // ════════════════════════════
    function loadTasks() {
        fetch('/tasks', { headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(res => res.json())
            .then(data => {
                tasks = data.map(t => ({
                    id: t.id, title: t.title,
                    startDate: t.start_date, endDate: t.end_date, color: t.color
                }));
                renderCalendar(currentMonth, currentYear);
                renderSidebarTasks();
            })
            .catch(() => { renderCalendar(currentMonth, currentYear); renderSidebarTasks(); });
    }

    function saveTask() {
        const id = document.getElementById('editingTaskId').value;
        const title = document.getElementById('taskTitle').value.trim();
        const startDate = document.getElementById('taskStartDate').value;
        const endDate = document.getElementById('taskEndDate').value;
        const color = document.getElementById('taskColor').value;

        if (!title || !startDate || !endDate) { alert('Lütfen tüm alanları doldurun!'); return; }
        if (new Date(startDate) > new Date(endDate)) { alert('Bitiş tarihi başlangıçtan önce olamaz!'); return; }

        const btn = document.getElementById('saveBtn');
        btn.disabled = true; btn.innerText = '...';

        fetch(id ? `/tasks/${id}` : '/tasks', {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ title, startDate, endDate, color })
        })
            .then(() => { cancelEdit(); loadTasks(); })
            .catch(() => alert('Bağlantı hatası!'))
            .finally(() => { btn.disabled = false; });
    }

    function deleteTask(id) {
        if (!confirm('Görevi silmek istiyor musun?')) return;
        fetch(`/tasks/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(() => loadTasks());
    }

    function editTask(id) {
        const task = tasks.find(t => t.id == id);
        if (!task) return;
        document.getElementById('editingTaskId').value = task.id;
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskStartDate').value = task.startDate;
        document.getElementById('taskEndDate').value = task.endDate;
        document.getElementById('taskColor').value = task.color;
        document.getElementById('formTitle').innerText = '✏️ Görevi Düzenle';
        document.getElementById('saveBtn').innerText = '💾 Güncelle';
        document.getElementById('cancelBtn').style.display = 'block';
        document.querySelector('.sidebar-scroll').scrollTo({ top: 0, behavior: 'smooth' });
        // Mobilde takvim sekmesine geç
        switchTab('takvim', document.querySelector('.tab-btn'));
    }

    function cancelEdit() {
        document.getElementById('editingTaskId').value = '';
        document.getElementById('taskTitle').value = '';
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('taskStartDate').value = today;
        document.getElementById('taskEndDate').value = today;
        document.getElementById('taskColor').value = '#4a90e2';
        document.getElementById('formTitle').innerText = 'Yeni Görev';
        document.getElementById('saveBtn').innerText = '+ Kaydet';
        document.getElementById('cancelBtn').style.display = 'none';
    }

    // ════════════════════════════
    // TAKVİM RENDER
    // ════════════════════════════
    function renderCalendar(month, year) {
        const grid = document.getElementById('calendarGrid');
        const months = ["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"];
        document.getElementById('monthYearDisplay').innerText = `${months[month]} ${year}`;
        grid.innerHTML = '';

        let firstDay = new Date(year, month, 1).getDay();
        firstDay = firstDay === 0 ? 6 : firstDay - 1;
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.classList.add('calendar-cell', 'empty');
            grid.appendChild(el);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement('div');
            cell.classList.add('calendar-cell');
            const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const cellDate = new Date(dateStr);

            if (day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear())
                cell.classList.add('today');

            const num = document.createElement('div');
            num.classList.add('date-number');
            num.innerText = day;
            cell.appendChild(num);

            tasks.filter(t => {
                const s = new Date(t.startDate), e = new Date(t.endDate);
                return cellDate >= s && cellDate <= e;
            }).forEach(task => {
                const tag = document.createElement('div');
                tag.classList.add('task-tag');
                tag.innerText = task.title;
                tag.style.backgroundColor = task.color;
                tag.onclick = () => editTask(task.id);
                cell.appendChild(tag);
            });

            grid.appendChild(cell);
        }
    }

    function renderSidebarTasks() {
        const container = document.getElementById('sidebarTaskList');
        container.innerHTML = '';

        if (tasks.length === 0) {
            container.innerHTML = '<p style="opacity:0.35; font-size:12px; text-align:center; margin-top:10px;">Henüz görev yok</p>';
            return;
        }

        [...tasks].sort((a,b) => new Date(a.startDate) - new Date(b.startDate)).forEach(task => {
            const item = document.createElement('div');
            item.classList.add('task-item');
            item.style.borderLeftColor = task.color;
            const opts = { day:'numeric', month:'short' };
            const s = new Date(task.startDate).toLocaleDateString('tr-TR', opts);
            const e = new Date(task.endDate).toLocaleDateString('tr-TR', opts);
            item.innerHTML = `
                <div class="task-info">
                    <span class="task-title-text">${task.title}</span>
                    <span class="task-date-text">📅 ${s === e ? s : s+' – '+e}</span>
                </div>
                <div class="task-actions">
                    <button onclick="editTask(${task.id})" class="task-btn task-btn-edit">✏️</button>
                    <button onclick="deleteTask(${task.id})" class="task-btn task-btn-delete">🗑️</button>
                </div>`;
            container.appendChild(item);
        });
    }

    // ════════════════════════════
    // AKLIMDAKİLER (localStorage)
    // ════════════════════════════
    const PRIORITY_COLORS = { high: '#e74c3c', medium: '#e67e22', low: '#27ae60' };
    const PRIORITY_LABELS = { high: '🔴 Yüksek', medium: '🟡 Orta', low: '🟢 Düşük' };
    let notes = [];

    function loadNotes() {
        fetch('/notes', { headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(res => res.json())
            .then(data => { notes = data; renderNotes(); })
            .catch(() => renderNotes());
    }

    function addNote() {
        const input = document.getElementById('noteInput');
        const text = input.value.trim();
        if (!text) { input.focus(); return; }
        const priority = document.getElementById('notePriority').value;

        fetch('/notes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ text, priority })
        })
            .then(res => res.json())
            .then(note => { notes.unshift(note); renderNotes(); input.value = ''; input.focus(); });
    }

    function toggleNote(id) {
        fetch(`/notes/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF }
        })
            .then(res => res.json())
            .then(updated => {
                const i = notes.findIndex(n => n.id == id);
                if (i !== -1) notes[i] = updated;
                renderNotes();
            });
    }

    function deleteNote(id) {
        fetch(`/notes/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF }
        }).then(() => { notes = notes.filter(n => n.id != id); renderNotes(); });
    }

    function filterNotes(filter, btn) {
        currentFilter = filter;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderNotes();
    }

    function renderNotes() {
        const list = document.getElementById('noteList');
        let filtered = notes;

        if (currentFilter === 'active') filtered = notes.filter(n => !n.done);
        else if (currentFilter === 'done') filtered = notes.filter(n => n.done);
        else if (currentFilter === 'high') filtered = notes.filter(n => n.priority === 'high');

        if (filtered.length === 0) {
            list.innerHTML = '<p class="notes-empty">Burada henüz bir şey yok 🌱</p>';
            return;
        }

        list.innerHTML = filtered.map(note => `
        <div class="note-card ${note.done ? 'done' : ''}" style="border-left-color:${PRIORITY_COLORS[note.priority]}">
            <div class="note-check" onclick="toggleNote(${note.id})">${note.done ? '✓' : ''}</div>
            <div class="note-body">
                <div class="note-text">${escapeHtml(note.text)}</div>
                <div class="note-priority" style="color:${PRIORITY_COLORS[note.priority]}">${PRIORITY_LABELS[note.priority]}</div>
            </div>
            <button class="note-delete" onclick="deleteNote(${note.id})">✕</button>
        </div>
    `).join('');
    }

    // ════════════════════════════
    // ACİL EYLEM PLANI
    // ════════════════════════════
    function generateEmergencyPlan() {
        const modal = document.getElementById('emergency-modal');
        const list = document.getElementById('emergency-list');
        modal.style.display = 'flex';

        const today = new Date();
        const upcoming = tasks
            .filter(t => new Date(t.endDate) >= today)
            .sort((a,b) => new Date(a.endDate) - new Date(b.endDate))
            .slice(0, 5);

        if (upcoming.length === 0) { list.innerHTML = '<p style="color:#7f8c8d;">Yaklaşan görev yok.</p>'; return; }

        list.innerHTML = upcoming.map((t, i) => {
            const daysLeft = Math.ceil((new Date(t.endDate) - today) / 86400000);
            const color = daysLeft <= 2 ? '#e74c3c' : daysLeft <= 7 ? '#e67e22' : '#27ae60';
            return `<div class="emergency-item" style="border-left:4px solid ${t.color};">
                <strong>${i+1}. ${t.title}</strong><br>
                <small style="color:#7f8c8d;">📅 Bitiş: ${t.endDate}</small><br>
                <span style="color:${color}; font-weight:700; font-size:13px;">⏳ ${daysLeft} gün kaldı</span>
            </div>`;
        }).join('');
    }

    // ── ÇIKIŞ ──
    function handleLogout() {
        fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(res => res.json())
            .then(data => window.location.href = data.redirect || '/login');
    }

    // ── PWA SERVICE WORKER ──
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
