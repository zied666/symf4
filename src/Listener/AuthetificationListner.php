<?php

namespace App\Listener;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

class AuthetificationListner implements EventSubscriberInterface
{

    private $entityManager;
    private $session;
    private $flashBag;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->flashBag = $flashBag;
    }

    public static function getSubscribedEvents()
    {
        return array(
            "security.authentication.failure" => 'verifConnectionAttemps',
        );
    }

    public function verifConnectionAttemps(AuthenticationFailureEvent $authenticationFailureEvent)
    {
        if (!$this->session->has('numberAttemps'))
            $this->session->set("numberAttemps", 1);
        else
            $this->session->set("numberAttemps", $this->session->get('numberAttemps') + 1);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(array("email" => $authenticationFailureEvent->getAuthenticationToken()->getUsername()));
        if ($user and $user->isAccountNonLocked())
        {
            if ($this->session->get('numberAttemps') >= getenv('NUM_CONNECTION_ATTEMPTS'))
            {
                $user->setLock(1);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->flashBag->add("danger", "This Account has been locked");
            }
            else
                $this->flashBag->add("danger", "You have " . (getenv('NUM_CONNECTION_ATTEMPTS') - $this->session->get('numberAttemps')) . " attempts left");
        }
    }


}