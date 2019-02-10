<?php
// src/EventListener/LocaleListener.php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Negotiation\LanguageNegotiator;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $language = 'en';
        if (null !== $acceptLanguage = $event->getRequest()->headers->get('Accept-Language')) {
            $negotiator = new LanguageNegotiator();
            $best       = $negotiator->getBest(
                $event->getRequest()->headers->get('Accept-Language'),
                ['en','de','es','fr']
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