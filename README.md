# Site Mariage Lisa & Christ

Site de mariage statique PHP + MySQL, hébergé sur Hostinger.

## Structure

| Fichier / Dossier | Rôle |
|---|---|
| `index.php` | Page d'accueil du mariage |
| `config.php` | Connexion DB + constantes |
| `install.php` | Création des tables (à supprimer après) |
| `css/style.css` | Styles |
| `js/main.js` | Animations GSAP, countdown, RSVP |
| `api/rsvp.php` | Traitement du formulaire RSVP |
| `admin/` | Mini admin (galerie, invités, paramètres) |
| `uploads/gallery/` | Images uploadées |
| `.htaccess` | Config serveur |

## Installation sur Hostinger

1. Uploadez tous les fichiers dans `public_html/`
2. Allez sur `https://votre-domaine.fr/install.php`
3. Supprimez `install.php` après l'installation
4. Admin : `https://votre-domaine.fr/admin/` — identifiants : `admin` / `motsdepasse`

## Base de données

- DB : `u686558857_weeding`
- Tables : `guests`, `gallery`, `settings`
