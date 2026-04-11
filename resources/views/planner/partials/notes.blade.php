<div class="tab-content" id="tab-notlar">
    <div class="notes-container">

        {{-- Hızlı not ekle --}}
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

        {{-- Filtreler --}}
        <div class="note-filters">
            <button class="filter-btn active" onclick="filterNotes('all', this)">Tümü</button>
            <button class="filter-btn" onclick="filterNotes('active', this)">Bekliyor</button>
            <button class="filter-btn" onclick="filterNotes('done', this)">Tamamlandı</button>
            <button class="filter-btn" onclick="filterNotes('high', this)">🔴 Yüksek</button>
        </div>

        {{-- Not listesi --}}
        <div class="note-list" id="noteList"></div>

    </div>

    {{-- Çıkış şeridi --}}
    <button onclick="handleLogout()" class="logout-strip">🚪 Çıkış Yap</button>
</div>
