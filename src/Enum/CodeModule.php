<?php

namespace App\Enum;

/**
 * Liste des modules activables/désactivables de l'ERP.
 * Chaque valeur identifie un module métier vendable séparément.
 */
enum CodeModule: string
{
    case CONTACTS = 'CONTACTS';
    case CATALOGUE = 'CATALOGUE';
    case PROJETS = 'PROJETS';
    case CONTRATS = 'CONTRATS';
    case FACTURATION = 'FACTURATION';
    case VENTE_DIRECTE = 'VENTE_DIRECTE';
    case STOCK = 'STOCK';
    case DEPENSES = 'DEPENSES';
    case STATISTIQUES = 'STATISTIQUES';
    case COMPTA = 'COMPTA';

    public function libelle(): string
    {
        return match ($this) {
            self::CONTACTS => 'Contacts',
            self::CATALOGUE => 'Catalogue (produits)',
            self::PROJETS => 'Projets',
            self::CONTRATS => 'Contrats / Devis',
            self::FACTURATION => 'Facturation',
            self::VENTE_DIRECTE => 'Vente directe (caisse)',
            self::STOCK => 'Stock',
            self::DEPENSES => 'Dépenses',
            self::STATISTIQUES => 'Statistiques',
            self::COMPTA => 'Comptabilité / Export',
        };
    }
}
