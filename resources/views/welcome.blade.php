<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kişisel Planlayıcı</title>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#2c3e50">
    <link rel="apple-touch-icon" href="icon-192.png">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(reg => console.log('✅ PWA Service Worker aktif!', reg))
                    .catch(err => console.error('❌ PWA hatası!', err));
            });
        }
    </script>

    <style>
        /* GENEL AYARLAR */
        :root {
            --primary: #4a90e2;
            --bg-color: #f4f7f6;
            --sidebar-bg: #2c3e50;
            --text-light: #ecf0f1;
            --text-dark: #333;
        }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--bg-color); height: 100vh; overflow: hidden; display: flex; align-items: center; justify-content: center; touch-action: manipulation; }

        /* LOGIN EKRANI */
        #login-screen {
            background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center; width: 300px; max-width: 90vw;
        }
        #login-screen h2 { margin-top: 0; color: var(--text-dark); }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; }
        button:hover { background: #357abd; }
        #login-error { color: #e74c3c; font-size: 14px; margin-top: 10px; display: none; }

        /* ANA UYGULAMA (APP) EKRANI */
        #app-screen {
            display: none; width: 100vw; height: 100vh; display: flex;
        }

        /* SOL MENÜ (SIDEBAR) */
        .sidebar { width: 320px; background: var(--sidebar-bg); color: var(--text-light); padding: 20px; display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar h2 { margin-top: 0; border-bottom: 2px solid #34495e; padding-bottom: 10px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 13px; color: #bdc3c7;}
        .form-group input { width: 100%; padding: 8px; border-radius: 4px; border: none; box-sizing: border-box; }
        .form-group input[type="color"] { height: 40px; padding: 2px; cursor: pointer; }

        .date-row { display: flex; gap: 10px; } /* Tarihleri yan yana koymak için */
        .date-row .form-group { flex: 1; }

        .btn-add { background: #27ae60; margin-top: 10px; }
        .btn-add:hover { background: #2ecc71; }

        /* GÖREV LİSTESİ */
        .task-list { margin-top: 20px; flex-grow: 1; }
        .task-item { background: rgba(255,255,255,0.1); padding: 10px; margin-bottom: 10px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; border-left: 5px solid #ccc; }
        .task-info { display: flex; flex-direction: column; }
        .task-title { font-weight: bold; font-size: 14px; margin-bottom: 3px; }
        .task-date { font-size: 11px; color: #bdc3c7; }
        .delete-btn { background: #e74c3c; padding: 5px 10px; font-size: 12px; width: auto; }

        /* TAKVİM ALANI */
        .calendar-container { flex-grow: 1; padding: 20px; display: flex; flex-direction: column; background: white; overflow-y: auto; }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .calendar-header h1 { margin: 0; color: var(--text-dark); }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; flex-grow: 1; min-height: 500px; }
        .day-name { text-align: center; font-weight: bold; color: #7f8c8d; padding: 10px 0; border-bottom: 2px solid #eee; }
        .calendar-cell { border: 1px solid #eee; border-radius: 8px; padding: 5px; min-height: 100px; display: flex; flex-direction: column; overflow-y: auto; background: #fafafa; }
        .calendar-cell.empty { background: transparent; border: none; }
        .date-number { font-weight: bold; color: #bdc3c7; margin-bottom: 5px; text-align: right; padding-right: 5px;}
        .calendar-cell.today .date-number { color: var(--primary); font-size: 18px; }

        /* TAKVİM İÇİ GÖREV ETİKETİ */
        .task-tag { color: white; padding: 4px 6px; border-radius: 4px; font-size: 11px; margin-bottom: 3px; word-wrap: break-word; box-shadow: 0 1px 3px rgba(0,0,0,0.2); font-weight: 500;}

        /* MOBİL UYUM */
        @media (max-width: 768px) {
            #app-screen { flex-direction: column; }
            .sidebar { width: 100%; height: 45vh; border-bottom: 2px solid #34495e; padding: 15px; box-sizing: border-box; }
            .calendar-container { height: 55vh; padding: 10px; }
            .calendar-grid { gap: 5px; }
            .calendar-cell { padding: 3px; min-height: 70px; }
            .day-name { font-size: 12px; padding: 5px 0; }
            .date-number { font-size: 12px; margin-bottom: 3px; }
            .task-tag { font-size: 9px; padding: 2px; }
        }
    </style>
</head>
<body>

<div id="login-screen">
    <h2>🗓️ Planner Girişi</h2>
    <div class="input-group">
        <label>Kullanıcı Adı</label>
        <input type="text" id="username" placeholder="Kullanıcı adını girin">
    </div>
    <div class="input-group">
        <label>Şifre</label>
        <input type="password" id="password" placeholder="Şifrenizi girin">
    </div>
    <button onclick="checkLogin()">Giriş Yap</button>
    <div id="login-error">Hatalı kullanıcı adı veya şifre!</div>
</div>

<div id="app-screen" style="display: none;">

    <div class="sidebar">
        <h2>Yeni Görev</h2>
        <div class="form-group">
            <label>Görev Adı</label>
            <input type="text" id="taskTitle" placeholder="Örn: Raporu Hazırla">
        </div>

        <div class="date-row">
            <div class="form-group">
                <label>Başlangıç Tarihi</label>
                <input type="date" id="taskStartDate">
            </div>
            <div class="form-group">
                <label>Bitiş Tarihi</label>
                <input type="date" id="taskEndDate">
            </div>
        </div>

        <div class="form-group">
            <label>Renk Seçimi</label>
            <input type="color" id="taskColor" value="#4a90e2">
        </div>
        <button class="btn-add" onclick="addTask()">+ Görev Ekle</button>

        <div class="task-list">
            <h3 style="border-bottom: 1px solid #7f8c8d; padding-bottom:5px; font-size: 16px;">Görev Listesi</h3>
            <div id="sidebarTaskList"></div>
        </div>
    </div>

    <div class="calendar-container">
        <div class="calendar-header">
            <h1 id="monthYearDisplay">Aylık Takvim</h1>
        </div>

        <div class="calendar-grid" id="calendarGrid">
            <div class="day-name">Pzt</div><div class="day-name">Sal</div><div class="day-name">Çar</div>
            <div class="day-name">Per</div><div class="day-name">Cum</div><div class="day-name">Cmt</div><div class="day-name">Paz</div>
        </div>
    </div>

</div>

<script>
    // --- GÜVENLİK ---
    function checkLogin() {
        const user = document.getElementById('username').value.trim();
        const pass = document.getElementById('password').value.trim();

        if (user === 'HSAR' && pass === 'SrksHsn33') {
            document.getElementById('login-screen').style.display = 'none';
            document.getElementById('app-screen').style.display = 'flex';
            initCalendar();
        } else {
            document.getElementById('login-error').style.display = 'block';
        }
    }

    document.getElementById('password').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') checkLogin();
    });

    // --- VERİ YÖNETİMİ ---
    let tasks = JSON.parse(localStorage.getItem('plannerTasks')) || [];

    // Eski verileri yeni "Başlangıç/Bitiş" formatına uydurma yaması (Eğer önceden görev eklediysen)
    tasks = tasks.map(t => {
        if(!t.startDate) {
            t.startDate = t.date;
            t.endDate = t.date;
        }
        return t;
    });

    const currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    function initCalendar() {
        renderCalendar(currentMonth, currentYear);
        renderSidebarTasks();
        // Tarihleri bugüne ayarla
        document.getElementById('taskStartDate').valueAsDate = new Date();
        document.getElementById('taskEndDate').valueAsDate = new Date();
    }

    function renderCalendar(month, year) {
        const calendarGrid = document.getElementById('calendarGrid');
        const monthYearDisplay = document.getElementById('monthYearDisplay');

        const dayNames = `<div class="day-name">Pzt</div><div class="day-name">Sal</div><div class="day-name">Çar</div><div class="day-name">Per</div><div class="day-name">Cum</div><div class="day-name">Cmt</div><div class="day-name">Paz</div>`;
        calendarGrid.innerHTML = dayNames;

        const months = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
        monthYearDisplay.innerText = `${months[month]} ${year}`;

        let firstDay = new Date(year, month, 1).getDay();
        firstDay = firstDay === 0 ? 6 : firstDay - 1;

        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.classList.add('calendar-cell', 'empty');
            calendarGrid.appendChild(emptyCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement('div');
            cell.classList.add('calendar-cell');

            if (day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
                cell.classList.add('today');
            }

            const dateNum = document.createElement('div');
            dateNum.classList.add('date-number');
            dateNum.innerText = day;
            cell.appendChild(dateNum);

            // Bu hücrenin tarihi
            const cellDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const currentCellDate = new Date(cellDateStr);

            // Bu tarihe denk gelen görevleri bul (Aralık Kontrolü)
            const daysTasks = tasks.filter(t => {
                const sDate = new Date(t.startDate);
                const eDate = new Date(t.endDate);
                return currentCellDate >= sDate && currentCellDate <= eDate;
            });

            daysTasks.forEach(task => {
                const taskTag = document.createElement('div');
                taskTag.classList.add('task-tag');
                taskTag.innerText = task.title;
                taskTag.style.backgroundColor = task.color;
                cell.appendChild(taskTag);
            });

            calendarGrid.appendChild(cell);
        }
    }

    function addTask() {
        const title = document.getElementById('taskTitle').value;
        const startDate = document.getElementById('taskStartDate').value;
        const endDate = document.getElementById('taskEndDate').value;
        const color = document.getElementById('taskColor').value;

        if (!title || !startDate || !endDate) {
            alert('Lütfen görev adını, başlangıç ve bitiş tarihlerini doldurun!');
            return;
        }

        // Başlangıç tarihi bitişten büyük olamaz kontrolü
        if (new Date(startDate) > new Date(endDate)) {
            alert('Bitiş tarihi, başlangıç tarihinden önce olamaz!');
            return;
        }

        const newTask = {
            id: Date.now().toString(),
            title: title,
            startDate: startDate,
            endDate: endDate,
            color: color
        };

        tasks.push(newTask);
        saveAndRender();
        document.getElementById('taskTitle').value = '';
    }

    function deleteTask(id) {
        tasks = tasks.filter(task => task.id !== id);
        saveAndRender();
    }

    function saveAndRender() {
        localStorage.setItem('plannerTasks', JSON.stringify(tasks));
        renderCalendar(currentMonth, currentYear);
        renderSidebarTasks();
    }

    function renderSidebarTasks() {
        const listContainer = document.getElementById('sidebarTaskList');
        listContainer.innerHTML = '';

        const sortedTasks = [...tasks].sort((a, b) => new Date(a.startDate) - new Date(b.startDate));

        sortedTasks.forEach(task => {
            const item = document.createElement('div');
            item.classList.add('task-item');
            item.style.borderLeftColor = task.color;

            const sDateObj = new Date(task.startDate);
            const eDateObj = new Date(task.endDate);
            const formatOpts = { day: 'numeric', month: 'short' };

            const sDateStr = sDateObj.toLocaleDateString('tr-TR', formatOpts);
            const eDateStr = eDateObj.toLocaleDateString('tr-TR', formatOpts);

            // Eğer başlama ve bitiş aynı günse tek tarih yaz, farklıysa aralık yaz
            const dateDisplay = (sDateStr === eDateStr) ? sDateStr : `${sDateStr} - ${eDateStr}`;

            item.innerHTML = `
                <div class="task-info">
                    <span class="task-title">${task.title}</span>
                    <span class="task-date">📅 ${dateDisplay}</span>
                </div>
                <button class="delete-btn" onclick="deleteTask('${task.id}')">Sil</button>
            `;
            listContainer.appendChild(item);
        });
    }
</script>

</body>
</html>
