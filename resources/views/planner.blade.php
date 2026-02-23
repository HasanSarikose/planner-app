<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kişisel Planlayıcı</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2c3e50">
    <link rel="apple-touch-icon" href="{{ asset('icon-192.png') }}">

    <style>
        :root { --primary: #4a90e2; --bg-color: #f4f7f6; --sidebar-bg: #2c3e50; --text-light: #ecf0f1; --text-dark: #333; }
        body { margin: 0; font-family: sans-serif; background: var(--bg-color); height: 100vh; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        #login-screen { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 300px; max-width: 90vw; }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; font-weight: bold; font-size: 14px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; }

        #app-screen { display: none; width: 100vw; height: 100vh; display: flex; }
        .sidebar { width: 340px; background: var(--sidebar-bg); color: var(--text-light); padding: 20px; display: flex; flex-direction: column; overflow-y: auto; }
        .form-group { margin-bottom: 12px; } .form-group input { width: 100%; padding: 8px; border-radius: 4px; border: none; box-sizing: border-box; }
        .date-row { display: flex; gap: 10px; } .date-row .form-group { flex: 1; }

        .task-list { margin-top: 20px; flex-grow: 1; }
        .task-item { background: rgba(255,255,255,0.1); padding: 10px; margin-bottom: 10px; border-radius: 6px; display: flex; flex-direction: column; border-left: 5px solid #ccc; }
        .task-info { display: flex; flex-direction: column; margin-bottom: 8px; }
        .task-title { font-weight: bold; font-size: 14px; }
        .task-date { font-size: 11px; color: #bdc3c7; }
        .task-actions { display: flex; gap: 5px; }
        .btn-small { padding: 5px 10px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; }
        .btn-edit { background: #f39c12; } .btn-delete { background: #e74c3c; }

        .calendar-container { flex-grow: 1; padding: 20px; display: flex; flex-direction: column; background: white; overflow-y: auto; }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; flex-grow: 1; min-height: 500px; }
        .day-name { text-align: center; font-weight: bold; color: #7f8c8d; padding: 10px 0; border-bottom: 2px solid #eee; }
        .calendar-cell { border: 1px solid #eee; border-radius: 8px; padding: 5px; min-height: 100px; display: flex; flex-direction: column; background: #fafafa; }
        .date-number { font-weight: bold; color: #bdc3c7; margin-bottom: 5px; text-align: right; }
        .calendar-cell.today .date-number { color: var(--primary); font-size: 18px; }
        .task-tag { color: white; padding: 4px 6px; border-radius: 4px; font-size: 11px; margin-bottom: 3px; word-wrap: break-word; font-weight: 500;}

        /* Acil Eylem Planı Modalı */
        #emergency-modal { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.8); z-index: 9999; justify-content: center; align-items: center; }
        .modal-content { background: white; color: #333; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%; box-shadow: 0 15px 30px rgba(0,0,0,0.3); }
        .modal-content h2 { color: #e74c3c; margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .modal-content ul { padding-left: 20px; line-height: 1.8; font-size: 15px; }

        @media (max-width: 768px) { #app-screen { flex-direction: column; } .sidebar { width: 100%; height: 50vh; } .calendar-container { height: 50vh; } }
    </style>
</head>
<body>

<div id="login-screen">
    <h2>🗓️ Planner Girişi</h2>
    <div class="input-group"><label>Kullanıcı Adı</label><input type="text" id="username"></div>
    <div class="input-group"><label>Şifre</label><input type="password" id="password"></div>
    <button onclick="checkLogin()">Giriş Yap</button>
    <div id="login-error" style="color:red; display:none; margin-top:10px;">Hatalı giriş!</div>
</div>

<div id="app-screen">
    <div class="sidebar">
        <button onclick="logout()" style="background:#e74c3c; margin-bottom: 15px; font-size: 14px;">🚪 Çıkış Yap</button>

        <h2 id="formTitle">Yeni Görev</h2>
        <input type="hidden" id="editingTaskId" value="">
        <div class="form-group"><label>Görev Adı</label><input type="text" id="taskTitle"></div>
        <div class="date-row">
            <div class="form-group"><label>Başlangıç</label><input type="date" id="taskStartDate"></div>
            <div class="form-group"><label>Bitiş</label><input type="date" id="taskEndDate"></div>
        </div>
        <div class="form-group"><label>Renk</label><input type="color" id="taskColor" value="#4a90e2"></div>

        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <button id="saveBtn" onclick="saveTask()" style="background:#27ae60;">+ Kaydet</button>
            <button id="cancelEditBtn" onclick="cancelEdit()" style="background:#95a5a6; display:none;">İptal</button>
        </div>

        <button onclick="generateEmergencyPlan()" style="background:#8e44ad; margin-top:20px; font-size:15px;">🚨 Acil Eylem Planı</button>

        <div class="task-list"><h3 style="border-bottom: 1px solid #7f8c8d;">Görev Listesi</h3><div id="sidebarTaskList"></div></div>
    </div>

    <div class="calendar-container">
        <div class="calendar-header"><h1 id="monthYearDisplay">Aylık Takvim</h1></div>
        <div class="calendar-grid" id="calendarGrid"></div>
    </div>
</div>

<div id="emergency-modal">
    <div class="modal-content">
        <h2>🚨 Stratejik Eylem Planı</h2>
        <div id="emergency-list"></div>
        <button onclick="document.getElementById('emergency-modal').style.display='none'" style="background:#34495e; margin-top:15px;">Anladım, Kapat</button>
    </div>
</div>

<script>
    let isLoggedIn = @json(Auth::check());
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let tasks = [];
    const currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    window.onload = function() {
        if (isLoggedIn) {
            document.getElementById('login-screen').style.display = 'none';
            document.getElementById('app-screen').style.display = 'flex';
            initCalendar();
        } else {
            document.getElementById('login-screen').style.display = 'block';
        }
    };

    function checkLogin() {
        fetch('/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ username: document.getElementById('username').value.trim(), password: document.getElementById('password').value.trim() })
        }).then(res => res.json()).then(data => { if (data.success) window.location.reload(); else document.getElementById('login-error').style.display = 'block'; });
    }

    function logout() {
        fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } }).then(() => window.location.reload());
    }

    function initCalendar() {
        fetch('/tasks').then(res => res.json()).then(data => {
            tasks = data.map(t => ({ id: t.id, title: t.title, startDate: t.start_date, endDate: t.end_date, color: t.color }));
            renderCalendar(currentMonth, currentYear);
            renderSidebarTasks();
        });
        document.getElementById('taskStartDate').valueAsDate = new Date();
        document.getElementById('taskEndDate').valueAsDate = new Date();
    }

    // TAKVİM ÇİZİMİ
    function renderCalendar(month, year) {
        const calendarGrid = document.getElementById('calendarGrid');
        calendarGrid.innerHTML = `<div class="day-name">Pzt</div><div class="day-name">Sal</div><div class="day-name">Çar</div><div class="day-name">Per</div><div class="day-name">Cum</div><div class="day-name">Cmt</div><div class="day-name">Paz</div>`;

        const months = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
        document.getElementById('monthYearDisplay').innerText = `${months[month]} ${year}`;

        let firstDay = new Date(year, month, 1).getDay(); firstDay = firstDay === 0 ? 6 : firstDay - 1;
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) calendarGrid.innerHTML += `<div class="calendar-cell empty"></div>`;

        for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement('div'); cell.classList.add('calendar-cell');
            if (day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) cell.classList.add('today');
            cell.innerHTML = `<div class="date-number">${day}</div>`;

            const cellDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const currentCellDate = new Date(cellDateStr);

            tasks.filter(t => currentCellDate >= new Date(t.startDate) && currentCellDate <= new Date(t.endDate)).forEach(task => {
                const tag = document.createElement('div'); tag.classList.add('task-tag');
                tag.innerText = task.title; tag.style.backgroundColor = task.color;
                cell.appendChild(tag);
            });
            calendarGrid.appendChild(cell);
        }
    }

    // GÖREV KAYDET VEYA GÜNCELLE
    function saveTask() {
        const id = document.getElementById('editingTaskId').value;
        const title = document.getElementById('taskTitle').value;
        const startDate = document.getElementById('taskStartDate').value;
        const endDate = document.getElementById('taskEndDate').value;
        const color = document.getElementById('taskColor').value;

        if (!title || !startDate || !endDate) return alert('Doldurun!');
        if (new Date(startDate) > new Date(endDate)) return alert('Hata: Bitiş, başlangıçtan önce olamaz!');

        const url = id ? `/tasks/${id}` : '/tasks';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ title, startDate, endDate, color })
        }).then(() => {
            cancelEdit(); // Formu sıfırla
            initCalendar(); // Listeyi yenile
        });
    }

    // DÜZENLEME MODUNA GEÇİŞ
    function editTask(id) {
        const task = tasks.find(t => t.id === id);
        if(!task) return;

        document.getElementById('formTitle').innerText = "Görevi Düzenle";
        document.getElementById('editingTaskId').value = task.id;
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskStartDate').value = task.startDate;
        document.getElementById('taskEndDate').value = task.endDate;
        document.getElementById('taskColor').value = task.color;

        document.getElementById('saveBtn').innerText = "Güncelle";
        document.getElementById('cancelEditBtn').style.display = "block";
    }

    // DÜZENLEMEYİ İPTAL ET
    function cancelEdit() {
        document.getElementById('formTitle').innerText = "Yeni Görev";
        document.getElementById('editingTaskId').value = "";
        document.getElementById('taskTitle').value = "";
        document.getElementById('taskStartDate').valueAsDate = new Date();
        document.getElementById('taskEndDate').valueAsDate = new Date();
        document.getElementById('taskColor').value = "#4a90e2";

        document.getElementById('saveBtn').innerText = "+ Kaydet";
        document.getElementById('cancelEditBtn').style.display = "none";
    }

    // GÖREV SİL
    function deleteTask(id) {
        if(!confirm("Görevi silmek istediğinize emin misiniz?")) return;
        fetch('/tasks/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken } }).then(() => initCalendar());
    }

    // SAĞ MENÜYE GÖREVLERİ ÇİZ
    function renderSidebarTasks() {
        const list = document.getElementById('sidebarTaskList'); list.innerHTML = '';
        [...tasks].sort((a, b) => new Date(a.startDate) - new Date(b.startDate)).forEach(task => {
            const formatOpts = { day: 'numeric', month: 'short' };
            const sDate = new Date(task.startDate).toLocaleDateString('tr-TR', formatOpts);
            const eDate = new Date(task.endDate).toLocaleDateString('tr-TR', formatOpts);
            const dateDisplay = (sDate === eDate) ? sDate : `${sDate} - ${eDate}`;

            list.innerHTML += `
                <div class="task-item" style="border-left-color: ${task.color}">
                    <div class="task-info">
                        <span class="task-title">${task.title}</span>
                        <span class="task-date">📅 ${dateDisplay}</span>
                    </div>
                    <div class="task-actions">
                        <button class="btn-small btn-edit" onclick="editTask(${task.id})">✎</button>
                        <button class="btn-small btn-delete" onclick="deleteTask(${task.id})">✖</button>
                    </div>
                </div>`;
        });
    }

    // 🚨 ACİL EYLEM PLANI ALGORİTMASI 🚨
    function generateEmergencyPlan() {
        const today = new Date();
        today.setHours(0,0,0,0);

        // Sadece bitiş tarihi geçmiş olmayan (bugün ve sonrası) görevleri al
        let upcomingTasks = tasks.filter(t => new Date(t.endDate) >= today);

        // Bitiş tarihine göre en yakından uzağa doğru sırala
        upcomingTasks.sort((a, b) => new Date(a.endDate) - new Date(b.endDate));

        const contentDiv = document.getElementById('emergency-list');

        if(upcomingTasks.length === 0) {
            contentDiv.innerHTML = "<p style='color:#27ae60; font-weight:bold;'>Harika! Yaklaşan acil bir görevin yok. Kahveni yudumlayabilirsin. ☕</p>";
        } else {
            let html = "<ul>";
            upcomingTasks.forEach((t, index) => {
                let eDate = new Date(t.endDate);
                let diffTime = Math.abs(eDate - today);
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                let urgency = "";
                if (diffDays === 0) urgency = "<span style='color:#c0392b; font-weight:bold;'>🔥 BUGÜN BİTİYOR!</span>";
                else if (diffDays <= 2) urgency = `<span style='color:#e67e22; font-weight:bold;'>⏳ Son ${diffDays} gün</span>`;
                else urgency = `<span style='color:#7f8c8d;'>${diffDays} gün var</span>`;

                html += `<li><b>${index + 1}. Sıra:</b> ${t.title} <br><small>${urgency}</small></li>`;
            });
            html += "</ul><p style='font-size:12px; color:#7f8c8d; border-top:1px solid #eee; padding-top:10px;'>Yapay Zeka olmadan, tamamen bitiş tarihi algoritmamıza göre önceliklendirildi. İlk sıradakine hemen başla!</p>";
            contentDiv.innerHTML = html;
        }

        document.getElementById('emergency-modal').style.display = 'flex';
    }
</script>

</body>
</html>
