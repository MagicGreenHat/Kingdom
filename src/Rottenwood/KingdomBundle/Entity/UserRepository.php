<?php

namespace Rottenwood\KingdomBundle\Entity;

use Rottenwood\KingdomBundle\Entity\Infrastructure\AbstractRepository;

class UserRepository extends AbstractRepository {

    public function findById($userId) {
        return $this->find($userId);
    }
}
