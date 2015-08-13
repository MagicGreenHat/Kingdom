#!/bin/bash

# Настройка для доступа www-data к внешним файлам
usermod -u 1000 www-data

# Копирование конфигов для nginx
cp -r /kingdom/app/docker/nginx /etc
ln -s /etc/nginx/sites-available/kingdom.conf /etc/nginx/sites-enabled/

# Обновление библиотек композера
[ -d /kingdom/vendor ] || mkdir /kingdom/vendor
chown www-data:www-data /kingdom/vendor
sudo -u www-data ~/composer.phar install -n -d /kingdom/

# Создание БД, при ее отсутствии
/kingdom/app/console doctrine:database:create > /dev/null 2>&1

# Обновление структуры БД
/kingdom/app/console doctrine:schema:update --force

# Загрузка игровых данных в БД
/kingdom/app/console kingdom:map:create
/kingdom/app/console kingdom:items:create

# Инициализация серверов
/etc/init.d/php5-fpm start
/etc/init.d/redis-server start
/etc/init.d/nginx start

# Запуск node.js приложений
cd /kingdom/websocket
(node router.js &) && node gate.js
