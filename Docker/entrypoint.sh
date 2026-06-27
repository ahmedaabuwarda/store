#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-progress --no-interaction
fi

if [ ! -f ".env" ]; then
    echo "Creating env file for env $APP_ENV"
    cp .env.example .env
else
    echo "env file exists."
fi

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
until php -r "
  \$env = parse_ini_file('.env');
  \$pass = trim(\$env['DB_PASSWORD'] ?? 'root', \"'\\\" \");
  new PDO('mysql:host=database;port=3306', 'root', \$pass);
" 2>/dev/null; do
  echo "MySQL is not ready yet. Retrying in 3s..."
  sleep 3
done
echo "MySQL is ready!"

php artisan optimize:clear
php artisan migrate --seed
php artisan serve --port=$PORT --host=0.0.0.0 --env=.env
exec docker-php-entrypoint "$@"
