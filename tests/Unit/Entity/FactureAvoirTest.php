<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Facture;
use App\Entity\LigneArticle;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie les totaux d'une facture et la notion d'avoir (= montant négatif).
 */
class FactureAvoirTest extends TestCase
{
    private function ligne(string $qte, string $puHt, string $tva): LigneArticle
    {
        return (new LigneArticle())
            ->setDesignation('Article')
            ->setQuantite($qte)
            ->setPrixUnitaireHt($puHt)
            ->setTauxTva($tva);
    }

    public function testFacturePositive(): void
    {
        $facture = new Facture();
        $facture->addLigne($this->ligne('2', '18.50', '5.5')); // 37.00 HT

        self::assertSame(37.00, $facture->getTotalHt());
        self::assertSame(39.04, $facture->getTotalTtc());
        self::assertFalse($facture->isAvoir());
        self::assertSame('Facture', $facture->getLibelleType());
    }

    public function testAvoirEstUneFactureNegative(): void
    {
        $facture = new Facture();
        $facture->addLigne($this->ligne('-1', '100.00', '20')); // -100.00 HT

        self::assertSame(-100.00, $facture->getTotalHt());
        self::assertTrue($facture->isAvoir());
        self::assertSame('Avoir', $facture->getLibelleType());
    }
}
