#!/bin/sh

echo "Проверка установленных приложений ..."
command -v docker >/dev/null 2>&1 || { echo "Докер не установлен. Установка: http://docs.docker.com/linux/started"; exit 1; }
command -v curl >/dev/null 2>&1 || { echo "Curl не установлен. Установка: sudo apt-get install curl"; exit 1; }

echo "Сборка Docker-образа ..."
docker stop kingdom > /dev/null 2>&1
docker rm kingdom > /dev/null 2>&1
docker rmi rottenwood/kingdom > /dev/null 2>&1
docker build --no-cache -t rottenwood/kingdom .
