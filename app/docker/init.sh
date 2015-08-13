#!/bin/bash

echo "Настройка для доступа www-data к внешним файлам ..."
usermod -u 1000 www-data

echo "Копирование конфигов для nginx ..."
cp -r /kingdom/app/docker/nginx /etc
ln -s /etc/nginx/sites-available/kingdom.conf /etc/nginx/sites-enabled/

echo "Обновление библиотек композера ..."
[ -d /kingdom/vendor ] || mkdir /kingdom/vendor
chown www-data:www-data /kingdom/vendor
sudo -u www-data ~/composer.phar install -n -d /kingdom/

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

echo "Запуск node.js приложений ..."
cd /kingdom/websocket
(node router.js &) && node gate.js
