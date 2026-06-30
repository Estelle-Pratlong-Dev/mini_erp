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

    public function testCoutAchatSimple(): void
    {
        $rose = $this->produit('ROSE')->setPrixAchatHt('0.60');

        self::assertSame(0.60, (new NomenclatureService())->coutAchat($rose));
    }

    public function testCoutAchatCompose(): void
    {
        $rose = $this->produit('ROSE')->setPrixAchatHt('0.60');
        $lys = $this->produit('LYS')->setPrixAchatHt('0.90');
        $compo = $this->produit('COMPO');
        $this->composer($compo, $rose, '6');
        $this->composer($compo, $lys, '4');

        // 6 × 0.60 + 4 × 0.90 = 7.20
        self::assertSame(7.20, (new NomenclatureService())->coutAchat($compo));
    }

    public function testCoutAchatImbrique(): void
    {
        $rose = $this->produit('ROSE')->setPrixAchatHt('0.60');
        $bouquet = $this->produit('BOUQUET');
        $this->composer($bouquet, $rose, '6'); // coût 3.60
        $coffret = $this->produit('COFFRET');
        $this->composer($coffret, $bouquet, '2'); // 2 × 3.60 = 7.20

        self::assertSame(7.20, (new NomenclatureService())->coutAchat($coffret));
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
