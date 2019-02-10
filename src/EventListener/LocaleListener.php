<?php
// src/EventListener/LocaleListener.php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Negotiation\LanguageNegotiator;
use App\Repository\LanguageRepository;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;
    private $locales;

    public function __construct(LanguageRepository $languageRepository)
    {
        $this->locales = $languageRepository->getAllCodes();
        $this->defaultLocale = $this->locales[0];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $language = $this->defaultLocale;
        if (null !== $acceptLanguage = $event->getRequest()->headers->get('Accept-Language')) {
            $negotiator = new LanguageNegotiator();
            $best       = $negotiator->getBest(
                $event->getRequest()->headers->get('Accept-Language'),
                $this->locales
            );

            if (null !== $best) {
                $language = $best->getType();
            }
        }

        $request->getSession()->set('_locale', $language);
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}