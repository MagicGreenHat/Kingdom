<?php

codecept_debug(`/kingdom/app/console doctrine:database:drop --force -e test`);
codecept_debug(`/kingdom/app/console doctrine:database:create -e test`);
codecept_debug(`/kingdom/app/console doctrine:schema:update --force -e test`);
codecept_debug(`/kingdom/app/console kingdom:create:map -e test`);
codecept_debug(`/kingdom/app/console kingdom:create:user test test test@test.ru -e test`);
codecept_debug(`/kingdom/app/console kingdom:create:items -e test`);
