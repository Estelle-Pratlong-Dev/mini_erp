<?php

namespace App\Enum;

/**
 * Délai / condition de paiement. Sert à calculer la date d'échéance d'une facture.
 */
enum DelaiPaiement: string
{
    case A_RECEPTION = 'A_RECEPTION';
    case A_LA_COMMANDE = 'A_LA_COMMANDE';
    case JOURS_15 = 'JOURS_15';
    case JOURS_30 = 'JOURS_30';
    case JOURS_45 = 'JOURS_45';
    case JOURS_60 = 'JOURS_60';

    public function libelle(): string
    {
        return match ($this) {
            self::A_RECEPTION => 'À réception',
            self::A_LA_COMMANDE => 'À la commande',
            self::JOURS_15 => '15 jours',
            self::JOURS_30 => '30 jours',
            self::JOURS_45 => '45 jours',
            self::JOURS_60 => '60 jours',
        };
    }

    /** Nombre de jours à ajouter à la date d'émission pour obtenir l'échéance. */
    public function jours(): int
    {
        return match ($this) {
            self::A_RECEPTION, self::A_LA_COMMANDE => 0,
            self::JOURS_15 => 15,
            self::JOURS_30 => 30,
            self::JOURS_45 => 45,
            self::JOURS_60 => 60,
        };
    }
}
