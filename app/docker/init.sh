#!/bin/bash

# Копирование конфигов для nginx
cp -r /kingdom/app/docker/nginx /etc
ln -s /etc/nginx/sites-available/kingdom.conf /etc/nginx/sites-enabled/

# Инициализация серверов при запуске контейнера
/etc/init.d/nginx start
/etc/init.d/mysql start
/etc/init.d/redis-server start
(node /kingdom/websocket/router.js &) && node /kingdom/websocket/gate.js

# Изменение прав на директории app/cache и app/logs
chown -R :www-data app/cache app/logs
