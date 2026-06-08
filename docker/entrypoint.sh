#!/bin/sh
set -e

cd /var/www/html

if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY non définie ! Définissez-la dans les variables d'environnement Dokploy."
fi

# ─── Attente de la base de données (jusqu'à ~120s) ───────────────────────────
echo "⏳ Attente de la base de données ($DB_HOST)..."
i=0
until php -r '
    $h=getenv("DB_HOST"); $p=getenv("DB_PORT")?:3306;
    $u=getenv("DB_USERNAME"); $w=getenv("DB_PASSWORD"); $d=getenv("DB_DATABASE");
    try { new PDO("mysql:host=$h;port=$p;dbname=$d", $u, $w, [PDO::ATTR_TIMEOUT=>2]); exit(0); }
    catch (Throwable $e) { exit(1); }
' 2>/dev/null; do
    i=$((i + 1))
    if [ "$i" -ge 60 ]; then
        echo "⚠️  Base toujours injoignable après 120s — on tente quand même."
        break
    fi
    sleep 2
done

# ─── Migrations (avec réessais : MySQL peut être lent au 1er démarrage) ───────
echo "🗄️  Migrations..."
n=0
until php artisan migrate --force --no-interaction; do
    n=$((n + 1))
    if [ "$n" -ge 5 ]; then
        echo "⚠️  migrate a échoué après 5 tentatives (vérifiez la connexion BDD)."
        break
    fi
    echo "↻  Nouvelle tentative de migration ($n/5)..."
    sleep 5
done

# ─── Lien storage + caches de production ─────────────────────────────────────
echo "🔗 Lien storage..."
php artisan storage:link --no-interaction 2>/dev/null || true

echo "⚡ Mise en cache (config, routes, vues)..."
php artisan config:cache --no-interaction || true
php artisan route:cache  --no-interaction || true
php artisan view:cache   --no-interaction || true

echo "🚀 Démarrage de Nginx + PHP-FPM..."
exec supervisord -c /etc/supervisord.conf
