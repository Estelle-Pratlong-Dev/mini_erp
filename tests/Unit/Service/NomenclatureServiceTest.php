<?php

namespace App\Tests\Unit\Service;

use App\Entity\Composant;
use App\Entity\Produit;
use App\Service\NomenclatureService;
use PHPUnit\Framework\TestCase;

class NomenclatureServiceTest extends TestCase
{
    private function produit(string $ref): Produit
    {
        return (new Produit())->setReference($ref)->setDesignation($ref);
    }

    private function composer(Produit $parent, Produit $ingredient, string $qte): void
    {
        $parent->addComposant((new Composant())->setComposant($ingredient)->setQuantite($qte));
    }

    public function testEclatementSimple(): void
    {
        $farine = $this->produit('FARINE');
        $levain = $this->produit('LEVAIN');
        $baguette = $this->produit('BAGUETTE');
        $this->composer($baguette, $farine, '0.6');
        $this->composer($baguette, $levain, '0.2');

        $besoins = (new NomenclatureService())->besoins($baguette, 10);

        $map = [];
        foreach ($besoins as $b) {
            $map[$b['produit']->getReference()] = $b['quantite'];
        }

        self::assertSame(6.0, $map['FARINE']);
        self::assertSame(2.0, $map['LEVAIN']);
    }

    public function testEclatementImbrique(): void
    {
        $farine = $this->produit('FARINE');
        $baguette = $this->produit('BAGUETTE');
        $this->composer($baguette, $farine, '0.6');

        $lot = $this->produit('LOT_12'); // 12 baguettes
        $this->composer($lot, $baguette, '12');

        $besoins = (new NomenclatureService())->besoins($lot, 1);

        self::assertCount(1, $besoins);
        self::assertSame('FARINE', $besoins[0]['produit']->getReference());
        self::assertSame(7.2, $besoins[0]['quantite']); // 12 × 0.6
    }

    public function testProtectionAntiCycle(): void
    {
        $a = $this->produit('A');
        $b = $this->produit('B');
        $this->composer($a, $b, '1');
        $this->composer($b, $a, '1'); // cycle

        // Ne doit pas boucler indéfiniment.
        $besoins = (new NomenclatureService())->besoins($a, 1);

        self::assertIsArray($besoins);
    }
}
