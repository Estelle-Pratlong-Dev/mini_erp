<?php

namespace App\Service;

use App\Enum\CodeModule;
use App\Repository\ModuleRepository;

/**
 * Point central pour savoir si un module est activé.
 * Les états sont chargés une seule fois par requête (cache mémoire interne).
 */
class ModuleManager
{
    /** @var array<string, bool>|null */
    private ?array $cache = null;

    public function __construct(private readonly ModuleRepository $moduleRepository)
    {
    }

    public function isEnabled(CodeModule|string $code): bool
    {
        $key = $code instanceof CodeModule ? $code->value : $code;

        return $this->getMap()[$key] ?? false;
    }

    /**
     * @return list<CodeModule> modules actifs
     */
    public function getEnabledModules(): array
    {
        $enabled = [];
        foreach (CodeModule::cases() as $case) {
            if ($this->isEnabled($case)) {
                $enabled[] = $case;
            }
        }

        return $enabled;
    }

    /**
     * @return array<string, bool>
     */
    private function getMap(): array
    {
        if ($this->cache === null) {
            $this->cache = $this->moduleRepository->getActiveCodesMap();
        }

        return $this->cache;
    }
}
