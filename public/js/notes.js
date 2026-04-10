const PRIORITY_COLORS = { high: '#e74c3c', medium: '#e67e22', low: '#27ae60' };
const PRIORITY_LABELS = { high: '🔴 Yüksek', medium: '🟡 Orta', low: '🟢 Düşük' };

let notes = [];
let currentFilter = 'all';

function loadNotes() {
    fetch('/notes', { headers: { 'X-CSRF-TOKEN': window.CSRF } })
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
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF },
        body: JSON.stringify({ text, priority })
    })
        .then(res => res.json())
        .then(note => {
            notes.unshift(note);
            renderNotes();
            input.value = '';
            input.focus();
        });
}

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
        });
}

function deleteNote(id) {
    fetch(`/notes/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': window.CSRF }
    }).then(() => {
        notes = notes.filter(n => n.id != id);
        renderNotes();
    });
}

function filterNotes(filter, btn) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderNotes();
}

function renderNotes() {
    const list = document.getElementById('noteList');

    let filtered = [...notes];
    if (currentFilter === 'active') filtered = notes.filter(n => !n.done);
    else if (currentFilter === 'done') filtered = notes.filter(n => n.done);
    else if (currentFilter === 'high') filtered = notes.filter(n => n.priority === 'high');

    if (filtered.length === 0) {
        list.innerHTML = '<p class="notes-empty">Burada henüz bir şey yok 🌱</p>';
        return;
    }

    list.innerHTML = filtered.map(note => `
        <div class="note-card ${note.done ? 'done' : ''}"
             style="border-left-color:${PRIORITY_COLORS[note.priority]}">
            <div class="note-check" onclick="toggleNote(${note.id})">${note.done ? '✓' : ''}</div>
            <div class="note-body">
                <div class="note-text">${escapeHtml(note.text)}</div>
                <div class="note-priority" style="color:${PRIORITY_COLORS[note.priority]}">
                    ${PRIORITY_LABELS[note.priority]}
                </div>
            </div>
            <button class="note-delete" onclick="deleteNote(${note.id})">✕</button>
        </div>
    `).join('');
}

function escapeHtml(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
