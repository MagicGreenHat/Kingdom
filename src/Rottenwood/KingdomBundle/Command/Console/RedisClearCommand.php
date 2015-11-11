<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Redis\RedisClientInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisClearCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:redis:clear');
        $this->setDescription('Очистка данных в redis');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        /** @var \Redis $redis */
        $redis = $this->getContainer()->get('snc_redis.default');
        $redis->del(RedisClientInterface::ID_USERNAME_HASH);
        $redis->del(RedisClientInterface::ID_SESSION_HASH);
        $redis->del(RedisClientInterface::SESSION_ID_HASH);
        $redis->del(RedisClientInterface::ONLINE_LIST);

        $output->writeln('Данные удалены из redis');
    }
}
