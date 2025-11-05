#!/usr/bin/env bash
set -euo pipefail

# Ensure runtime-writable directories are owned by www-data and have safe perms
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
find /var/www/html/storage -type d -exec chmod 775 {} + || true
find /var/www/html/storage -type f -exec chmod 664 {} + || true
find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} + || true
find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} + || true

# Ensure Laravel expected storage subdirectories exist
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/app/private
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage || true
find /var/www/html/storage -type d -exec chmod 775 {} + || true
find /var/www/html/storage -type f -exec chmod 664 {} + || true

# Ensure bootstrap cache exists
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/bootstrap/cache || true
find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} + || true
find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} + || true

# If any additional runtime actions are required they can be placed here

# Exec the main process (php-fpm by default)
# Wait for DB (if using MySQL) - simple tcp check
if [ -n "${DB_HOST:-}" ] && [ "${DB_HOST}" != "" ] ; then
	echo "Waiting for DB host ${DB_HOST}:${DB_PORT:-3306} to be available..."
	for i in $(seq 1 30); do
		if php -r "\$s=@fsockopen('${DB_HOST}', ${DB_PORT:-3306}); if (\$s) { fclose(\$s); exit(0);} exit(1);"; then
			echo "DB available"
			break
		fi
		echo "DB not available yet...sleeping"
		sleep 1
	done
fi

# Optionally run migrations on startup
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
	echo "RUN_MIGRATIONS=true -> running migrations"
	# Run migrations (attempt as current user). If artisan is not writable or permissions
	# require www-data, migrations will still be attempted; ensure ownership afterwards.
	php artisan migrate --force || true
	chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
fi

exec "$@"
