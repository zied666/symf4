<?php

namespace App\Listener;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Service\MailerService;

class CreateUserListner implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array('postPersist');
    }

    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $user = $args->getObject();
        if ($user instanceof User)
        {
            $this->mailerService->mailAddUser($user, $user->getPlainPassword());
        }
    }


}