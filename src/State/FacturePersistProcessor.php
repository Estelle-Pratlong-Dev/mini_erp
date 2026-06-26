<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Facture;
use App\Service\FactureNumeroGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Processor de création de facture via l'API : attribue un numéro séquentiel
 * avant de déléguer la persistance au processor Doctrine standard d'API Platform.
 *
 * @implements ProcessorInterface<Facture, Facture>
 */
final class FacturePersistProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        private readonly FactureNumeroGenerator $numeroGenerator,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Facture && $data->getNumero() === null) {
            $data->setNumero($this->numeroGenerator->generer());
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
