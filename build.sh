#!/bin/sh

command -v docker >/dev/null 2>&1 || { echo "Докер не установлен. Установка: http://docs.docker.com/linux/started"; exit 1; }

docker stop kingdom > /dev/null 2>&1
docker rm kingdom > /dev/null 2>&1
docker rmi rottenwood/kingdom > /dev/null 2>&1
docker build --no-cache -t rottenwood/kingdom .
