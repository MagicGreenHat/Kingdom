#!/bin/bash

echo "Настройка для доступа www-data к внешним файлам ..."
usermod -u 1000 www-data

echo "Копирование конфигов ..."
cp -r /kingdom/app/docker/etc /

echo "Подключение страницы с информацией о загрузке ..."
ln -s /etc/nginx/sites-available/maintain.conf /etc/nginx/sites-enabled/

echo "Запуск nginx ..."
/etc/init.d/nginx start

echo "Обновление библиотек композера ..."
sudo /composer.phar config -g github-oauth.github.com 0c8682fe7bcabe7618e8342f6dfbc6bb1e0da05d
sudo /composer.phar install --prefer-dist -o -n -d /kingdom/
sudo chown -R www-data:www-data /kingdom/vendor /kingdom/bin /kingdom/app/cache /kingdom/app/logs

echo "Очистка кэша ..."
rm -rf /kingdom/app/cache/dev /kingdom/app/cache/prod /kingdom/app/logs/dev.log /kingdom/app/logs/prod.log

echo "Создание БД, при ее отсутствии ..."
sudo -u www-data /kingdom/app/console doctrine:database:create -e $SYMFONY_ENVIRONMENT > /dev/null 2>&1

echo "Обновление структуры БД ..."
sudo -u www-data /kingdom/app/console doctrine:schema:update -e $SYMFONY_ENVIRONMENT --force

echo "Загрузка игровых данных в БД ..."
sudo -u www-data /kingdom/app/console kingdom:create:map -e $SYMFONY_ENVIRONMENT

echo "Инициализация серверов ..."
/etc/init.d/php7.0-fpm start
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
else
    /kingdom/app/console kingdom:create:user test test test@test.ru -e $SYMFONY_ENVIRONMENT
    /kingdom/app/console kingdom:create:items -e $SYMFONY_ENVIRONMENT
fi

echo "Настройка прав ..."
echo "... на логи"
chown -R www-data /kingdom/app/logs
echo "... на сессии"
chown -R www-data /var/lib/php/sessions

echo "Очистка кэша ..."
rm -rf /kingdom/app/cache/dev /kingdom/app/cache/prod /kingdom/app/logs/dev.log /kingdom/app/logs/prod.log

echo "Конфигурация Symfony-окружения: $SYMFONY_ENVIRONMENT ..."
rm /kingdom/web/app_dev.php

if [ ${SYMFONY_ENVIRONMENT} = "prod" ] ; then
    ln -s /etc/nginx/sites-available/kingdom.conf /etc/nginx/sites-enabled/
    rm /etc/nginx/nginx-dev.conf
else
    ln -s /etc/nginx/sites-available/kingdom-dev.conf /etc/nginx/sites-enabled/
    cp /kingdom/app/docker/symfony/app_dev.php /kingdom/web/
    mv /etc/nginx/nginx-dev.conf /etc/nginx/nginx.conf

    # TODO: Включение модуля xdebug [sudo php5enmod xdebug]

    echo "Рестарт php-fpm ..."
    /etc/init.d/php7.0-fpm restart
fi

echo "Отключение страницы с информацией о загрузке ..."
rm /etc/nginx/sites-enabled/maintain.conf

echo "Рестарт nginx ..."
sudo service nginx restart

echo "Запуск node.js приложений ..."
cd /kingdom/websocket
node router.js &
node ticker.js &
node gate.js
