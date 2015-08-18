#!/bin/bash

echo "Настройка для доступа www-data к внешним файлам ..."
usermod -u 1000 www-data

echo "Копирование конфигов для nginx ..."
cp -r /kingdom/app/docker/nginx /etc

echo "Конфигурация Symfony-окружения: $SYMFONY_ENVIRONMENT ..."

if [ ${SYMFONY_ENVIRONMENT} = "dev" ]; then
    ln -s /etc/nginx/sites-available/kingdom-dev.conf /etc/nginx/sites-enabled/
    rm /kingdom/web/app_dev.php
    cp /kingdom/app/docker/symfony/app_dev.php /kingdom/web/
    mv /etc/nginx/nginx-dev.conf /etc/nginx/nginx.conf
else
    rm /kingdom/web/app_dev.php
    ln -s /etc/nginx/sites-available/kingdom.conf /etc/nginx/sites-enabled/
    rm /etc/nginx/nginx-dev.conf
fi

echo "Обновление библиотек композера ..."
[ -d /kingdom/vendor ] || mkdir /kingdom/vendor
sudo -u www-data /composer.phar install -n -d /kingdom/

echo "Создание БД, при ее отсутствии ..."
/kingdom/app/console doctrine:database:create > /dev/null 2>&1

echo "Обновление структуры БД ..."
/kingdom/app/console doctrine:schema:update --force

echo "Загрузка игровых данных в БД ..."
/kingdom/app/console kingdom:map:create
/kingdom/app/console kingdom:items:create

echo "Инициализация серверов ..."
/etc/init.d/php5-fpm start
/etc/init.d/redis-server start
/etc/init.d/nginx start

echo "Сборка CSS и JS ассетов ..."
cd /kingdom && node node_modules/gulp/bin/gulp.js build

echo "Очистка кэша ..."
rm -rf /kingdom/app/cache/dev /kingdom/app/cache/prod /kingdom/app/logs/dev.log /kingdom/app/logs/prod.log

if [ ${SYMFONY_ENVIRONMENT} = "prod" ]; then
    sudo -u www-data /kingdom/app/console cache:warm -e prod
fi

echo "Запуск node.js приложений ..."
cd /kingdom/websocket
(node router.js &) && node gate.js
