<?php

namespace Rottenwood\KingdomBundle\Command\Console\Integration;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Truncate extends ContainerAwareCommand {

    /**
     * @param string          $entityName
     * @param OutputInterface $output
     * @param string          $startEcho
     * @param string          $endEcho
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function truncateEntity(
        string $entityName,
        OutputInterface $output = null,
        string $startEcho = '',
        string $endEcho = ''
    ) {
        if ($output) {
            $output->write($startEcho);
        }

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

        if ($output) {
            $output->writeln($endEcho);
        }
    }
}
