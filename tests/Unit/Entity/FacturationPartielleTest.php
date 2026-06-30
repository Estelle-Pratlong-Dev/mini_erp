<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Facture;
use App\Entity\LigneArticle;
use App\Entity\DelaiPaiement;
use PHPUnit\Framework\TestCase;

/**
 * Facturation partielle : % facturé par rapport au contrat, et calcul de l'échéance.
 */
class FacturationPartielleTest extends TestCase
{
    public function testPourcentageFactureParRapportAuContrat(): void
    {
        $source = (new LigneArticle())->setQuantite('10')->setPrixUnitaireHt('18.50')->setTauxTva('5.5');

        $ligne = (new LigneArticle())
            ->setQuantite('5')
            ->setPrixUnitaireHt('18.50')
            ->setTauxTva('5.5');
        $ligne->setLigneSource($source);

        self::assertSame('10', $ligne->getQuantiteContrat());
        self::assertSame(185.0, $ligne->getMontantContrat());
        self::assertSame(50.0, $ligne->getPourcentageFacture());
        self::assertSame(92.50, $ligne->getMontantHt());
    }

    public function testPourcentageNulSansSource(): void
    {
        $ligne = (new LigneArticle())->setQuantite('3')->setPrixUnitaireHt('10')->setTauxTva('20');

        self::assertNull($ligne->getPourcentageFacture());
        self::assertNull($ligne->getQuantiteContrat());
    }

    public function testDeductionDejaFactureDonneLeNet(): void
    {
        $facture = new Facture();
        $facture->addLigne(
            (new LigneArticle())->setQuantite('10')->setPrixUnitaireHt('18.50')->setTauxTva('5.5')
        ); // 185.00 HT, 10.18 TVA
        $facture->setMontantDejaFactureHt('100.00')->setMontantDejaFactureTva('5.50');

        self::assertTrue($facture->aDeduction());
        self::assertSame(185.00, $facture->getTotalHt());
        self::assertSame(85.00, $facture->getNetHt());
        self::assertSame(89.68, $facture->getNetTtc());
    }

    public function testSansDeductionLeNetEgaleLeTotal(): void
    {
        $facture = new Facture();
        $facture->addLigne(
            (new LigneArticle())->setQuantite('2')->setPrixUnitaireHt('18.50')->setTauxTva('5.5')
        );

        self::assertFalse($facture->aDeduction());
        self::assertSame($facture->getTotalTtc(), $facture->getNetTtc());
    }

    public function testEcheanceCalculeeSelonLeDelai(): void
    {
        $facture = (new Facture())
            ->setDateEmission(new \DateTimeImmutable('2026-06-22'))
            ->setDelaiPaiement((new DelaiPaiement())->setNom('30 jours')->setJours(30));

        $facture->appliquerDelaiPaiement();

        self::assertSame('2026-07-22', $facture->getDateEcheance()->format('Y-m-d'));
    }
}
