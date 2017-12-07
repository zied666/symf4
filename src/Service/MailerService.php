<?php

namespace App\Service;


use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class MailerService
{


    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $template, FlashBagInterface $flashBag)
    {
        $this->mailer = $mailer;
        $this->template = $template;
        $this->mailer_user = getenv("MAIL_USER");
        $this->flashBag = $flashBag;
    }


    public function mailAddUser(User $user, $plainPassword)
    {
        $subject = 'Account creation';
        $to = $user->getEmail();
        $profile = "";
        if ($user->hasRole("ROLE_CTL"))
            $profile = "ctl";
        if ($user->hasRole("ROLE_VENDOR"))
            $profile = "vendor";
        $body = $this->template->render('mails/new_user_registred.html.twig', array(
            'user'     => $user,
            'password' => $plainPassword,
            'profile'  => $profile,
        ));
        $this->sendMail($to, $subject, $body);
    }

    public function sendMail($to, $subject, $body)
    {
        try
        {
            $message = (new \Swift_Message())
                ->setSubject($subject)
                ->setFrom($this->mailer_user)
                ->setTo($to)
                ->setBody($body, 'text/html');
            $this->mailer->send($message);
        } catch (\Exception $ex)
        {
            $this->flashBag->add("danger",$ex->getMessage());
        }
    }

    public function sendMails($tos, $subject, $body)
    {
        try
        {
            $message = (new \Swift_Message())
                ->setSubject($subject)
                ->setFrom($this->mailer_user)
                ->setBody($body, 'text/html');
            foreach ($tos as $to)
                $message->addTo($to);
            $this->mailer->send($message);
        } catch (\Exception $ex)
        {
            $this->flashBag->add("danger",$ex->getMessage());
        }
    }

}