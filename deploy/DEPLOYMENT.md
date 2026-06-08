# 🚀 Guide de déploiement — Production

## 🔒 Règle de sécurité fondamentale

> **Le domaine doit pointer vers le dossier `public/` du projet — JAMAIS vers la racine du projet.**

```
/var/www/gestionscholaire/        ← racine du PROJET (jamais exposée au web)
├── .env                          ← secrets (mot de passe BDD, clés) — PROTÉGÉ
├── app/  config/  storage/ ...   ← code et données — PROTÉGÉS
└── public/                       ← 👈 RACINE DU SITE (document root du domaine)
    └── index.php                 ← seul point d'entrée web
```

Si le domaine pointait sur la racine du projet, n'importe qui pourrait accéder à
`https://votre-domaine.com/.env` et lire le mot de passe de la base de données.
En pointant sur `public/`, ces fichiers sont physiquement hors de portée du web.

---

## 🖥️ Selon votre hébergement

### A. Serveur VPS / dédié avec **Nginx**
Utilisez [`nginx.conf.example`](nginx.conf.example) — la directive clé :
```nginx
root /var/www/gestionscholaire/public;   # ← /public
```

### B. Serveur VPS / dédié avec **Apache**
Utilisez [`apache-vhost.conf.example`](apache-vhost.conf.example) — la directive clé :
```apache
DocumentRoot /var/www/gestionscholaire/public   # ← /public
```
Activez le module rewrite : `a2enmod rewrite`

### C. Hébergement **cPanel** 👉 guide dédié : **[`CPANEL.md`](CPANEL.md)**

Résumé des 2 options :
1. **Recommandé** — cPanel → Domaines → **Document Root** = `gestionscholaire/public`
2. **Domaine principal verrouillé** — app hors `public_html` + `index.php` adapté
   ([`cpanel-index.php.example`](cpanel-index.php.example))

Le guide [`CPANEL.md`](CPANEL.md) couvre tout : base de données, PHP 8.3,
Git, Composer, assets, `.env`, migrations, HTTPS (AutoSSL) et cron.

---

## ✅ Checklist de mise en production

```bash
# Dépendances optimisées (sans paquets de dev)
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Environnement de production
# .env :  APP_ENV=production   APP_DEBUG=false
php artisan key:generate          # si pas encore fait
php artisan migrate --force
php artisan storage:link

# Mise en cache (performances)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Permissions des dossiers (Linux)
```bash
sudo chown -R www-data:www-data /var/www/gestionscholaire
sudo chmod -R 775 storage bootstrap/cache
```

### Tâche planifiée (impayés)
```bash
# crontab -e  (utilisateur www-data)
* * * * * cd /var/www/gestionscholaire && php artisan schedule:run >> /dev/null 2>&1
```

### HTTPS (obligatoire en production)
```bash
sudo certbot --nginx   -d votre-domaine.com   # Nginx
sudo certbot --apache  -d votre-domaine.com   # Apache
```
> Le HTTPS est aussi requis pour que la **caméra** (scan badge / capture photo)
> fonctionne sur les postes distants.

---

## 🔎 Vérifier que la protection fonctionne

Après déploiement, ces URL doivent renvoyer **403** ou **404** (jamais le contenu) :

```
https://votre-domaine.com/.env
https://votre-domaine.com/composer.json
https://votre-domaine.com/storage/logs/laravel.log
```

Si l'une d'elles affiche du contenu, le Document Root pointe au mauvais endroit.
