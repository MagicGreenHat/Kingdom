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
        /** @var RedisClientInterface $redis */
        $redis = $this->getContainer()->get('snc_redis.default');
        $redis->del(RedisClientInterface::CHARACTERS_HASH_NAME);

        $output->writeln('Данные удалены из redis');
    }
}
