<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if(!$this->session->has("menu_collapsed"))
            $this->session->set("menu_collapsed",false);

        if(!$this->session->has("locale"))
            $this->session->set("locale",array("code"=>"en",'name'=>"English"));

        $event->getRequest()->setLocale($this->session->get("locale")['code']);
    }




}