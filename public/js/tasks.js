window.tasks = [];

function loadTasks() {
    fetch('/tasks', { headers: { 'X-CSRF-TOKEN': window.CSRF } })
        .then(res => res.json())
        .then(data => {
            window.tasks = data.map(t => ({
                id: t.id,
                title: t.title,
                startDate: t.start_date,
                endDate: t.end_date,
                color: t.color
            }));
            renderCalendar(currentMonth, currentYear);
            renderSidebarTasks();
        })
        .catch(() => {
            renderCalendar(currentMonth, currentYear);
            renderSidebarTasks();
        });
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
    btn.disabled = true;
    btn.innerText = '...';

    fetch(id ? `/tasks/${id}` : '/tasks', {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF },
        body: JSON.stringify({ title, startDate, endDate, color })
    })
        .then(() => { cancelEdit(); loadTasks(); })
        .catch(() => alert('Bağlantı hatası!'))
        .finally(() => { btn.disabled = false; });
}

function deleteTask(id) {
    if (!confirm('Görevi silmek istiyor musun?')) return;
    fetch(`/tasks/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': window.CSRF }
    }).then(() => loadTasks());
}

window.editTask = function(id) {
    const task = window.tasks.find(t => t.id == id);
    if (!task) return;

    document.getElementById('editingTaskId').value = task.id;
    document.getElementById('taskTitle').value = task.title;
    document.getElementById('taskStartDate').value = task.startDate;
    document.getElementById('taskEndDate').value = task.endDate;
    document.getElementById('taskColor').value = task.color;
    document.getElementById('formTitle').innerText = '✏️ Görevi Düzenle';
    document.getElementById('saveBtn').innerText = '💾 Güncelle';
    document.getElementById('cancelBtn').style.display = 'block';

    document.querySelector('.sidebar-scroll')?.scrollTo({ top: 0, behavior: 'smooth' });

    // Takvim sekmesine geç
    switchTab('takvim', document.querySelector('.tab-btn'));
};

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

function renderSidebarTasks() {
    const container = document.getElementById('sidebarTaskList');
    container.innerHTML = '';

    if (window.tasks.length === 0) {
        container.innerHTML = '<p style="opacity:0.35;font-size:12px;text-align:center;margin-top:10px;">Henüz görev yok</p>';
        return;
    }

    [...window.tasks]
        .sort((a, b) => new Date(a.startDate) - new Date(b.startDate))
        .forEach(task => {
            const item = document.createElement('div');
            item.classList.add('task-item');
            item.style.borderLeftColor = task.color;

            const opts = { day: 'numeric', month: 'short' };
            const s = new Date(task.startDate).toLocaleDateString('tr-TR', opts);
            const e = new Date(task.endDate).toLocaleDateString('tr-TR', opts);

            item.innerHTML = `
                <div class="task-info">
                    <span class="task-title-text">${task.title}</span>
                    <span class="task-date-text">📅 ${s === e ? s : s + ' – ' + e}</span>
                </div>
                <div class="task-actions">
                    <button onclick="editTask(${task.id})" class="task-btn task-btn-edit">✏️</button>
                    <button onclick="deleteTask(${task.id})" class="task-btn task-btn-delete">🗑️</button>
                </div>`;
            container.appendChild(item);
        });
}

function generateEmergencyPlan() {
    const modal = document.getElementById('emergency-modal');
    const list = document.getElementById('emergency-list');
    modal.style.display = 'flex';

    const today = new Date();
    const upcoming = window.tasks
        .filter(t => new Date(t.endDate) >= today)
        .sort((a, b) => new Date(a.endDate) - new Date(b.endDate))
        .slice(0, 5);

    if (upcoming.length === 0) {
        list.innerHTML = '<p style="color:#7f8c8d;">Yaklaşan görev yok.</p>';
        return;
    }

    list.innerHTML = upcoming.map((t, i) => {
        const daysLeft = Math.ceil((new Date(t.endDate) - today) / 86400000);
        const color = daysLeft <= 2 ? '#e74c3c' : daysLeft <= 7 ? '#e67e22' : '#27ae60';
        return `<div class="emergency-item" style="border-left:4px solid ${t.color};">
            <strong>${i + 1}. ${t.title}</strong><br>
            <small style="color:#7f8c8d;">📅 Bitiş: ${t.endDate}</small><br>
            <span style="color:${color};font-weight:700;font-size:13px;">⏳ ${daysLeft} gün kaldı</span>
        </div>`;
    }).join('');
}
