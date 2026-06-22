<?php

namespace App\Attribute;

use App\Enum\CodeModule;

/**
 * Déclare qu'un contrôleur (ou une action) nécessite un module activé.
 * Le ModuleAccessSubscriber bloque l'accès si le module est désactivé.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class RequireModule
{
    public function __construct(public CodeModule $module)
    {
    }
}
