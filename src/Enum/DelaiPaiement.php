<?php

namespace App\Enum;

/**
 * Délai de paiement (en jours). Sert à calculer la date d'échéance d'une facture.
 */
enum DelaiPaiement: int
{
    case COMPTANT = 0;
    case JOURS_15 = 15;
    case JOURS_30 = 30;
    case JOURS_45 = 45;
    case JOURS_60 = 60;

    public function libelle(): string
    {
        return match ($this) {
            self::COMPTANT => 'Comptant',
            self::JOURS_15 => '15 jours',
            self::JOURS_30 => '30 jours',
            self::JOURS_45 => '45 jours',
            self::JOURS_60 => '60 jours',
        };
    }
}
