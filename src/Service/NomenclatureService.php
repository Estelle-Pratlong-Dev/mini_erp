<?php

namespace App\Service;

use App\Entity\Produit;

/**
 * Éclate un produit composé (nomenclature) en ses besoins réels d'articles de base,
 * en gérant les compositions imbriquées. Utilisé pour la consommation de stock.
 */
class NomenclatureService
{
    /**
     * Besoins en articles de base pour fabriquer $quantite unités de $produit.
     *
     * @return list<array{produit: Produit, quantite: float}>
     */
    public function besoins(Produit $produit, float $quantite = 1.0): array
    {
        $accumulateur = [];
        $this->accumuler($produit, $quantite, $accumulateur, []);

        return array_map(
            static fn (array $l): array => ['produit' => $l['produit'], 'quantite' => round($l['quantite'], 3)],
            array_values($accumulateur),
        );
    }

    /**
     * @param array<int|string, array{produit: Produit, quantite: float}> $accumulateur
     * @param list<int>                                                    $visites
     */
    private function accumuler(Produit $produit, float $quantite, array &$accumulateur, array $visites): void
    {
        if ($produit->isCompose()) {
            $oid = spl_object_id($produit);
            if (in_array($oid, $visites, true)) {
                return; // protection anti-cycle
            }
            $visites[] = $oid;
            foreach ($produit->getComposants() as $composant) {
                $sous = $composant->getComposant();
                if ($sous === null) {
                    continue;
                }
                $this->accumuler($sous, $quantite * (float) $composant->getQuantite(), $accumulateur, $visites);
            }

            return;
        }

        // Article de base : on cumule la quantité.
        $cle = $produit->getId() ?? spl_object_id($produit);
        if (isset($accumulateur[$cle])) {
            $accumulateur[$cle]['quantite'] += $quantite;
        } else {
            $accumulateur[$cle] = ['produit' => $produit, 'quantite' => $quantite];
        }
    }
}
