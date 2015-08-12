<?php

namespace Rottenwood\KingdomBundle\Command\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ItemsPurgeCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('kingdom:items:purge')->setDescription('Удаление всех предметов');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->write('Удаление предметов в инвентарях ... ');
        $this->truncateEntity('Rottenwood\KingdomBundle\Entity\InventoryItem');
        $output->writeln('Инвентари удалены.');

        $output->write('Удаление предметов ... ');
        $this->truncateEntity('Rottenwood\KingdomBundle\Entity\Infrastructure\Item');
        $output->writeln('Предметы удалены.');
    }

    private function truncateEntity($entityName) {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $entityMetadata = $em->getClassMetadata($entityName);
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
    }
}
