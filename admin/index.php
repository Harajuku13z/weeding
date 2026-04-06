<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Mariage</title>
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Jost',sans-serif;background:#f4f5f7;color:#2d3436;font-weight:300;min-height:100vh}
.login-wrap{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
.login-card{background:#fff;padding:48px 40px;border-radius:12px;width:100%;max-width:380px;box-shadow:0 8px 40px rgba(0,0,0,.06)}
.login-card h1{font-size:24px;font-weight:500;margin-bottom:8px;text-align:center}
.login-card p{font-size:13px;color:#888;text-align:center;margin-bottom:28px}
.login-card label{font-size:11px;letter-spacing:1px;text-transform:uppercase;font-weight:500;display:block;margin-bottom:6px;color:#555}
.login-card input{width:100%;padding:12px 16px;border:1px solid #ddd;border-radius:8px;font-family:inherit;font-size:14px;margin-bottom:16px;outline:none;transition:border-color .3s}
.login-card input:focus{border-color:#7B9EC4}
.login-card button{width:100%;padding:14px;background:#7B9EC4;color:#fff;border:none;border-radius:8px;font-family:inherit;font-size:13px;letter-spacing:2px;text-transform:uppercase;font-weight:500;cursor:pointer;transition:background .3s}
.login-card button:hover{background:#6B8EB5}
.login-err{background:#fde8e8;color:#c0392b;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;text-align:center}
/* ADMIN PANEL */
.admin{display:flex;min-height:100vh}
.sidebar{width:220px;background:#2C3E50;padding:24px 0;flex-shrink:0;display:flex;flex-direction:column}
.sidebar-brand{color:#fff;font-size:18px;font-weight:500;text-align:center;padding:0 16px 24px;border-bottom:1px solid rgba(255,255,255,.06)}
.sidebar-nav{flex:1;padding:16px 0}
.sidebar-nav a{display:flex;align-items:center;gap:10px;padding:12px 24px;color:rgba(255,255,255,.5);text-decoration:none;font-size:13px;transition:all .2s}
.sidebar-nav a:hover,.sidebar-nav a.active{color:#fff;background:rgba(255,255,255,.06)}
.sidebar-nav a i{font-size:16px;width:20px;text-align:center}
.sidebar-bottom{padding:16px 24px;border-top:1px solid rgba(255,255,255,.06)}
.sidebar-bottom a{color:rgba(255,255,255,.4);text-decoration:none;font-size:12px}
.main{flex:1;padding:32px;overflow-y:auto}
.main h2{font-size:22px;font-weight:500;margin-bottom:24px}
.card{background:#fff;border-radius:12px;padding:28px;margin-bottom:20px;box-shadow:0 2px 12px rgba(0,0,0,.04)}
.card h3{font-size:16px;font-weight:500;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:8px;font-family:inherit;font-size:12px;letter-spacing:1px;text-transform:uppercase;font-weight:500;cursor:pointer;transition:all .3s}
.btn-blue{background:#7B9EC4;color:#fff}.btn-blue:hover{background:#6B8EB5}
.btn-red{background:#D96B6B;color:#fff}.btn-red:hover{background:#c0392b}
.btn-sm{padding:6px 14px;font-size:11px}
.form-row{margin-bottom:16px}
.form-row label{font-size:11px;letter-spacing:1px;text-transform:uppercase;font-weight:500;display:block;margin-bottom:6px;color:#555}
.form-row input,.form-row textarea{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-family:inherit;font-size:14px;outline:none;transition:border-color .3s}
.form-row input:focus,.form-row textarea:focus{border-color:#7B9EC4}
textarea{resize:vertical}
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;font-size:10px;letter-spacing:1px;text-transform:uppercase;color:#888;font-weight:500;padding:10px 12px;border-bottom:2px solid #eee}
td{padding:10px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle}
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:10px;letter-spacing:1px;text-transform:uppercase;font-weight:500}
.badge-ok{background:#e8f5e9;color:#2e7d32}
.badge-warn{background:#fff3e0;color:#e65100}
.badge-err{background:#fde8e8;color:#c0392b}
.badge-grey{background:#f0f0f0;color:#888}
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-top:16px}
.gallery-thumb{position:relative;aspect-ratio:1;border-radius:8px;overflow:hidden;background:#f0f0f0}
.gallery-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.gallery-thumb .del-btn{position:absolute;top:6px;right:6px;background:rgba(0,0,0,.6);color:#fff;border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;opacity:0;transition:opacity .2s}
.gallery-thumb:hover .del-btn{opacity:1}
.toast{position:fixed;bottom:24px;right:24px;background:#2C3E50;color:#fff;padding:14px 24px;border-radius:8px;font-size:13px;box-shadow:0 8px 32px rgba(0,0,0,.15);transform:translateY(100px);opacity:0;transition:all .4s;z-index:9999}
.toast.show{transform:translateY(0);opacity:1}
.page{display:none}.page.active{display:block}
@media(max-width:768px){.sidebar{width:60px}.sidebar-brand{font-size:0;padding:12px}.sidebar-nav a span{display:none}.sidebar-nav a{padding:14px 0;justify-content:center}.sidebar-bottom{text-align:center}.sidebar-bottom a span{display:none}.main{padding:16px}}
</style>
</head>
<body>
<?php if (!isAdmin()): ?>
<div class="login-wrap">
    <div class="login-card">
        <h1>Administration</h1>
        <p>Mariage Lisa &amp; Christ</p>
        <?php if (isset($_GET['error'])): ?>
        <div class="login-err">Identifiants incorrects.</div>
        <?php endif; ?>
        <form method="POST" action="auth.php">
            <input type="hidden" name="action" value="login">
            <label>Identifiant</label>
            <input type="text" name="username" required autofocus>
            <label>Mot de passe</label>
            <input type="password" name="password" required>
            <button type="submit">Connexion</button>
        </form>
    </div>
</div>
<?php else: ?>
<div class="admin">
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="bi bi-heart-fill"></i> Admin</div>
        <nav class="sidebar-nav">
            <a href="#" class="active" data-page="gallery"><i class="bi bi-images"></i><span>Galerie</span></a>
            <a href="#" data-page="guests"><i class="bi bi-people"></i><span>Invités</span></a>
            <a href="#" data-page="settings"><i class="bi bi-gear"></i><span>Paramètres</span></a>
        </nav>
        <div class="sidebar-bottom">
            <a href="/" target="_blank"><i class="bi bi-eye"></i> <span>Voir le site</span></a><br><br>
            <form method="POST" action="auth.php" style="display:inline">
                <input type="hidden" name="action" value="logout">
                <button type="submit" style="background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;font-family:inherit;font-size:12px"><i class="bi bi-box-arrow-left"></i> <span>Déconnexion</span></button>
            </form>
        </div>
    </aside>
    <div class="main">

        <!-- GALERIE -->
        <div class="page active" id="page-gallery">
            <h2>Galerie photos</h2>
            <div class="card">
                <h3><i class="bi bi-upload"></i> Ajouter une image</h3>
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="form-row"><label>Image (JPG, PNG, WEBP – max 5 Mo)</label><input type="file" name="image" accept="image/*" required></div>
                    <div class="form-row"><label>Légende (optionnel)</label><input type="text" name="caption" placeholder="Description de la photo"></div>
                    <button type="submit" class="btn btn-blue"><i class="bi bi-cloud-upload"></i> Uploader</button>
                </form>
            </div>
            <div class="card">
                <h3><i class="bi bi-images"></i> Images actuelles</h3>
                <div class="gallery-grid" id="adminGallery"></div>
            </div>
        </div>

        <!-- INVITÉS -->
        <div class="page" id="page-guests">
            <h2>Invités &amp; RSVP</h2>
            <div class="card">
                <h3><i class="bi bi-person-plus"></i> Ajouter un invité</h3>
                <form id="addGuestForm" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
                    <div class="form-row" style="flex:1;min-width:140px"><label>Code</label><input type="text" name="code" placeholder="INVITE06" required style="text-transform:uppercase"></div>
                    <div class="form-row" style="flex:1;min-width:140px"><label>Nom (optionnel)</label><input type="text" name="name" placeholder="Jean Dupont"></div>
                    <button type="submit" class="btn btn-blue btn-sm"><i class="bi bi-plus"></i> Ajouter</button>
                </form>
            </div>
            <div class="card">
                <h3><i class="bi bi-table"></i> Liste des invités</h3>
                <div style="overflow-x:auto"><table>
                    <thead><tr><th>Code</th><th>Nom</th><th>Statut</th><th>Accomp.</th><th>Régime</th><th>Message</th><th>Répondu</th><th></th></tr></thead>
                    <tbody id="guestsTable"></tbody>
                </table></div>
            </div>
        </div>

        <!-- PARAMÈTRES -->
        <div class="page" id="page-settings">
            <h2>Paramètres du site</h2>
            <div class="card">
                <form id="settingsForm">
                    <div class="form-row"><label>Prénom de la mariée</label><input type="text" name="bride_name" id="set_bride_name"></div>
                    <div class="form-row"><label>Prénom du marié</label><input type="text" name="groom_name" id="set_groom_name"></div>
                    <div class="form-row"><label>Date du mariage</label><input type="date" name="wedding_date" id="set_wedding_date"></div>
                    <div class="form-row"><label>Sous-titre hero</label><input type="text" name="hero_subtitle" id="set_hero_subtitle"></div>
                    <div class="form-row"><label>Citation</label><input type="text" name="quote" id="set_quote"></div>
                    <div class="form-row"><label>Texte histoire</label><textarea name="story_text" id="set_story_text" rows="5"></textarea></div>
                    <button type="submit" class="btn btn-blue"><i class="bi bi-check-lg"></i> Sauvegarder</button>
                </form>
            </div>
        </div>

    </div>
</div>
<div class="toast" id="toast"></div>
<script>
const API = 'api.php';

function toast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

/* NAV */
document.querySelectorAll('[data-page]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('[data-page]').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.page').forEach(x => x.classList.remove('active'));
        a.classList.add('active');
        document.getElementById('page-' + a.dataset.page).classList.add('active');
    });
});

/* ─── GALLERY ────────────────────────────────────── */
async function loadGallery() {
    const res = await fetch(API + '?action=gallery_list');
    const json = await res.json();
    const grid = document.getElementById('adminGallery');
    if (!json.data.length) { grid.innerHTML = '<p style="color:#888;font-size:13px">Aucune image. Ajoutez-en via le formulaire ci-dessus.</p>'; return; }
    grid.innerHTML = json.data.map(img => `
        <div class="gallery-thumb">
            <img src="../uploads/gallery/${img.filename}" alt="${img.caption || ''}">
            <button class="del-btn" onclick="delImage(${img.id})"><i class="bi bi-trash"></i></button>
        </div>
    `).join('');
}

document.getElementById('uploadForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'gallery_upload');
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    if (json.success) { e.target.reset(); loadGallery(); }
});

async function delImage(id) {
    if (!confirm('Supprimer cette image ?')) return;
    const fd = new FormData();
    fd.append('action', 'gallery_delete');
    fd.append('id', id);
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    loadGallery();
}

/* ─── GUESTS ─────────────────────────────────────── */
async function loadGuests() {
    const res = await fetch(API + '?action=guests_list');
    const json = await res.json();
    const tb = document.getElementById('guestsTable');
    if (!json.data.length) { tb.innerHTML = '<tr><td colspan="8" style="color:#888">Aucun invité.</td></tr>'; return; }
    const badges = { accepted: 'badge-ok', maybe: 'badge-warn', declined: 'badge-err', pending: 'badge-grey' };
    const labels = { accepted: 'Accepté', maybe: 'Peut-être', declined: 'Décliné', pending: 'En attente' };
    tb.innerHTML = json.data.map(g => `
        <tr>
            <td><code>${g.code}</code></td>
            <td>${g.name || '—'}</td>
            <td><span class="badge ${badges[g.status] || 'badge-grey'}">${labels[g.status] || g.status}</span></td>
            <td>${g.companions || 0}</td>
            <td>${g.dietary || '—'}</td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${g.message || '—'}</td>
            <td>${g.responded_at ? new Date(g.responded_at).toLocaleDateString('fr') : '—'}</td>
            <td><button class="btn btn-red btn-sm" onclick="delGuest(${g.id})"><i class="bi bi-trash"></i></button></td>
        </tr>
    `).join('');
}

document.getElementById('addGuestForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'guest_add');
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    if (json.success) { e.target.reset(); loadGuests(); }
});

async function delGuest(id) {
    if (!confirm('Supprimer cet invité ?')) return;
    const fd = new FormData();
    fd.append('action', 'guest_delete');
    fd.append('id', id);
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    loadGuests();
}

/* ─── SETTINGS ───────────────────────────────────── */
async function loadSettings() {
    const res = await fetch(API + '?action=settings_get');
    const json = await res.json();
    if (json.data) {
        Object.entries(json.data).forEach(([k, v]) => {
            const el = document.getElementById('set_' + k);
            if (el) el.value = v;
        });
    }
}

document.getElementById('settingsForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'settings_save');
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
});

/* INIT */
loadGallery();
loadGuests();
loadSettings();
</script>
<?php endif; ?>
</body>
</html>
