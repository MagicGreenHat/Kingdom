#!/bin/sh

echo "Удаление старого контейнера ..."
docker stop kingdom > /dev/null 2>&1
docker rm kingdom > /dev/null 2>&1
echo "Создание нового контейнера ..."
docker run -d --name="kingdom" -v $(pwd):/kingdom rottenwood/kingdom kingdom/app/docker/init.sh
echo "Контейнер создан!"
