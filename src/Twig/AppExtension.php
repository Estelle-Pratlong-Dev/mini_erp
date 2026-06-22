<?php

namespace App\Twig;

use App\Service\ModuleManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(private readonly ModuleManager $moduleManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('module_actif', [$this, 'moduleActif']),
        ];
    }

    public function moduleActif(string $code): bool
    {
        return $this->moduleManager->isEnabled($code);
    }
}
