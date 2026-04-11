const PRIORITY_COLORS = { high: '#e74c3c', medium: '#e67e22', low: '#27ae60' };
const PRIORITY_LABELS = { high: '🔴 Yüksek', medium: '🟡 Orta', low: '🟢 Düşük' };

let notes = [];
let currentFilter = 'all';
let currentView = 'card'; // 'card' veya 'table'

// ── YÜKLE ──
function loadNotes() {
    fetch('/notes', { headers: { 'X-CSRF-TOKEN': window.CSRF } })
        .then(res => res.json())
        .then(data => { notes = data; renderNotes(); renderNotesArea(); })
        .catch(() => { renderNotes(); renderNotesArea(); });
}

// ── NOT EKLE (sidebar) ──
function addNote() {
    const input = document.getElementById('noteInput');
    const text = input.value.trim();
    if (!text) { input.focus(); return; }

    const priority = document.getElementById('notePriority').value;

    fetch('/notes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF },
        body: JSON.stringify({ text, priority })
    })
        .then(res => res.json())
        .then(note => {
            notes.unshift(note);
            renderNotes();
            renderNotesArea();
            input.value = '';
            input.focus();
        })
        .catch(err => console.error('Not eklenemedi:', err));
}

// ── TAMAMLANDI TOGGLE ──
function toggleNote(id) {
    fetch(`/notes/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': window.CSRF }
    })
        .then(res => res.json())
        .then(updated => {
            const i = notes.findIndex(n => n.id == id);
            if (i !== -1) notes[i] = updated;
            renderNotes();
            renderNotesArea();
        });
}

// ── SİL ──
function deleteNote(id) {
    fetch(`/notes/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': window.CSRF }
    }).then(() => {
        notes = notes.filter(n => n.id != id);
        renderNotes();
        renderNotesArea();
    });
}

// ── AÇIKLAMA GÜNCELLE ──
function openNoteEditModal(id) {
    const note = notes.find(n => n.id == id);
    if (!note) return;
    document.getElementById('editNoteId').value = id;
    document.getElementById('editNoteDesc').value = note.description || '';
    document.getElementById('note-edit-modal').style.display = 'flex';
    setTimeout(() => document.getElementById('editNoteDesc').focus(), 100);
}

function closeNoteEditModal() {
    document.getElementById('note-edit-modal').style.display = 'none';
}

function saveNoteDesc() {
    const id = document.getElementById('editNoteId').value;
    const description = document.getElementById('editNoteDesc').value.trim();

    fetch(`/notes/${id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF },
        body: JSON.stringify({ description })
    })
        .then(res => res.json())
        .then(updated => {
            const i = notes.findIndex(n => n.id == id);
            if (i !== -1) notes[i] = updated;
            closeNoteEditModal();
            renderNotes();
            renderNotesArea();
        });
}

// ── FİLTRE (sidebar) ──
function filterNotes(filter, btn) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderNotes();
}

// ── FİLTRE (ana alan) ──
function filterNotesArea(filter, btn) {
    currentFilter = filter;
    document.querySelectorAll('.area-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    // Sidebar filtrelerini de senkronize et
    document.querySelectorAll('.filter-btn').forEach(b => {
        if (b.getAttribute('onclick')?.includes(`'${filter}'`)) b.classList.add('active');
        else b.classList.remove('active');
    });
    renderNotesArea();
    renderNotes();
}

// ── GÖRÜNÜM DEĞİŞTİR ──
function setView(view, btn) {
    currentView = view;
    document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.getElementById('notes-card-view').style.display = view === 'card' ? 'grid' : 'none';
    document.getElementById('notes-table-view').style.display = view === 'table' ? 'block' : 'none';

    renderNotesArea();
}

// ── FİLTRELİ LİSTE ──
function getFilteredNotes() {
    let filtered = [...notes];
    if (currentFilter === 'active') filtered = notes.filter(n => !n.done);
    else if (currentFilter === 'done') filtered = notes.filter(n => n.done);
    else if (currentFilter === 'high') filtered = notes.filter(n => n.priority === 'high');
    return filtered;
}

// ── SIDEBAR RENDER ──
function renderNotes() {
    const list = document.getElementById('noteList');
    const filtered = getFilteredNotes();

    if (filtered.length === 0) {
        list.innerHTML = '<p class="notes-empty">Burada henuz bir sey yok 🌱</p>';
        return;
    }

    list.innerHTML = filtered.map(note => `
        <div class="note-card ${note.done ? 'done' : ''}"
             style="border-left-color:${PRIORITY_COLORS[note.priority]}">
            <div class="note-check" onclick="toggleNote(${note.id})">${note.done ? '&#10003;' : ''}</div>
            <div class="note-body">
                <div class="note-text">${escapeHtml(note.text)}</div>
                <div class="note-priority" style="color:${PRIORITY_COLORS[note.priority]}">
                    ${PRIORITY_LABELS[note.priority]}
                </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:3px; flex-shrink:0;">
                <button class="note-delete" onclick="openNoteEditModal(${note.id})" style="color:rgba(255,255,255,0.5);">&#9998;</button>
                <button class="note-delete" onclick="deleteNote(${note.id})">&#10005;</button>
            </div>
        </div>
    `).join('');
}

// ── ANA ALAN RENDER ──
function renderNotesArea() {
    const filtered = getFilteredNotes();
    if (currentView === 'card') renderCardView(filtered);
    else renderTableView(filtered);
}

function renderCardView(filtered) {
    const grid = document.getElementById('notes-card-view');
    if (!grid) return;

    if (filtered.length === 0) {
        grid.innerHTML = '<p style="grid-column:1/-1; text-align:center; color:#bdc3c7; margin-top:40px; font-size:15px;">Henuz not yok 🌱</p>';
        return;
    }

    grid.innerHTML = filtered.map(note => `
        <div class="note-area-card ${note.done ? 'done' : ''}"
             style="border-top:3px solid ${PRIORITY_COLORS[note.priority]}">
            <div class="note-area-card-header">
                <div class="note-area-check ${note.done ? 'checked' : ''}" onclick="toggleNote(${note.id})">
                    ${note.done ? '&#10003;' : ''}
                </div>
                <span class="note-area-title-text ${note.done ? 'strikethrough' : ''}">${escapeHtml(note.text)}</span>
                <span class="note-area-priority-badge" style="background:${PRIORITY_COLORS[note.priority]}22; color:${PRIORITY_COLORS[note.priority]}">
                    ${PRIORITY_LABELS[note.priority]}
                </span>
            </div>
            ${note.description ? `
                <div class="note-area-desc">${escapeHtml(note.description)}</div>
            ` : `
                <div class="note-area-desc empty" onclick="openNoteEditModal(${note.id})">Not ekle...</div>
            `}
            <div class="note-area-footer">
                <span class="note-area-date">🕐 ${formatDate(note.updated_at)}</span>
                <div style="display:flex; gap:6px;">
                    <button onclick="openNoteEditModal(${note.id})" class="note-area-btn edit">&#9998; Düzenle</button>
                    <button onclick="deleteNote(${note.id})" class="note-area-btn delete">&#10005;</button>
                </div>
            </div>
        </div>
    `).join('');
}

function renderTableView(filtered) {
    const tbody = document.getElementById('notes-table-body');
    if (!tbody) return;

    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#bdc3c7; padding:40px;">Henuz not yok 🌱</td></tr>';
        return;
    }

    tbody.innerHTML = filtered.map(note => `
        <tr class="${note.done ? 'table-row-done' : ''}">
            <td>
                <div class="table-check ${note.done ? 'checked' : ''}" onclick="toggleNote(${note.id})">
                    ${note.done ? '&#10003;' : ''}
                </div>
            </td>
            <td>
                <span class="${note.done ? 'strikethrough' : ''}" style="font-weight:600; color:#2c3e50;">
                    ${escapeHtml(note.text)}
                </span>
            </td>
            <td>
                <span class="priority-badge" style="background:${PRIORITY_COLORS[note.priority]}22; color:${PRIORITY_COLORS[note.priority]};">
                    ${PRIORITY_LABELS[note.priority]}
                </span>
            </td>
            <td>
                ${note.description
        ? `<span style="color:#555; font-size:13px;">${escapeHtml(note.description)}</span>`
        : `<span style="color:#bdc3c7; font-size:13px; cursor:pointer;" onclick="openNoteEditModal(${note.id})">Not ekle...</span>`
    }
            </td>
            <td style="color:#95a5a6; font-size:12px; white-space:nowrap;">
                ${formatDate(note.updated_at)}
            </td>
            <td>
                <div style="display:flex; gap:5px;">
                    <button onclick="openNoteEditModal(${note.id})" class="note-area-btn edit" style="font-size:11px; padding:4px 8px;">&#9998;</button>
                    <button onclick="deleteNote(${note.id})" class="note-area-btn delete" style="font-size:11px; padding:4px 8px;">&#10005;</button>
                </div>
            </td>
        </tr>
    `).join('');
}

// ── YARDIMCILAR ──
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('tr-TR', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
