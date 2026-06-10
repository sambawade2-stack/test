# 🐳 Déploiement sur Dokploy

Dokploy déploie l'application via Docker à partir du dépôt GitHub. Le HTTPS,
le domaine et la base de données sont gérés pour vous. **Aucun souci de version
PHP** (l'image fige PHP 8.3) ni de Document Root (Nginx sert déjà `public/`).

> ## ⚠️ À LIRE EN PREMIER — n'utilisez PAS « Application » (Nixpacks)
>
> Si vous créez une **Application**, Dokploy utilise **Nixpacks** (auto-build) qui
> lance son propre `npm run build` et **échoue** :
> ```
> process "/bin/bash -ol pipefail -c npm run build" did not complete successfully: exit code 1
> ❌ Nixpacks build failed
> ```
> **Ce projet n'utilise PAS Nixpacks.** Il faut créer un service de type
> **Compose** (qui utilise notre `docker-compose.yml` + `Dockerfile`).
> → Voir l'étape 1 ci-dessous.

---

## 1. Créer le service — type **Compose** (PAS « Application »)

1. Dokploy → dans votre projet → **Create Service** → **Compose**
   *(intitulé selon version : « Compose », « Docker Compose ».
   ⚠️ surtout pas « Application » qui déclenche Nixpacks.)*
2. **Provider / Source** : Git → `https://github.com/sambawade2-stack/test.git`, branche `main`
3. **Compose Path** : `docker-compose.yml`
4. **Build Type** : laissez sur **Docker Compose** (il lit notre Dockerfile, pas Nixpacks)

> Dokploy lira le `docker-compose.yml` → construit l'app via notre `Dockerfile`
> + démarre MySQL + volumes persistants. Tout est inclus, rien d'autre à créer.

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

---

## 🛠️ Dépannage

### ❌ `Nixpacks build failed` / `npm run build ... exit code 1`
Vous avez créé une **Application** (build Nixpacks). Supprimez-la et recréez un
service de type **Compose** (étape 1). Notre image gère elle-même la compilation
des assets — Nixpacks ne doit pas intervenir.

### ❌ `No such container: select-a-container`
Le build a échoué → aucun conteneur n'existe encore. Ce message disparaît une fois
le déploiement (Compose) réussi. Corrigez d'abord le build (point ci-dessus).

### ❌ La page reste en erreur après déploiement
1. **APP_KEY vide** → renseignez-la (étape 2).
2. **Migrations** : ouvrez les **Logs**, vérifiez les lignes `migrate ... DONE`.
   Si « Connection refused », la base met du temps à démarrer — l'entrypoint
   réessaie automatiquement (jusqu'à 5 fois).
3. **APP_URL** doit correspondre exactement au domaine (https, sans `/` final).

### 🔐 Recommandé en production
Ajoutez ces variables (HTTPS uniquement) :
```dotenv
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
```
