# 🌐 Déploiement sur cPanel — Guide pas à pas

Objectif sécurité : **le web ne doit servir que `public/`**, jamais `.env` ni le code.

Deux options selon votre domaine. **L'Option 1 est la plus simple et la plus propre** —
choisissez-la si possible.

---

## ✅ Option 1 — Document Root sur `public/` (recommandée)

Fonctionne pour un **sous-domaine** (ex. `ecole.mondomaine.com`), un **domaine
additionnel**, et souvent pour le **domaine principal**.

1. **cPanel → Domaines** (ou « Sous-domaines » / « Domaines additionnels »)
2. Créez/ouvrez le domaine et réglez son **Document Root** sur :
   ```
   gestionscholaire/public
   ```
3. C'est tout pour la racine : aucun fichier `index.php` à modifier.

➡️ Passez à la section **« Étapes communes »** ci-dessous.

---

## 🔁 Option 2 — Domaine principal verrouillé sur `public_html`

Si cPanel ne permet pas de changer le Document Root du domaine principal.

Structure cible (l'app est **hors** du web root, donc protégée) :
```
/home/UTILISATEUR/
├── gestionscholaire/      ← l'application (privée)
└── public_html/           ← racine web
    ├── index.php          ← version adaptée (voir ci-dessous)
    ├── .htaccess          ← copié de gestionscholaire/public/.htaccess
    └── build/ favicon.ico robots.txt   ← copiés de gestionscholaire/public/
```

1. Placez l'application dans `/home/UTILISATEUR/gestionscholaire` (hors `public_html`).
2. Copiez **le contenu** de `gestionscholaire/public/` dans `public_html/`
   (`.htaccess`, `favicon.ico`, `robots.txt`, dossier `build/`).
3. Remplacez `public_html/index.php` par
   [`cpanel-index.php.example`](cpanel-index.php.example) (adaptez le nom du dossier d'app).
4. Lien des fichiers uploadés (photos) — via **Terminal** cPanel :
   ```bash
   ln -s /home/UTILISATEUR/gestionscholaire/storage/app/public /home/UTILISATEUR/public_html/storage
   ```

➡️ Continuez avec les **« Étapes communes »**.

---

## 🧩 Étapes communes

### 1. PHP 8.3 + extensions
**cPanel → Select PHP Version (MultiPHP)** → choisir **8.3** →
cocher les extensions : `gd`, `zip`, `mbstring`, `pdo_mysql`, `fileinfo`, `bcmath`.

### 2. Base de données
**cPanel → MySQL Databases** :
- Créez une base (ex. `gestion`) → nom réel = `UTILISATEUR_gestion`
- Créez un utilisateur + mot de passe
- **Ajoutez l'utilisateur à la base** avec **ALL PRIVILEGES**

### 3. Récupérer le code
**cPanel → Git™ Version Control → Create** :
- Clone URL : `https://github.com/sambawade2-stack/test.git`
- Répertoire : `/home/UTILISATEUR/gestionscholaire`

(ou bien : uploader un ZIP via le **Gestionnaire de fichiers** et l'extraire.)

### 4. Dépendances (via **Terminal** cPanel)
```bash
cd ~/gestionscholaire
composer install --no-dev --optimize-autoloader
```
> Pas de Terminal/SSH sur votre offre ? Lancez `composer install` **en local**
> puis uploadez le dossier `vendor/` sur le serveur.

### 5. Assets compilés (Vite)
Les hébergements mutualisés n'ont pas Node.js. **Compilez en local** :
```bash
npm run build        # génère public/build/
```
…puis uploadez `public/build/` vers le serveur (dans `public_html/build` en Option 2,
ou `gestionscholaire/public/build` en Option 1).

### 6. Fichier `.env`
Copiez `.env.example` en `.env` (Gestionnaire de fichiers), puis renseignez :
```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=UTILISATEUR_gestion
DB_USERNAME=UTILISATEUR_dbuser
DB_PASSWORD=motdepasse_bdd

SCHOOL_NAME="Lycée Technique"
SCHOOL_CODE="LT"
```
Puis générez la clé (Terminal) :
```bash
php artisan key:generate
```

### 7. Migrations + lien de stockage + caches (Terminal)
```bash
cd ~/gestionscholaire
php artisan migrate --force
php artisan storage:link        # (Option 1 ; en Option 2, voir le lien manuel plus haut)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Compte administrateur (Terminal)
```bash
php artisan tinker --execute="App\Models\User::create([
  'name'=>'Administrateur','email'=>'admin@ecole.sn',
  'password'=>bcrypt('password'),'role'=>'admin','is_active'=>true]);"
```

### 9. Permissions
**Gestionnaire de fichiers** (ou Terminal) — les dossiers suivants doivent être
inscriptibles (755) :
```bash
chmod -R 755 ~/gestionscholaire/storage ~/gestionscholaire/bootstrap/cache
```

### 10. HTTPS
**cPanel → SSL/TLS Status → Run AutoSSL** (Let's Encrypt gratuit).
Le HTTPS est requis pour la **caméra** (scan badge / capture photo).

### 11. Tâche planifiée (impayés)
**cPanel → Cron Jobs** → ajouter (toutes les minutes) :
```
/usr/local/bin/php /home/UTILISATEUR/gestionscholaire/artisan schedule:run >/dev/null 2>&1
```

---

## 🔎 Vérification finale

Ces URL doivent renvoyer **403/404** (jamais leur contenu) :
```
https://votre-domaine.com/.env
https://votre-domaine.com/composer.json
```
Si `.env` s'affiche → le Document Root pointe au mauvais endroit (revoyez l'option choisie).

L'application doit s'ouvrir sur la page de connexion :
```
https://votre-domaine.com         →  admin@ecole.sn / password
```
> Changez le mot de passe admin après la première connexion.
