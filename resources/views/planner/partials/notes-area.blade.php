<div class="main-content" id="notes-area" style="display:none;">

    <div class="notes-area-header">
        <div class="notes-area-title-row">
            <h2 class="notes-area-title">💡 Aklımdakiler</h2>
            <div class="notes-view-toggle">
                <button class="view-btn active" id="btn-card" onclick="setView('card', this)" title="Kart görünümü">
                    ▦
                </button>
                <button class="view-btn" id="btn-table" onclick="setView('table', this)" title="Tablo görünümü">
                    ☰
                </button>
            </div>
        </div>

        {{-- Filtreler --}}
        <div class="notes-area-filters">
            <button class="area-filter-btn active" onclick="filterNotesArea('all', this)">Tümü</button>
            <button class="area-filter-btn" onclick="filterNotesArea('active', this)">Bekliyor</button>
            <button class="area-filter-btn" onclick="filterNotesArea('done', this)">Tamamlandı</button>
            <button class="area-filter-btn high" onclick="filterNotesArea('high', this)">🔴 Yüksek</button>
        </div>
    </div>

    {{-- KART GÖRÜNÜMÜ --}}
    <div id="notes-card-view" class="notes-card-grid"></div>

    {{-- TABLO GÖRÜNÜMÜ --}}
    <div id="notes-table-view" style="display:none; overflow-x:auto;">
        <table class="notes-table">
            <thead>
            <tr>
                <th style="width:40px;"></th>
                <th>Görev</th>
                <th style="width:100px;">Öncelik</th>
                <th>Not</th>
                <th style="width:140px;">Son Güncelleme</th>
                <th style="width:80px;"></th>
            </tr>
            </thead>
            <tbody id="notes-table-body"></tbody>
        </table>
    </div>

    {{-- Not düzenleme modalı --}}
    <div id="note-edit-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999; justify-content:center; align-items:center; padding:20px; backdrop-filter:blur(4px);">
        <div style="background:white; border-radius:16px; padding:25px; width:100%; max-width:480px;">
            <h3 style="margin:0 0 16px 0; color:#2c3e50;">✏️ Notu Düzenle</h3>
            <input type="hidden" id="editNoteId">
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#7f8c8d; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.5px;">Açıklama / Not</label>
                <textarea id="editNoteDesc" rows="4"
                          style="width:100%; padding:10px; border:1.5px solid #eee; border-radius:8px; font-size:14px; resize:vertical; font-family:inherit; box-sizing:border-box;"
                          placeholder="Bu görev hakkında not bırak..."></textarea>
            </div>
            <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button onclick="closeNoteEditModal()"
                        style="padding:10px 18px; background:#ecf0f1; border:none; border-radius:8px; cursor:pointer; font-weight:600; color:#7f8c8d;">
                    İptal
                </button>
                <button onclick="saveNoteDesc()"
                        style="padding:10px 18px; background:#27ae60; border:none; border-radius:8px; cursor:pointer; font-weight:700; color:white;">
                    Kaydet
                </button>
            </div>
        </div>
    </div>

</div>
