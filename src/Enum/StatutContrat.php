<?php

namespace App\Enum;

enum StatutContrat: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case ENVOYE = 'ENVOYE';
    case ACCEPTE = 'ACCEPTE';
    case REFUSE = 'REFUSE';
    case SIGNE = 'SIGNE';

    public function libelle(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::ENVOYE => 'Envoyé',
            self::ACCEPTE => 'Accepté',
            self::REFUSE => 'Refusé',
            self::SIGNE => 'Signé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'secondary',
            self::ENVOYE => 'info',
            self::ACCEPTE => 'success',
            self::REFUSE => 'danger',
            self::SIGNE => 'primary',
        };
    }
}
