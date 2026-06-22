<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Remplit automatiquement les champs d'audit (createdAt/By, updatedAt/By)
 * pour toute entité exposant les setters correspondants (trait TimestampableTrait).
 */
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class TimestampableListener
{
    public function __construct(private readonly Security $security)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        $now = new \DateTimeImmutable();

        if (method_exists($entity, 'setCreatedAt') && $entity->getCreatedAt() === null) {
            $entity->setCreatedAt($now);
        }
        if (method_exists($entity, 'setCreatedBy') && $entity->getCreatedBy() === null) {
            $entity->setCreatedBy($this->currentUser());
        }
        if (method_exists($entity, 'setUpdatedAt')) {
            $entity->setUpdatedAt($now);
        }
        if (method_exists($entity, 'setUpdatedBy')) {
            $entity->setUpdatedBy($this->currentUser());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        $changed = false;
        if (method_exists($entity, 'setUpdatedAt')) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
            $changed = true;
        }
        if (method_exists($entity, 'setUpdatedBy')) {
            $entity->setUpdatedBy($this->currentUser());
            $changed = true;
        }

        if ($changed) {
            // En preUpdate, il faut recalculer le changeset pour persister nos modifications.
            $em = $args->getObjectManager();
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet(
                $em->getClassMetadata($entity::class),
                $entity
            );
        }
    }

    private function currentUser(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }
}
