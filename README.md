# 🎓 Gestion Scolaire

Application web de gestion d'un établissement scolaire (collège / lycée), de la 6ème à la Terminale.
Développée avec **Laravel 12**.

---

## ✨ Fonctionnalités

### Élèves & classes
- Gestion des élèves (CRUD) avec **photo** (upload ou capture webcam)
- **Matricule professionnel** auto-généré : `LT-2026-0001` (code école · année · séquence)
- **Badge scolaire avec QR code** (prévisualisation + impression PDF, individuel ou par classe)
- Gestion des classes par cycle (collège / lycée) et par année scolaire

### Paiements
- Frais d'**inscription** et **mensualités** (avec paiement par tranche / soldes)
- **Reçu PDF** automatique pour chaque paiement
- Détection automatique des **impayés** (après le 10 de chaque mois)
- Complément de paiement (régler le solde restant)

### Personnel & paie
- Enseignants et personnel (administratif / appoint)
- Fiches de **paie mensuelle** + **historique PDF** par employé

### Présences & certificats
- **Feuille d'appel** par classe et par jour (présent / absent / retard / excusé)
- Certificat de **scolarité** et d'**assiduité** (PDF)

### Contrôle d'accès au badge
- **Scan du QR** pour vérifier si un élève est **en règle** (caméra ou saisie manuelle)
- **Journal des passages** (historique des scans)

### Pilotage
- **Tableau de bord** avec statistiques
- **Tableau de bord financier** (recettes mensuelles, graphiques)
- **Exports Excel & PDF** (rapport financier, paiements, élèves)

### Sécurité — 6 profils
`admin` · `directeur` · `comptable` · `surveillant` · `secrétaire` · `caissier`
Chaque profil a ses droits (contrôle d'accès côté serveur + menus adaptés).

---

## 🛠️ Stack technique

| Composant | Version |
|---|---|
| PHP | 8.3+ |
| Laravel | 12 |
| Base de données | MySQL 8 |
| Front | Blade + Tailwind CSS v4 (Vite) |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel |
| QR code | endroid/qr-code |
| Rôles | spatie/laravel-permission |

---

## 🚀 Installation

### Prérequis
- PHP 8.3+ avec extensions `gd`, `zip`, `pdo_mysql`
- Composer 2
- Node.js 18+ & npm
- MySQL 8

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/sambawade2-stack/test.git gestionscholaire
cd gestionscholaire

# 2. Dépendances PHP & JS
composer install
npm install

# 3. Configuration
cp .env.example .env
php artisan key:generate
```

Éditez ensuite le fichier `.env` :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_scholaire
DB_USERNAME=root
DB_PASSWORD=VOTRE_MOT_DE_PASSE

# Établissement (affiché sur badges, reçus, certificats)
SCHOOL_NAME="Lycée Technique"
SCHOOL_CODE="LT"
SCHOOL_PHONE="+221 33 800 00 00"
SCHOOL_ADDRESS="Dakar, Sénégal"
SCHOOL_EMAIL="direction@ecole.sn"
```

```bash
# 4. Base de données
mysql -u root -p -e "CREATE DATABASE gestion_scholaire CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate

# 5. Lien de stockage (photos, fichiers)
php artisan storage:link

# 6. Compiler les assets
npm run build

# 7. Lancer
php artisan serve
```

L'application est accessible sur **http://localhost:8000**.

### Créer le compte administrateur

```bash
php artisan tinker --execute="App\Models\User::create([
    'name' => 'Administrateur',
    'email' => 'admin@ecole.sn',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'is_active' => true,
]);"
```

| Identifiant | Valeur |
|---|---|
| Email | `admin@ecole.sn` |
| Mot de passe | `password` |

> ⚠️ Changez ce mot de passe après la première connexion.

---

## 🌐 Accès depuis d'autres machines (réseau local)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Les autres machines accèdent via `http://<IP-DE-LA-MACHINE>:8000`.

> ⚠️ **Caméra (scan badge / capture photo)** : les navigateurs exigent **HTTPS** ou `localhost`.
> Sur les autres machines en HTTP, la caméra est bloquée — la **saisie manuelle** et l'**import de fichier** restent disponibles.
> Pour la caméra partout, servez l'app en HTTPS (ex. `tailscale serve`, `mkcert`, reverse-proxy TLS).

---

## ⏰ Tâche planifiée (impayés)

La détection des impayés est planifiée le 11 de chaque mois. Activez le scheduler :

```bash
# crontab -e
* * * * * cd /chemin/vers/gestionscholaire && php artisan schedule:run >> /dev/null 2>&1
```

Détection manuelle :

```bash
php artisan school:detect-unpaid --notify
```

---

## 🚀 Déploiement en production

> 🔒 **Sécurité — point essentiel :** en production, le domaine doit pointer vers le
> dossier **`public/`**, **jamais** vers la racine du projet. Sinon `.env`
> (mot de passe de la base) deviendrait accessible publiquement via le web.

```
/var/www/gestionscholaire/     ← racine du PROJET (hors web)
├── .env  app/  config/  ...    ← PROTÉGÉS
└── public/                     ← 👈 document root du domaine
```

Configurations prêtes à l'emploi dans le dossier [`deploy/`](deploy/) :

| Hébergement | Fichier |
|---|---|
| **Dokploy / Docker** ⭐ recommandé | 👉 **[`deploy/DOKPLOY.md`](deploy/DOKPLOY.md)** (`Dockerfile` + `docker-compose.yml` inclus) |
| Nginx (VPS/dédié) | [`deploy/nginx.conf.example`](deploy/nginx.conf.example) |
| Apache (VPS/dédié) | [`deploy/apache-vhost.conf.example`](deploy/apache-vhost.conf.example) |
| cPanel (mutualisé) | [`deploy/CPANEL.md`](deploy/CPANEL.md) |

Avec **Dokploy/Docker**, l'image fige PHP 8.3, sert déjà `public/`, et le HTTPS
est automatique — aucun réglage de version PHP ni de Document Root.

👉 **Guide général : [`deploy/DEPLOYMENT.md`](deploy/DEPLOYMENT.md)** (checklist, HTTPS, permissions, vérification).

---

## 📁 Architecture

```
app/
├── Console/Commands/   # DetectUnpaidStudents
├── Exports/            # Exports Excel (paiements, élèves, finances)
├── Http/
│   ├── Controllers/    # Students, Payments, Payrolls, Scan, Finance...
│   ├── Middleware/     # CheckRole (contrôle d'accès par profil)
│   └── Requests/       # Validation des formulaires
├── Models/             # Eloquent (Student, Payment, Payroll, ScanLog...)
└── Services/           # PaymentService, PdfService, NotificationService
resources/views/        # Blade + Tailwind
database/migrations/    # Schéma complet
```

---

## 📄 Licence

Projet propriétaire. Tous droits réservés.
