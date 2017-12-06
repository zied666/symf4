<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;


class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction()
    {
        //dump($translator->trans('xxxx'));
        return $this->render('dashboard/index.html.twig');
    }

    /**
     * @Route("/ajax_change_menu", name="ajax_change_menu")
     */
    public function ajaxMenuCollapsed(SessionInterface $session)
    {
        $session->set('menu_collapsed',!$session->get('menu_collapsed'));
        return new JsonResponse("1");
    }

    /**
     * @Route("/changeLocale/{locale}", name="change_locale")
     */
    public function changeLocale(Request $request, $locale)
    {
        $session = $this->get('session');

        if ($locale == "fr")
            $session->set("locale", array("code" => "fr", "name" => "Francais"));

        if ($locale == "en")
            $session->set("locale", array("code" => "en", "name" => "English"));
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
