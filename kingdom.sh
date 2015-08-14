#!/bin/sh

command -v docker >/dev/null 2>&1 || { echo "Докер не установлен. Установка: http://docs.docker.com/linux/started"; exit 1; }
command -v curl >/dev/null 2>&1 || { echo "Curl не установлен. Установка: sudo apt-get install curl"; exit 1; }

case $1 in
'')
    $0 help
;;

'download')
    # Проверка соединения с сетью
    echo -e "GET http://google.com HTTP/1.0\n\n" | nc google.com 80 > /dev/null 2>&1

    if [ $? -eq 0 ]; then
        echo "Обновление образа из Docker hub ..."
        docker pull rottenwood/kingdom
    fi
;;

'build')
    $0 stop
    echo "Сборка Docker-образа ..."
    docker rmi rottenwood/kingdom > /dev/null 2>&1
    docker build --no-cache -t rottenwood/kingdom .
;;

(start|restart)
    $0 stop
    $0 download
    echo "Настройка контейнера данных для MySQL ..."
    docker create --name kingdom-mysql-data rottenwood/mysql-data > /dev/null 2>&1

    echo "Запуск контейнера с сервером MySQL ..."
    docker run -d --name kingdom-mysql-server \
        -p 3306:3306 \
        --volumes-from=kingdom-mysql-data \
        -e MYSQL_PASS="docker" \
        -e MYSQL_USER="kingdom" \
        tutum/mysql

    SYMFONY_ENVIRONMENT="prod"
    if [ ! -z $2 ]; then
        if [ $2 = "dev" ]; then
            SYMFONY_ENVIRONMENT="dev"
        fi
    fi

    echo "Выбрано symfony окружение: \033[1;31m$SYMFONY_ENVIRONMENT\033[0m"

    echo "Создание нового контейнера ..."
    docker run -d --name="kingdom" \
        -v $(pwd):/kingdom -p 7777:7777 -p 81:81 \
        --entrypoint="kingdom/app/docker/init.sh" \
        --link kingdom-mysql-server:mysql \
        -e SYMFONY_ENVIRONMENT="$SYMFONY_ENVIRONMENT" \
        rottenwood/kingdom

    SERVER_URL="$(docker inspect --format='{{.NetworkSettings.IPAddress}}' kingdom)"

    if [ -z ${SERVER_URL} ]; then
        echo "\033[1;31mКонтейнер не был создан!\033[0m"
        exit 1
    fi

    echo "Контейнер создан!"
    echo "Игра доступна по адресу: \033[1;33;24mhttp://$SERVER_URL:81\033[0m"
    echo "Если контейнер запускается впервые, системе понадобится время для установки и настройки."
    echo "Это может занять около минуты. Детальная информация в логах: \033[5;33;24m$0 log\033[0m"
;;

'stop')
    echo "Удаление старого контейнера ..."
    docker kill kingdom > /dev/null 2>&1
    docker rm kingdom > /dev/null 2>&1
    docker kill kingdom-mysql-server > /dev/null 2>&1
    docker rm kingdom-mysql-server > /dev/null 2>&1
    echo "Контейнер остановлен и удален!"
;;

'bash')
    docker exec -it kingdom bash
;;

'update')
    $0 console doc:sch:upd --force
;;

'mysql')
    docker exec -it kingdom-mysql-server mysql
;;

'console')
    shift
    docker exec -it kingdom /kingdom/app/console "$@"
;;

(gulp|css)
    command -v node >/dev/null 2>&1 || { echo "Node.js не установлен"; exit 1; }
    node node_modules/gulp/bin/gulp.js build
;;

'log')
    case $2 in
    '')
        docker logs kingdom
    ;;

    'nginx')
        docker exec -it kingdom tail -100 /var/log/nginx/kingdom_error.log
    ;;
    esac
;;

'help')
	echo "--------------------------------------------------------------------"
	echo "\033[1;33;24m$0 start\033[0m - запуск контейнера с окружением"
	echo "\033[1;33;24m$0 start dev\033[0m - запуск контейнера с dev-окружением (без кэширования)"
	echo "\033[1;33;24m$0 restart\033[0m [dev]- перезапуск контейнера"
	echo "\033[1;33;24m$0 stop\033[0m - остановка контейнера\n"

	echo "\033[1;33;24m$0 log\033[0m [nginx]- просмотр логов"
	echo "\033[1;33;24m$0 bash\033[0m - Запуск серверной консоли"
	echo "\033[1;33;24m$0 mysql\033[0m - Запуск консоли MySQL"
	echo "\033[1;33;24m$0 update\033[0m - Обновление структуры базы данных"
	echo "\033[1;33;24m$0 console\033[0m - Консоль Symfony\n"

	echo "\033[1;33;24m$0 css\033[0m - Сборка CSS и JS с помощью gulp"
	echo "\033[1;33;24m$0 gulp\033[0m - Сборка CSS и JS с помощью gulp\n"

	echo "\033[1;33;24m$0 build\033[0m - сборка нового образа"
	echo "----------------------------------------------------------------------"
;;

*)
    $0 help
;;
esac

