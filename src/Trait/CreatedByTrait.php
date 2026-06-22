<?php

namespace App\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

trait CreatedByTrait
{
    private Security $security;

    public function injectSecurity(Security $security): void
    {
        $this->security = $security;
    }

    public function persistEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if (method_exists($entityInstance, 'setCreatedBy')) {
            $entityInstance->setCreatedBy($this->security->getUser());
        }
        if (method_exists($entityInstance, 'setCreatedAt')) {
            $entityInstance->setCreatedAt(new \DateTimeImmutable());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if (method_exists($entityInstance, 'setUpdatedBy')) {
            $entityInstance->setUpdatedBy($this->security->getUser());
        }
        if (method_exists($entityInstance, 'setUpdatedAt')) {
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
