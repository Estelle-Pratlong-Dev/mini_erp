<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Contrat;
use App\Entity\LigneArticle;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie le calcul des totaux d'un contrat à partir de ses lignes.
 */
class ContratTotauxTest extends TestCase
{
    private function ligne(string $qte, string $puHt, string $tva): LigneArticle
    {
        return (new LigneArticle())
            ->setDesignation('Article')
            ->setQuantite($qte)
            ->setPrixUnitaireHt($puHt)
            ->setTauxTva($tva);
    }

    public function testMontantsDUneLigne(): void
    {
        $ligne = $this->ligne('10', '18.50', '5.5');

        self::assertSame(185.00, $ligne->getMontantHt());
        self::assertSame(10.18, $ligne->getMontantTva()); // round(185 * 5.5%)
        self::assertSame(195.18, $ligne->getMontantTtc());
    }

    public function testTotauxDuContrat(): void
    {
        $contrat = new Contrat();
        $contrat->addLigne($this->ligne('10', '18.50', '5.5')); // 185.00 HT
        $contrat->addLigne($this->ligne('24', '4.20', '5.5'));  // 100.80 HT

        self::assertSame(285.80, $contrat->getTotalHt());
        self::assertSame(15.72, $contrat->getTotalTva());
        self::assertSame(301.52, $contrat->getTotalTtc());
    }

    public function testContratVideADesTotauxNuls(): void
    {
        $contrat = new Contrat();

        self::assertSame(0.0, $contrat->getTotalHt());
        self::assertSame(0.0, $contrat->getTotalTtc());
    }
}
