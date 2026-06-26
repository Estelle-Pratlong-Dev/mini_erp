<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\SoftDeletableInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Processor pour les suppressions via l'API : effectue une suppression logique
 * (soft delete) au lieu d'un DELETE physique, pour rester cohérent avec le reste
 * de l'application (la date/auteur sont portés par l'audit modifieLe/modifiePar).
 *
 * @implements ProcessorInterface<SoftDeletableInterface, void>
 */
final class SoftDeleteProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof SoftDeletableInterface) {
            $data->setSupprime(true);
            $this->em->flush();
        }
    }
}
