#!/bin/sh

command -v docker >/dev/null 2>&1 || { echo "Докер не установлен. Установка: http://docs.docker.com/linux/started"; exit 1; }

echo "Удаление старого контейнера ..."
docker stop kingdom > /dev/null 2>&1
docker rm kingdom > /dev/null 2>&1
echo "Создание нового контейнера ..."
docker run -d --name="kingdom" --hostname="kingdom" -v $(pwd):/kingdom --entrypoint="kingdom/app/docker/init.sh" -p 7777:7777 -p 81:81 rottenwood/kingdom
echo "Контейнер создан!"
