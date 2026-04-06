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
            <a href="#" data-page="programme"><i class="bi bi-clock-history"></i><span>Programme</span></a>
            <a href="#" data-page="ambiance"><i class="bi bi-brush"></i><span>Ambiance</span></a>
            <a href="#" data-page="lieux"><i class="bi bi-geo-alt"></i><span>Lieux</span></a>
            <a href="#" data-page="guests"><i class="bi bi-people"></i><span>Invités</span></a>
            <a href="#" data-page="theme"><i class="bi bi-palette"></i><span>Thème</span></a>
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

        <!-- PROGRAMME -->
        <div class="page" id="page-programme">
            <h2>Programme de la journée</h2>
            <div class="card">
                <h3><i class="bi bi-plus-circle"></i> Ajouter un moment</h3>
                <form id="addProgForm" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
                    <div class="form-row" style="width:80px"><label>Heure</label><input type="text" name="time_label" placeholder="20:00" required></div>
                    <div class="form-row" style="flex:1;min-width:160px"><label>Titre</label><input type="text" name="title" placeholder="Dîner de gala" required></div>
                    <div class="form-row" style="flex:2;min-width:200px"><label>Description</label><input type="text" name="description" placeholder="Un repas raffiné..."></div>
                    <div class="form-row" style="width:70px"><label>Ordre</label><input type="number" name="sort_order" value="0" min="0"></div>
                    <button type="submit" class="btn btn-blue btn-sm"><i class="bi bi-plus"></i> Ajouter</button>
                </form>
            </div>
            <div class="card">
                <h3><i class="bi bi-list-ol"></i> Programme actuel</h3>
                <div style="overflow-x:auto"><table>
                    <thead><tr><th>Ordre</th><th>Heure</th><th>Titre</th><th>Description</th><th></th><th></th></tr></thead>
                    <tbody id="progTable"></tbody>
                </table></div>
            </div>
        </div>

        <!-- AMBIANCE -->
        <div class="page" id="page-ambiance">
            <h2>Ambiance &amp; Univers</h2>

            <div class="card">
                <h3><i class="bi bi-image"></i> Photos d'ambiance</h3>
                <p style="font-size:13px;color:#888;margin-bottom:16px">Ajoutez 1 ou 2 photos qui illustrent l'univers de votre mariage (moodboard, déco, nature…).</p>
                <form id="uploadAmbianceForm" enctype="multipart/form-data">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
                        <div class="form-row" style="flex:1;min-width:200px"><label>Image (JPG, PNG, WEBP – max 5 Mo)</label><input type="file" name="image" accept="image/*" required style="padding:6px"></div>
                        <div class="form-row" style="flex:1;min-width:180px"><label>Légende (optionnel)</label><input type="text" name="caption" placeholder="Ex: Notre inspiration…"></div>
                        <button type="submit" class="btn btn-blue btn-sm"><i class="bi bi-cloud-upload"></i> Uploader</button>
                    </div>
                </form>
                <div class="gallery-grid" id="ambianceGallery" style="margin-top:16px"></div>
            </div>

            <div class="card">
                <h3><i class="bi bi-palette-fill"></i> Palette de couleurs</h3>
                <p style="font-size:13px;color:#888;margin-bottom:16px">Définissez les couleurs de votre mariage. Elles seront affichées sous forme de ronds sur le site.</p>
                <form id="addColorForm" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;margin-bottom:20px">
                    <div class="form-row">
                        <label>Couleur</label>
                        <input type="color" name="color_hex" value="#A8C8E0" style="width:48px;height:36px;padding:2px;border:1px solid #ddd;border-radius:6px;cursor:pointer">
                    </div>
                    <div class="form-row" style="flex:1;min-width:120px">
                        <label>Nom</label>
                        <input type="text" name="color_name" placeholder="Bleu ciel">
                    </div>
                    <div class="form-row" style="width:60px">
                        <label>Ordre</label>
                        <input type="number" name="sort_order" value="0" min="0">
                    </div>
                    <button type="submit" class="btn btn-blue btn-sm"><i class="bi bi-plus"></i> Ajouter</button>
                </form>
                <div id="colorsList"></div>
            </div>
        </div>

        <!-- LIEUX -->
        <div class="page" id="page-lieux">
            <h2>Lieux</h2>
            <div class="card">
                <h3><i class="bi bi-plus-circle"></i> Ajouter un lieu</h3>
                <form id="addLieuForm" enctype="multipart/form-data">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px">
                        <div class="form-row" style="flex:1;min-width:180px"><label>Nom du lieu</label><input type="text" name="name" placeholder="Mairie de Chevigny" required></div>
                        <div class="form-row" style="flex:2;min-width:200px"><label>Adresse</label><input type="text" name="address" placeholder="Place du Général de Gaulle, 21800..."></div>
                        <div class="form-row" style="width:70px"><label>Ordre</label><input type="number" name="sort_order" value="0" min="0"></div>
                    </div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px">
                        <div class="form-row" style="flex:1;min-width:200px"><label>Photo du lieu</label><input type="file" name="photo" accept="image/*" style="padding:6px"></div>
                        <div class="form-row" style="flex:1;min-width:200px"><label>Lien Google Maps (itinéraire)</label><input type="url" name="maps_url" placeholder="https://maps.google.com/?q=..."></div>
                        <div class="form-row" style="flex:1;min-width:200px"><label>Lien iframe embed Maps</label><input type="url" name="maps_embed" placeholder="https://www.google.com/maps/embed?pb=..."></div>
                    </div>
                    <button type="submit" class="btn btn-blue"><i class="bi bi-plus"></i> Ajouter</button>
                </form>
            </div>
            <div class="card">
                <h3><i class="bi bi-geo-alt-fill"></i> Lieux actuels</h3>
                <div id="lieuxList"></div>
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

        <!-- THÈME -->
        <div class="page" id="page-theme">
            <h2>Thème &amp; Couleurs</h2>
            <div class="card">
                <h3><i class="bi bi-palette2"></i> Couleurs du site</h3>
                <p style="font-size:13px;color:#888;margin-bottom:20px">Modifiez les couleurs principales. Les changements sont visibles sur le site public après sauvegarde.</p>
                <form id="themeForm">
                    <div style="display:flex;gap:24px;flex-wrap:wrap">
                        <div class="form-row">
                            <label>Couleur principale</label>
                            <div style="display:flex;gap:8px;align-items:center">
                                <input type="color" name="theme_primary" id="set_theme_primary" value="#A8C8E0" style="width:48px;height:36px;padding:2px;border:1px solid #ddd;border-radius:6px;cursor:pointer">
                                <input type="text" id="set_theme_primary_hex" value="#A8C8E0" style="width:90px;padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:monospace">
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Couleur accent</label>
                            <div style="display:flex;gap:8px;align-items:center">
                                <input type="color" name="theme_accent" id="set_theme_accent" value="#7B9EC4" style="width:48px;height:36px;padding:2px;border:1px solid #ddd;border-radius:6px;cursor:pointer">
                                <input type="text" id="set_theme_accent_hex" value="#7B9EC4" style="width:90px;padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:monospace">
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Couleur foncée</label>
                            <div style="display:flex;gap:8px;align-items:center">
                                <input type="color" name="theme_dark" id="set_theme_dark" value="#2C3E50" style="width:48px;height:36px;padding:2px;border:1px solid #ddd;border-radius:6px;cursor:pointer">
                                <input type="text" id="set_theme_dark_hex" value="#2C3E50" style="width:90px;padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:monospace">
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:24px;padding:20px;border-radius:8px;border:1px solid #eee">
                        <p style="font-size:10px;letter-spacing:1px;text-transform:uppercase;color:#888;margin-bottom:12px">Aperçu</p>
                        <div style="display:flex;gap:12px;flex-wrap:wrap">
                            <div id="swatch-primary" style="width:90px;height:55px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;font-weight:500;letter-spacing:1px;background:#A8C8E0">Principal</div>
                            <div id="swatch-accent" style="width:90px;height:55px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;font-weight:500;letter-spacing:1px;background:#7B9EC4">Accent</div>
                            <div id="swatch-dark" style="width:90px;height:55px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;font-weight:500;letter-spacing:1px;background:#2C3E50">Foncé</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-blue" style="margin-top:20px"><i class="bi bi-check-lg"></i> Sauvegarder le thème</button>
                </form>
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
                    <div class="form-row"><label>Date limite RSVP</label><input type="text" name="rsvp_deadline" id="set_rsvp_deadline" placeholder="15 Mai 2025"></div>
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

/* ─── PROGRAMME ──────────────────────────────────── */
async function loadProgramme() {
    const res = await fetch(API + '?action=programme_list');
    const json = await res.json();
    const tb = document.getElementById('progTable');
    if (!json.data.length) { tb.innerHTML = '<tr><td colspan="6" style="color:#888">Aucun élément. Ajoutez-en via le formulaire.</td></tr>'; return; }
    tb.innerHTML = json.data.map(p => `
        <tr id="prog-row-${p.id}">
            <td><input type="number" value="${p.sort_order}" style="width:50px;padding:4px 8px;border:1px solid #ddd;border-radius:4px" data-field="sort_order" data-id="${p.id}"></td>
            <td><input type="text" value="${p.time_label}" style="width:60px;padding:4px 8px;border:1px solid #ddd;border-radius:4px" data-field="time_label" data-id="${p.id}"></td>
            <td><input type="text" value="${p.title}" style="width:160px;padding:4px 8px;border:1px solid #ddd;border-radius:4px" data-field="title" data-id="${p.id}"></td>
            <td><input type="text" value="${p.description || ''}" style="width:100%;padding:4px 8px;border:1px solid #ddd;border-radius:4px" data-field="description" data-id="${p.id}"></td>
            <td><button class="btn btn-blue btn-sm" onclick="saveProg(${p.id})"><i class="bi bi-check-lg"></i></button></td>
            <td><button class="btn btn-red btn-sm" onclick="delProg(${p.id})"><i class="bi bi-trash"></i></button></td>
        </tr>
    `).join('');
}

document.getElementById('addProgForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'programme_add');
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    if (json.success) { e.target.reset(); loadProgramme(); }
});

async function saveProg(id) {
    const row = document.getElementById('prog-row-' + id);
    const fd = new FormData();
    fd.append('action', 'programme_update');
    fd.append('id', id);
    row.querySelectorAll('input[data-id="' + id + '"]').forEach(inp => {
        fd.append(inp.dataset.field, inp.value);
    });
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    if (json.success) loadProgramme();
}

async function delProg(id) {
    if (!confirm('Supprimer cet élément du programme ?')) return;
    const fd = new FormData();
    fd.append('action', 'programme_delete');
    fd.append('id', id);
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    loadProgramme();
}

/* ─── AMBIANCE PHOTOS ────────────────────────────── */
async function loadAmbiancePhotos() {
    const res = await fetch(API + '?action=ambiance_photos_list');
    const json = await res.json();
    const grid = document.getElementById('ambianceGallery');
    if (!json.data.length) { grid.innerHTML = '<p style="color:#888;font-size:13px">Aucune photo d\'ambiance.</p>'; return; }
    grid.innerHTML = json.data.map(img => `
        <div class="gallery-thumb">
            <img src="../uploads/ambiance/${img.filename}" alt="${img.caption || ''}">
            <button class="del-btn" onclick="delAmbiancePhoto(${img.id})"><i class="bi bi-trash"></i></button>
        </div>
    `).join('');
}

document.getElementById('uploadAmbianceForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'ambiance_photo_upload');
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    if (json.success) { e.target.reset(); loadAmbiancePhotos(); }
});

async function delAmbiancePhoto(id) {
    if (!confirm('Supprimer cette photo d\'ambiance ?')) return;
    const fd = new FormData();
    fd.append('action', 'ambiance_photo_delete');
    fd.append('id', id);
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    loadAmbiancePhotos();
}

/* ─── AMBIANCE COLORS ────────────────────────────── */
async function loadAmbianceColors() {
    const res = await fetch(API + '?action=ambiance_colors_list');
    const json = await res.json();
    const box = document.getElementById('colorsList');
    if (!json.data.length) { box.innerHTML = '<p style="color:#888;font-size:13px">Aucune couleur. Ajoutez-en via le formulaire.</p>'; return; }
    box.innerHTML = '<div style="display:flex;flex-wrap:wrap;gap:12px">' + json.data.map(c => `
        <div style="border:1px solid #eee;border-radius:10px;padding:14px;display:flex;align-items:center;gap:12px;min-width:240px">
            <input type="color" value="${c.color_hex}" data-cf="color_hex" data-cid="${c.id}" style="width:40px;height:32px;padding:1px;border:1px solid #ddd;border-radius:6px;cursor:pointer">
            <input type="text" value="${c.color_name||''}" data-cf="color_name" data-cid="${c.id}" placeholder="Nom" style="flex:1;padding:6px 10px;border:1px solid #ddd;border-radius:6px;font-size:13px;min-width:80px">
            <input type="number" value="${c.sort_order}" data-cf="sort_order" data-cid="${c.id}" style="width:45px;padding:6px 8px;border:1px solid #ddd;border-radius:6px;font-size:13px">
            <button class="btn btn-blue btn-sm" onclick="saveColor(${c.id})" style="padding:6px 10px"><i class="bi bi-check-lg"></i></button>
            <button class="btn btn-red btn-sm" onclick="delColor(${c.id})" style="padding:6px 10px"><i class="bi bi-trash"></i></button>
        </div>
    `).join('') + '</div>';
}

document.getElementById('addColorForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'ambiance_color_add');
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    if (json.success) { e.target.reset(); loadAmbianceColors(); }
});

async function saveColor(id) {
    const fd = new FormData();
    fd.append('action', 'ambiance_color_update');
    fd.append('id', id);
    document.querySelectorAll('[data-cid="' + id + '"]').forEach(inp => {
        fd.append(inp.dataset.cf, inp.value);
    });
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
}

async function delColor(id) {
    if (!confirm('Supprimer cette couleur ?')) return;
    const fd = new FormData();
    fd.append('action', 'ambiance_color_delete');
    fd.append('id', id);
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    loadAmbianceColors();
}

/* ─── LIEUX ──────────────────────────────────────── */
async function loadLieux() {
    const res = await fetch(API + '?action=lieux_list');
    const json = await res.json();
    const box = document.getElementById('lieuxList');
    if (!json.data.length) { box.innerHTML = '<p style="color:#888;font-size:13px">Aucun lieu. Ajoutez-en via le formulaire.</p>'; return; }
    const base = '../uploads/lieux/';
    box.innerHTML = json.data.map(l => `
        <div id="lieu-${l.id}" style="border:1px solid #eee;border-radius:8px;padding:20px;margin-bottom:12px">
            <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:12px;align-items:flex-start">
                <div style="flex-shrink:0">
                    ${l.photo ? `<img src="${base}${l.photo}" style="width:120px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd">` : '<div style="width:120px;height:80px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#bbb;font-size:12px">Pas de photo</div>'}
                    <div style="margin-top:6px"><label style="font-size:11px;color:#888">Changer la photo</label><input type="file" accept="image/*" data-f="photo" data-lid="${l.id}" style="font-size:11px;width:120px"></div>
                </div>
                <div style="flex:1;display:flex;gap:10px;flex-wrap:wrap">
                    <div class="form-row" style="flex:1;min-width:160px"><label>Nom</label><input type="text" value="${l.name}" data-f="name" data-lid="${l.id}" style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:6px"></div>
                    <div class="form-row" style="flex:2;min-width:200px"><label>Adresse</label><input type="text" value="${l.address||''}" data-f="address" data-lid="${l.id}" style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:6px"></div>
                    <div class="form-row" style="width:60px"><label>Ordre</label><input type="number" value="${l.sort_order}" data-f="sort_order" data-lid="${l.id}" style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:6px"></div>
                </div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px">
                <div class="form-row" style="flex:1;min-width:200px"><label>Lien itinéraire</label><input type="url" value="${l.maps_url||''}" data-f="maps_url" data-lid="${l.id}" style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:6px"></div>
                <div class="form-row" style="flex:1;min-width:200px"><label>Iframe embed</label><input type="url" value="${l.maps_embed||''}" data-f="maps_embed" data-lid="${l.id}" style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:6px"></div>
            </div>
            <div style="display:flex;gap:8px">
                <button class="btn btn-blue btn-sm" onclick="saveLieu(${l.id})"><i class="bi bi-check-lg"></i> Sauvegarder</button>
                <button class="btn btn-red btn-sm" onclick="delLieu(${l.id})"><i class="bi bi-trash"></i> Supprimer</button>
            </div>
        </div>
    `).join('');
}

document.getElementById('addLieuForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'lieux_add');
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    if (json.success) { e.target.reset(); loadLieux(); }
});

async function saveLieu(id) {
    const fd = new FormData();
    fd.append('action', 'lieux_update');
    fd.append('id', id);
    document.querySelectorAll('[data-lid="' + id + '"]').forEach(inp => {
        if (inp.type === 'file') {
            if (inp.files.length) fd.append('photo', inp.files[0]);
        } else {
            fd.append(inp.dataset.f, inp.value);
        }
    });
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    if (json.success) loadLieux();
}

async function delLieu(id) {
    if (!confirm('Supprimer ce lieu ?')) return;
    const fd = new FormData();
    fd.append('action', 'lieux_delete');
    fd.append('id', id);
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
    loadLieux();
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

/* ─── THEME ──────────────────────────────────────── */
const themeFields = ['theme_primary', 'theme_accent', 'theme_dark'];
const swatchMap = { theme_primary: 'swatch-primary', theme_accent: 'swatch-accent', theme_dark: 'swatch-dark' };

function syncThemeInputs() {
    themeFields.forEach(f => {
        const color = document.getElementById('set_' + f);
        const hex = document.getElementById('set_' + f + '_hex');
        const swatch = document.getElementById(swatchMap[f]);
        if (color && hex) {
            color.addEventListener('input', () => { hex.value = color.value; if (swatch) swatch.style.background = color.value; });
            hex.addEventListener('input', () => { if (/^#[0-9A-Fa-f]{6}$/.test(hex.value)) { color.value = hex.value; if (swatch) swatch.style.background = hex.value; } });
        }
    });
}
syncThemeInputs();

document.getElementById('themeForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'settings_save');
    const res = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    toast(json.message);
});

/* ─── SETTINGS ───────────────────────────────────── */
async function loadSettings() {
    const res = await fetch(API + '?action=settings_get');
    const json = await res.json();
    if (json.data) {
        Object.entries(json.data).forEach(([k, v]) => {
            const el = document.getElementById('set_' + k);
            if (el) {
                el.value = v;
                const hex = document.getElementById('set_' + k + '_hex');
                if (hex) hex.value = v;
                if (swatchMap[k]) {
                    const sw = document.getElementById(swatchMap[k]);
                    if (sw) sw.style.background = v;
                }
            }
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
loadProgramme();
loadAmbiancePhotos();
loadAmbianceColors();
loadLieux();
loadGuests();
loadSettings();
</script>
<?php endif; ?>
</body>
</html>
