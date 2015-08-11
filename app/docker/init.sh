#!/bin/bash

# Скрипт инициализации серверов при запуске контейнера

#/etc/init.d/apache2 start
/etc/init.d/mysql start
/etc/init.d/redis-server start
/kingdom/app/console server:run &
(node /kingdom/websocket/router.js &) && node /kingdom/websocket/gate.js
