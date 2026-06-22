<?php

namespace App\Enum;

enum TypeProduit: string
{
    case BIEN = 'BIEN';
    case SERVICE = 'SERVICE';

    public function libelle(): string
    {
        return match ($this) {
            self::BIEN => 'Bien',
            self::SERVICE => 'Service',
        };
    }
}
