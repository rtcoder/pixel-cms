cp .env.example .env
composer install
chmod 777 storage bootstrap/cache
php artisan storage:link
php artisan key:generate
php artisan migrate -vvv
php artisan setup-passport
