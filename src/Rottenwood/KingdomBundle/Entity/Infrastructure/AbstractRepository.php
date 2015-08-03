<?php

namespace Rottenwood\KingdomBundle\Entity\Infrastructure;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class AbstractRepository extends EntityRepository {

    /**
     * @param object $entity
     */
    public function persist($entity) {
        $this->_em->persist($entity);
    }

    /**
     * @param object|null $entity
     */
    public function flush($entity = null) {
        $this->_em->flush($entity);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager() {
        return $this->_em;
    }
}
