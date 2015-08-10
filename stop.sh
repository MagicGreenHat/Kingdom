#!/bin/sh

echo "Удаление старого контейнера ..."
docker stop kingdom > /dev/null 2>&1
docker rm kingdom > /dev/null 2>&1
echo "Контейнер остановлен и удален!"
