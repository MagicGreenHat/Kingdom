#!/bin/bash

echo "Настройка для доступа www-data к внешним файлам ..."
usermod -u 1000 www-data

echo "Копирование конфигов для nginx ..."
cp -r /kingdom/app/docker/nginx /etc

echo "Подключение страницы с информацией о загрузке ..."
ln -s /etc/nginx/sites-available/maintain.conf /etc/nginx/sites-enabled/

echo "Запуск nginx ..."
/etc/init.d/nginx start

echo "Обновление библиотек композера ..."
[ -d /kingdom/vendor ] || mkdir /kingdom/vendor
chown -R www-data:www-data /kingdom/vendor
#sudo -u www-data /composer.phar install -n -d /kingdom/
sudo /composer.phar install -n -d /kingdom/

echo "Очистка кэша ..."
rm -rf /kingdom/app/cache/dev /kingdom/app/cache/prod /kingdom/app/logs/dev.log /kingdom/app/logs/prod.log

echo "Создание БД, при ее отсутствии ..."
sudo -u www-data /kingdom/app/console doctrine:database:create > /dev/null 2>&1

echo "Обновление структуры БД ..."
sudo -u www-data /kingdom/app/console doctrine:schema:update --force

echo "Загрузка игровых данных в БД ..."
sudo -u www-data /kingdom/app/console kingdom:create:map

echo "Инициализация серверов ..."
/etc/init.d/php5-fpm start
/etc/init.d/redis-server start
/etc/init.d/nginx start

echo "Установка npm пакетов ..."
cd /kingdom
npm install

echo "Сборка CSS и JS ассетов ..."
node node_modules/gulp/bin/gulp.js build

echo "Очистка кэша ..."
rm -rf /kingdom/app/cache/dev /kingdom/app/cache/prod /kingdom/app/logs/dev.log /kingdom/app/logs/prod.log

if [ ${SYMFONY_ENVIRONMENT} = "prod" ]; then
    /kingdom/app/console cache:warm -e prod
elif [ ${SYMFONY_ENVIRONMENT} = "dev" ]; then
    /kingdom/app/console kingdom:create:user test test test@test.ru
    /kingdom/app/console kingdom:create:items
fi

echo "Настройка прав на логи"
chown -R www-data /kingdom/app/logs

echo "Очистка кэша ..."
rm -rf /kingdom/app/cache/dev /kingdom/app/cache/prod /kingdom/app/logs/dev.log /kingdom/app/logs/prod.log

echo "Конфигурация Symfony-окружения: $SYMFONY_ENVIRONMENT ..."
rm /kingdom/web/app_dev.php

if [ ${SYMFONY_ENVIRONMENT} = "dev" ]; then
    ln -s /etc/nginx/sites-available/kingdom-dev.conf /etc/nginx/sites-enabled/
    cp /kingdom/app/docker/symfony/app_dev.php /kingdom/web/
    mv /etc/nginx/nginx-dev.conf /etc/nginx/nginx.conf
else
    ln -s /etc/nginx/sites-available/kingdom.conf /etc/nginx/sites-enabled/
    rm /etc/nginx/nginx-dev.conf
fi

echo "Отключение страницы с информацией о загрузке ..."
rm /etc/nginx/sites-enabled/maintain.conf

echo "Рестарт nginx ..."
/etc/init.d/nginx restart

echo "Запуск node.js приложений ..."
cd /kingdom/websocket
node router.js &
node ticker.js &
node gate.js
