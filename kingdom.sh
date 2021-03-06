#!/bin/bash

#### VARIABLES ####
DOCKER_IMAGE_NAME="rottenwood/kingdom:2.0.0"
###################

command -v curl >/dev/null 2>&1 || { echo "Curl не установлен. Установка: sudo apt-get install curl"; exit 1; }
command -v docker >/dev/null 2>&1 || { echo "Докер не установлен. Установка: curl -sSL https://get.docker.com/ | sh"; exit 1; }

case $1 in
'')
    $0 help
;;

'help')
	echo -e "--------------------------------------------------------------------"
	echo -e "\033[1;33;24m$0 start\033[0m [dev|test] - Запуск контейнера [в dev-окружении (без кэширования)]"
	echo -e "\033[1;33;24m$0 restart\033[0m [dev|test]- Немедленный перезапуск контейнеров"
	echo -e "\033[1;33;24m$0 stop\033[0m - Остановка контейнера"
	echo -e "\033[1;33;24m$0 reboot\033[0m [dev] - Отложенная перезагрузка (с оповещением игроков)"
	echo -e ""
	echo -e "\033[1;33;24m$0 log\033[0m [game|players|nginx]- просмотр логов"
	echo -e "\033[1;33;24m$0 bash\033[0m - Запуск серверной консоли"
	echo -e "\033[1;33;24m$0 mysql\033[0m - Запуск консоли MySQL"
	echo -e "\033[1;33;24m$0 update\033[0m - Обновление структуры базы данных"
	echo -e "\033[1;33;24m$0 console\033[0m - Консоль Symfony"
	echo -e "\033[1;33;24m$0 (cache|clear)\033[0m [warm] - Очистка кэша"
	echo -e "\033[1;33;24m$0 (css|js|gulp)\033[0m - Сборка CSS и JS с помощью gulp"
	echo -e ""
	echo -e "\033[1;33;24m$0 broadcast (phrase)\033[0m - Отправка сообщения всем игрокам онлайн"
	echo -e ""
	echo -e "\033[1;33;24m$0 deploy\033[0m (dev) - Деплой проекта (git pull, рестарт серверов)"
	echo -e "\033[1;33;24m$0 test\033[0m (d|v|-d|-v|debug)- Запуск автоматических тестов (с выводом)"
	echo -e "\033[1;33;24m$0 build\033[0m - Сборка нового Docker-образа"
	echo -e "\033[1;33;24m$0 drop-database\033[0m - Удаление всех данных из БД"
	echo -e "\033[1;33;24m$0 check\033[0m [update [package_name|--all]] - Проверка новых пакетов composer (vinkla/climb)"
	echo "----------------------------------------------------------------------"
;;

'check')
    if [[ $2 = "update" ]]; then
        echo "Обновление зависимостей composer ..."
        vendor/vinkla/climb/bin/climb update $3
    else
        echo "Проверка наличия новых версий вендорных библиотек composer ..."
        vendor/vinkla/climb/bin/climb
    fi
;;

'download')
    # Проверка соединения с сетью
    echo -e "GET http://google.com HTTP/1.0\n\n" | nc google.com 80 > /dev/null 2>&1

    if [ $? -eq 0 ]; then
        echo "Обновление образа из Docker hub ..."
        docker pull ${DOCKER_IMAGE_NAME}
    fi
;;

'build')
    $0 stop
    echo "Сборка Docker-образа ..."
    docker rmi ${DOCKER_IMAGE_NAME} > /dev/null 2>&1
    docker build --no-cache -t ${DOCKER_IMAGE_NAME} .

    if [ ! -z $2 ] && [ $2 = "push" ]; then
        docker push ${DOCKER_IMAGE_NAME}
    fi
;;

(start|restart)
    $0 stop
    $0 download
    echo "Настройка контейнера данных для MySQL ..."
    docker create --name kingdom-mysql-data rottenwood/mysql-data > /dev/null 2>&1

    DB_USER=$(cat app/config/parameters.yml | grep database_user | sed "s/.*database_user: //")
    DB_PASSWORD=$(cat app/config/parameters.yml | grep database_password | sed "s/.*database_password: //")

    if [ -z "$DB_USER" ] || [ -z "$DB_PASSWORD" ]; then
        echo -e "\033[1;31mКонфиг app/config/parameters.yml не найден! Установка дефолтных паролей для БД\033[0m"
        DB_USER="kingdom"
        DB_PASSWORD="docker"
    fi

    echo "Запуск контейнера с сервером MySQL ..."
    docker run -d --name kingdom-mysql-server \
        -p 3307:3306 \
        --volumes-from=kingdom-mysql-data \
        -v $(pwd)/app/sessions:/var/lib/php/sessions \
        -e MYSQL_USER=${DB_USER} \
        -e MYSQL_PASS=${DB_PASSWORD} \
        tutum/mysql

    sleep 5

    SYMFONY_ENVIRONMENT="prod"
    if [ ! -z $2 ]; then
        if [ $2 = "dev" ]; then
            SYMFONY_ENVIRONMENT="dev"
        elif [ $2 = "test" ]; then
            SYMFONY_ENVIRONMENT="test"
        fi
    fi

    echo -e "Выбрано symfony окружение: \033[1;31m$SYMFONY_ENVIRONMENT\033[0m"

    echo "Создание нового контейнера ..."
    docker run -d --name="kingdom" \
        -v $(pwd):/kingdom -p 7777:7777 -p 81:81 \
        --entrypoint="kingdom/app/docker/init.sh" \
        --link kingdom-mysql-server:mysql \
        -e SYMFONY_ENVIRONMENT="$SYMFONY_ENVIRONMENT" \
        -e TERM=xterm \
        ${DOCKER_IMAGE_NAME}

    SERVER_URL="$(ifconfig docker | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*')"

    if [ -z $(docker inspect --format='{{.NetworkSettings.IPAddress}}' kingdom) ]; then
        echo -e "\033[1;31mКонтейнер не был создан!\033[0m"
        exit 1
    fi

    echo "Контейнер создан!"
    echo -e "Игра доступна по адресу: \033[1;33;24mhttp://$SERVER_URL:81\033[0m"
    echo "Если контейнер запускается впервые, системе понадобится время для установки и настройки."
    echo -e "Это может занять около минуты. Детальная информация в логах: \033[5;33;24m$0 log\033[0m"
;;

'stop')
    echo "Удаление старого контейнера ..."
    docker stop kingdom > /dev/null 2>&1
    docker rm kingdom > /dev/null 2>&1
    docker stop kingdom-mysql-server > /dev/null 2>&1
    docker rm kingdom-mysql-server > /dev/null 2>&1
    echo "Контейнер остановлен и удален!"
;;

'bash')
    docker exec -it kingdom bash
;;

'deploy')
    $0 stop
    git pull
    $0 start $2
;;

'broadcast')
    if [ ! -z $2 ]; then
        shift
        docker exec -it kingdom node /kingdom/websocket/broadcast.js $@
    fi
;;

'reboot')
    echo "Оповещение игроков о перезагрузке"

    MINUTES_TO_REBOOT=10
    BROADCAST_MESSAGE="Перезагрузка через $MINUTES_TO_REBOOT минут!";
    $0 broadcast ${BROADCAST_MESSAGE}
    echo ${BROADCAST_MESSAGE}
    sleep $(echo "5 * 60" | bc)

    # Второе предупреждение
    MINUTES_TO_REBOOT=5
    BROADCAST_MESSAGE="Перезагрузка через $MINUTES_TO_REBOOT минут!";
    $0 broadcast ${BROADCAST_MESSAGE}
    echo ${BROADCAST_MESSAGE}
    sleep $(echo "4 * 60" | bc)

    # Последнее предупреждение
    MINUTES_TO_REBOOT=1
    BROADCAST_MESSAGE="Перезагрузка через $MINUTES_TO_REBOOT минуту!";
    $0 broadcast ${BROADCAST_MESSAGE}
    echo ${BROADCAST_MESSAGE}
    sleep $(echo "1 * 60" | bc)

    $0 restart $2
;;

'update')
    $0 console doc:sch:upd --force
;;

'mysql')
    docker exec -it kingdom-mysql-server mysql
;;

'drop-database')
    $0 stop
    echo "Удаление всех данных из БД ..."
    docker rm kingdom-mysql-data >/dev/null 2>&1
;;

'console')
    shift
    docker exec -it kingdom sudo -u www-data /kingdom/app/console "$@"
;;

'test')
    VERBOSE=""
    TYPE=""

    case $2 in
    (-a|a)
        TYPE="acceptance"
    ;;
    (-f|f)
        TYPE="functional"
    ;;
    (-u|u)
        TYPE="unit"
    ;;
    (-v|v|-d|d|debug)
        VERBOSE="--debug"
    ;;
    esac

    case $3 in
    (-v|v|-d|d|debug)
        VERBOSE="--debug"
    ;;
    esac

    docker exec -it kingdom /kingdom/vendor/codeception/codeception/codecept run ${TYPE} \
        -c /kingdom/codeception.yml ${VERBOSE}
;;

(gulp|css|js)
    command -v node >/dev/null 2>&1 || { echo "Node.js не установлен"; echo "Установка: curl -sL https://deb.nodesource.com/setup_0.12 | sudo bash -"; exit 1; }
    node node_modules/gulp/bin/gulp.js build
;;

(cache|clear)
    docker exec kingdom rm -rf /kingdom/app/cache/dev /kingdom/app/cache/prod /kingdom/app/logs/dev.log /kingdom/app/logs/prod.log

    case $2 in
    'warm')
        $0 console cache:warm -e prod
    ;;
    esac
;;

'log')
    case $2 in
    '')
        docker logs kingdom
    ;;

    'nginx')
        docker exec -it kingdom tail -100 /var/log/nginx/kingdom_error.log
    ;;

    'game')
        docker exec kingdom cat /kingdom/app/logs/game_logs/user_actions.log
    ;;

    'players')
        docker exec kingdom cat /kingdom/app/logs/game_logs/user_actions.log \
        | awk -F" "  '{array[$3]}END{for (player in array) print player}' \
        | sort --version-sort
    ;;
    esac
;;

*)
    $0 help
;;
esac

