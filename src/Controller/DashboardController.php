<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction(Request $request)
    {
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
}
