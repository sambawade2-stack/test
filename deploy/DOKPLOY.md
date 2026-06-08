# 🐳 Déploiement sur Dokploy

Dokploy déploie l'application via Docker à partir du dépôt GitHub. Le HTTPS,
le domaine et la base de données sont gérés pour vous. **Aucun souci de version
PHP** (l'image fige PHP 8.3) ni de Document Root (Nginx sert déjà `public/`).

---

## 1. Créer l'application dans Dokploy

1. Dokploy → **Create Application** → type **Docker Compose** (Compose Type: `docker-compose.yml`)
2. **Source** : Git → dépôt `https://github.com/sambawade2-stack/test.git`, branche `main`
3. **Compose Path** : `docker-compose.yml` (à la racine)

> Dokploy lira le `Dockerfile` (construction de l'app) et le `docker-compose.yml`
> (app + base MySQL + volumes).

## 2. Variables d'environnement

Dans l'onglet **Environment** de l'application, collez ceci (adaptez les valeurs) :

```dotenv
APP_NAME=Gestion Scolaire
APP_KEY=base64:REMPLACEZ_PAR_VOTRE_CLE
APP_URL=https://demo.thioubalotech.com

DB_DATABASE=gestion_scholaire
DB_USERNAME=gestion_user
DB_PASSWORD=UnMotDePasseSolide
DB_ROOT_PASSWORD=UnAutreMotDePasseRoot

SCHOOL_NAME=Lycée Technique
SCHOOL_CODE=LT
SCHOOL_PHONE=+221 33 800 00 00
SCHOOL_ADDRESS=Dakar, Sénégal
SCHOOL_EMAIL=direction@ecole.sn
```

### 🔑 Générer une APP_KEY
Indispensable, sinon l'app renvoie une erreur. Générez-la avec :
```bash
echo "base64:$(openssl rand -base64 32)"
```
…et collez le résultat dans `APP_KEY`.

## 3. Domaine + HTTPS

Onglet **Domains** :
- Host : `demo.thioubalotech.com`
- Service / Port : **app** : **80**
- **HTTPS : activé** (Let's Encrypt automatique) ✅

> Le HTTPS rend la **caméra** (scan badge / capture photo) fonctionnelle partout.

## 4. Déployer

Cliquez sur **Deploy**. Au premier démarrage, le conteneur exécute
automatiquement (voir `docker/entrypoint.sh`) :
- attente de la base MySQL,
- `php artisan migrate --force` (création des tables),
- `storage:link`,
- mise en cache config/routes/vues.

## 5. Créer le compte administrateur

Une fois déployé, ouvrez le **Terminal** du conteneur dans Dokploy
(ou « Run Command ») et lancez :

```bash
php artisan tinker --execute="App\Models\User::create(['name'=>'Administrateur','email'=>'admin@ecole.sn','password'=>bcrypt('password'),'role'=>'admin','is_active'=>true]);"
```

| Identifiant | Valeur |
|---|---|
| URL | https://demo.thioubalotech.com |
| Email | `admin@ecole.sn` |
| Mot de passe | `password` |

> Changez le mot de passe après la première connexion.

---

## 🔁 Mises à jour

À chaque évolution :
```bash
# Sur votre PC
npm run build
git add -A && git commit -m "..." && git push
```
Puis dans Dokploy → **Deploy** (ou activez l'**Auto Deploy** sur push pour
que ce soit automatique).

---

## 🗂️ Persistance des données

| Donnée | Volume | Conservée au redéploiement |
|---|---|---|
| Base de données | `db_data` | ✅ |
| Photos uploadées | `storage_uploads` | ✅ |

---

## 🔎 Vérifications

- `https://demo.thioubalotech.com` → page de connexion stylée
- `https://demo.thioubalotech.com/.env` → **404** (jamais le contenu) 🔒
- Logs : onglet **Logs** de Dokploy (sortie Nginx + PHP-FPM + migrations)
