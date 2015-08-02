<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MapPurgeCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:map:purge')->setDescription('Удаление всех комнат');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $output->writeln('Удаление комнат ...');

        $entityMetadata = $em->getClassMetadata(Room::class);
        $tableName = $entityMetadata->getTableName();
        $connection = $em->getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM ' . $tableName);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }

        $connection->exec('ALTER TABLE ' . $tableName . ' AUTO_INCREMENT = 1;');

        $output->writeln('Комнаты удалены.');
    }
}
