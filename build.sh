#!/bin/sh

echo "Проверка установленных приложений ..."
command -v docker >/dev/null 2>&1 || { echo "Докер не установлен. Установка: http://docs.docker.com/linux/started"; exit 1; }
command -v curl >/dev/null 2>&1 || { echo "Curl не установлен. Установка: sudo apt-get install curl"; exit 1; }
command -v ./composer.phar >/dev/null 2>&1 || { echo "Composer не установлен. Установка ..."; curl -sS https://getcomposer.org/installer | php; }
command -v npm >/dev/null 2>&1 || { echo "NPM не установлен. Установка: sudo apt-get install npm"; exit 1; }

echo "Создание БД, при ее отсутствии ..."
app/console doctrine:database:create > /dev/null 2>&1
echo "Обновление структуры БД ..."
app/console doctrine:schema:update --force
echo "Обновление библиотек композера ..."
./composer.phar install
echo "Обновление библиотек nmp ..."
npm install


echo "Сборка Docker-образа ..."
docker stop kingdom > /dev/null 2>&1
docker rm kingdom > /dev/null 2>&1
docker rmi rottenwood/kingdom > /dev/null 2>&1
docker build --no-cache -t rottenwood/kingdom .
