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
