<div class="main-content" id="account-area" style="display:none;">

    <div class="notes-area-header">
        <div class="notes-area-title-row">
            <h2 class="notes-area-title">👤 Hesabım</h2>
        </div>
    </div>

    <div class="account-grid">

        {{-- Profil Kartı --}}
        <div class="account-card profile-card">
            <div class="account-avatar" id="accountAvatar">?</div>
            <div class="account-name" id="accountName">Yükleniyor...</div>
            <div class="account-since">Üye since: <span id="accountSince">-</span></div>
        </div>

        {{-- İstatistikler --}}
        <div class="account-card stats-card">
            <div class="account-card-title">📊 İstatistikler</div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" id="statTasks">-</div>
                    <div class="stat-label">Toplam Görev</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="statNotes">-</div>
                    <div class="stat-label">Toplam Not</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="statDone">-</div>
                    <div class="stat-label">Tamamlanan Not</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="statProgress">-</div>
                    <div class="stat-label">Tamamlanma %</div>
                </div>
            </div>
        </div>

        {{-- Şifre Değiştir --}}
        <div class="account-card password-card">
            <div class="account-card-title">🔒 Şifre Değiştir</div>

            <div class="pw-form-group">
                <label>Mevcut Şifre</label>
                <input type="password" id="currentPassword" placeholder="••••••••">
            </div>
            <div class="pw-form-group">
                <label>Yeni Şifre</label>
                <input type="password" id="newPassword" placeholder="En az 6 karakter">
            </div>
            <div class="pw-form-group">
                <label>Yeni Şifre (Tekrar)</label>
                <input type="password" id="newPasswordConfirm" placeholder="••••••••">
            </div>

            <div id="pwMessage" style="display:none; padding:10px; border-radius:8px; font-size:13px; font-weight:600; margin-top:10px;"></div>

            <button onclick="changePassword()" class="pw-btn">Şifreyi Güncelle</button>
        </div>

    </div>

</div>
