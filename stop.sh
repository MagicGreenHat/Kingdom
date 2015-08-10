#!/bin/sh

command -v docker >/dev/null 2>&1 || { echo "Докер не установлен. Установка: http://docs.docker.com/linux/started"; exit 1; }

echo "Удаление старого контейнера ..."
docker stop kingdom > /dev/null 2>&1
docker rm kingdom > /dev/null 2>&1
echo "Контейнер остановлен и удален!"
