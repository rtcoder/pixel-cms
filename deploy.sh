cp .env.example .env
chmod 777 storage bootstrap/cache
php artisan storage:link
php artisan key:generate
php artisan migrate
php artisan passport:install
php artisan passport:keys
