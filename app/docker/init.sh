#!/bin/bash

# Копирование конфигов для nginx
cp -r /kingdom/app/docker/nginx /etc
ln -s /etc/nginx/sites-available/kingdom.conf /etc/nginx/sites-enabled/

# Изменение прав на директории app/cache и app/logs
rm -rf /kingdom/app/cache/* /kingdom/app/logs/*
chown -R :www-data /kingdom/app/cache /kingdom/app/logs
chmod -R 775 /kingdom/app/cache /kingdom/app/logs
chown -R :www-data /kingdom/web

# Инициализация серверов при запуске контейнера
/etc/init.d/php5-fpm start
/etc/init.d/nginx start
/etc/init.d/mysql start
/etc/init.d/redis-server start

# Создание юзера для MySQL
echo "CREATE USER 'kingdom'@'localhost';" | mysql
echo "GRANT ALL PRIVILEGES ON * . * TO 'kingdom'@'localhost';" | mysql
echo "FLUSH PRIVILEGES;" | mysql

# Создание БД, при ее отсутствии
/kingdom/app/console doctrine:database:create > /dev/null 2>&1

# Обновление структуры БД
/kingdom/app/console doctrine:schema:update --force

# Обновление библиотек композера
/composer.phar install -n

# Обновление библиотек nmp ...
npm install

# Симфони-команды
# Загрузка игровых данных в БД
/kingdom/app/console kingdom:map:create
/kingdom/app/console kingdom:items:create

# Запуск node.js приложений
cd /kingdom/websocket
(node router.js &) && node gate.js
