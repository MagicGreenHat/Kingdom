<?php

namespace Rottenwood\KingdomBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {

    public function findById($userId) {
        return $this->find($userId);
    }
}
