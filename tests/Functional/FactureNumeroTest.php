<?php

namespace App\Tests\Functional;

use App\Entity\Societe;
use App\Repository\SocieteRepository;
use App\Service\FactureNumeroGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Vérifie que la numérotation des factures est séquentielle et incrémente le compteur.
 */
class FactureNumeroTest extends KernelTestCase
{
    public function testNumerotationSequentielle(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $generator = $container->get(FactureNumeroGenerator::class);
        $societeRepo = $container->get(SocieteRepository::class);

        $societeCreee = false;
        $societe = $societeRepo->getSociete();
        if ($societe === null) {
            $societe = (new Societe())->setRaisonSociale('Société de test');
            $em->persist($societe);
            $em->flush();
            $societeCreee = true;
        }

        $avant = $societe->getProchainNumeroFacture();

        $numero1 = $generator->generer();
        $numero2 = $generator->generer();

        self::assertNotSame($numero1, $numero2, 'Deux factures ne doivent pas avoir le même numéro.');
        self::assertStringEndsWith(sprintf('%05d', $avant), $numero1);
        self::assertStringEndsWith(sprintf('%05d', $avant + 1), $numero2);

        $em->refresh($societe);
        self::assertSame($avant + 2, $societe->getProchainNumeroFacture(), 'Le compteur doit avancer de 2.');

        if ($societeCreee) {
            $em->remove($societe);
            $em->flush();
        }
    }
}
