Техническая документация
========================

Проект представляет из себя Symfony2 приложение, работающее в связке с node.js вебсокет-сервером по модели publisher-subscriber, где каждому клиенту создается свой вебсокет-канал.

## Используемые технологии
* PHP 5.5
* MySQL 5.5
* jQuery 2.1
* Node.js 0.10.25
* Symfony 2.7 [документация](https://symfony.com/doc/current/index.html)
* Redis 2.8.4 [команды](http://redis.io/commands)
* Docker 1.0 [руководство](http://docs.docker.com/linux/started/)
* Autobahn.js 0.9.6 [документация](http://autobahn.ws/js/)
* PHPUnit 4.8.16
* Codeception 2.1.3

## Запуск тестов
* Приемочные, интеграционные и юнит тесты запускаются командой **kingdom.sh test**

## Ход работы приложения

#### Подключение
* Запуск вебсервера и серверов баз данных MySQL и redis
* Заупск серверов на Node.js: вебсокет роутер, обработчик команд и сервис событий
* Клиент запрашивает страницу в браузере
* PHP сервер с помощью симфони производит аутентификацию пользователя
* PHP производит запись в redis параметров пользователя и id его сессии
* PHP отдает пользователю страницу с javascript-клиентом
* Javascript-клиент соединяется с вебсокет роутером
* Javascript-клиент подключается к личному вебсокет-каналу, содержащему id его сессии
* Javascript-клиент регистрируется через вебсокеты на сервере обработчика команд
* Обработчик команд подключается к личному каналу клиента
* Javascript-клиент вешает коллбэк и слушает результаты от обработчика команд

#### Основной цикл взаимодействия клиента с сервером
* Javascript-клиент посылает игровую коману
* Обработчик команд получает команду и запускает соответствующую симфони-команду
* Симфони команда производит соответствующие расчеты бизнес-логики и отдает json-ответ
* Обработчик команд отправляет json-ответ запросившему в его личный вебсокет-канал
* Javascript-клиент обрабатывает полученный результат, отрисовывает данные в браузере


## Структура директорий
* **kingdom.sh** -- Исполняемый файл для управления проектом

* app/config -- Конфигурационные файлы фреймворка
* app/Documents -- Файлы проектной документации
* app/docker -- Конфигурационные файлы для настройки Docker контейнера

* src/Rottenwood/UserBundle -- Расширение для FOSUserBundle
* src/Rottenwood/UserBundle/Resources/views -- шаблоны для отображения страниц логина и регистрации
* Rottenwood/UserBundle/EventListener/FOSUserListener.php -- EventListener для события регистрации

* src/Rottenwood/KingdomBundle/Command/Infrastructure/AbstractGameCommand.php -- Абстрактный класс игровых команд
* src/Rottenwood/KingdomBundle/Command/Infrastructure/GameCommandInterface.php -- Интерфейс для игровых команд
* src/Rottenwood/KingdomBundle/Command/Infrastructure/CommandResponse.php -- DataObject для результата выполнения игровой команды
* src/Rottenwood/KingdomBundle/Command/ExecuteCommand.php -- Симфони команда для вызова игровых команд
* src/Rottenwood/KingdomBundle/Command/Composer/ScriptHandler.php -- Команды для вызова при `composer install`
* src/Rottenwood/KingdomBundle/Command/Console -- Технические консольные команды
* src/Rottenwood/KingdomBundle/Command/Game -- Игровые команды, вызываемые командой kingdom:execute

* src/Rottenwood/KingdomBundle/Entity -- Классы сущностей приложения
* src/Rottenwood/KingdomBundle/Entity/Infrastructure -- Классы репозиториев
* src/Rottenwood/KingdomBundle/Entity/Infrastructure/Item.php -- Абстрактный класс игрового предмета
* src/Rottenwood/KingdomBundle/Entity/Items -- Классы игровых предметов наследующихся от Item

* src/Rottenwood/KingdomBundle/Exception -- Классы исключений
* src/Rottenwood/KingdomBundle/Redis/RedisClientInterface.php -- Интерфейс для удобства работы с redis-командами

* src/Rottenwood/KingdomBundle/Service -- Сервисы, не имеющие состояний
* src/Rottenwood/KingdomBundle/Resources/views -- Twig-шаблоны страниц

* node_modules -- Директория для внешних npm-пакетов. Пакеты нуждающиеся в компилляции добавляются в гит. Остальные устанавливаются отдельно

* vendor/ -- Код сторонних библиотек, подгруженных композером

* tests/ -- директория с тестами
* tests/acceptance -- приемочные тесты, userstory
* tests/functional -- интеграционные тесты, тестирующие комплексные процессы
* tests/unit -- юнит-тесты для тестирования отдельных классов
* tests/_output -- директория с логами тестера. Может пригодиться для отладки
* tests/_output/_coverage -- директория в которой создается отчет о покрытии тестами

### Frontend

* web/ -- Файлы, доступные для запроса браузером: js, css, изображения 

* frontend/namespace.js -- Конфигурация пространства имен (файл должен загружаться первым)
* frontend/Model -- Модели. Классы, могут содержать логику обработки.
* frontend/Controller -- Контроллеры, события и рендеринг страниц. У каждого условного блока пользовательского интерфейса должен быть свой контроллер, отвечающий за обработку блока и работу с моделями.
* frontend/View -- Шаблоны для отрисовки HTML
* web/js/lib -- Внешние библиотеки
* web/js/src -- Старые исходники js-кода
* web/js -- Файлы в данной директории (не во вложенных директориях) являются скомпиллированными.

* src/Rottenwood/KingdomBundle/Resources/views/Default/game.html.twig -- Шаблон основной страницы игры

* websocket/ -- Серверы node.js для работы через вебсокеты
* websocket/router.js -- Вебсокет сервер и роутер соединений
* websocket/gate.js -- Сервер для обработки команд от клиентов
* websocket/ticker.js -- Сервер для генерации событий
