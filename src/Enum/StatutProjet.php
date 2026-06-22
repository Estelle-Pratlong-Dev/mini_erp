<?php

namespace App\Enum;

enum StatutProjet: string
{
    case EN_COURS = 'EN_COURS';
    case TERMINE = 'TERMINE';
    case ANNULE = 'ANNULE';

    public function libelle(): string
    {
        return match ($this) {
            self::EN_COURS => 'En cours',
            self::TERMINE => 'Terminé',
            self::ANNULE => 'Annulé',
        };
    }

    /** Classe Bootstrap pour le badge de statut. */
    public function couleur(): string
    {
        return match ($this) {
            self::EN_COURS => 'primary',
            self::TERMINE => 'success',
            self::ANNULE => 'secondary',
        };
    }
}
