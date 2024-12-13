# Configurar permissões
chmod -R 777 .docker/local/
chmod -R 777 storage/
chmod -R 777 storage/logs/
chmod -R 777 storage/framework/
chmod -R 777 bootstrap/cache/

# Instalação das dependências
composer install
composer dump-autoload --optimize
composer clear-cache
npm install

# Comandos artisan
php artisan optimize:clear
find public -type l -delete # caso haja algum symlink será removido
php artisan storage:link # refazendo o symlink
php artisan migrate

# Iniciar o Nginx
nginx

# Iniciar o PHP-FPM é 
php-fpm

# Realiza o build do NPM
npm run build