<?php

namespace Rottenwood\KingdomBundle\Command\Console\Integration;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class Truncate extends ContainerAwareCommand {

    protected function truncateEntity($entityName) {
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
