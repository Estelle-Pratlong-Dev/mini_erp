<?php

namespace App\EventSubscriber;

use App\Attribute\RequireModule;
use App\Service\ModuleManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Bloque l'accès aux contrôleurs marqués #[RequireModule] si le module est désactivé.
 * Empêche d'atteindre par URL une fonctionnalité d'un module non vendu/activé.
 */
class ModuleAccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ModuleManager $moduleManager,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $attributes = $event->getAttributes(RequireModule::class);
        if (empty($attributes)) {
            return;
        }

        /** @var RequireModule $require */
        foreach ($attributes as $require) {
            if (!$this->moduleManager->isEnabled($require->module)) {
                $url = $this->urlGenerator->generate('app_home');
                $event->setController(static fn (): RedirectResponse => new RedirectResponse($url));

                $request = $event->getRequest();
                if ($request->hasSession()) {
                    $request->getSession()->getFlashBag()->add(
                        'warning',
                        sprintf('Le module « %s » n\'est pas activé.', $require->module->libelle())
                    );
                }

                return;
            }
        }
    }
}
