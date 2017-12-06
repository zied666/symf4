<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\DataTableService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admins")
 * @IsGranted("ROLE_ADMIN")
 */
class AdministratorsController extends Controller
{
    /**
     * @Route("/", name="admins_list")
     */
    public function listAction(Request $request, DataTableService $dataTableService, EntityManagerInterface $em)
    {
        if ($request->isXmlHttpRequest())
        {
            $dataRequest = $dataTableService->getFilter();
            extract($dataRequest);
            $items = $em->getRepository(User::class)->dataTable(array("ROLE_ADMIN"), $filters, $start, $length, true, $columNameOrder, $dirOrder);
            $output = array(
                'data'            => array(),
                'recordsFiltered' => count($em->getRepository(User::class)->dataTable(array("ROLE_ADMIN"), $filters)),
                'recordsTotal'    => count($em->getRepository(User::class)->dataTable(array("ROLE_ADMIN")))
            );

            foreach ($items as $item)
            {
                $output['data'][] = [
                    'id'       => $item->getId(),
                    'fullName' => $item->getFullName(),
                    'mobile'   => $item->getMobile(),
                    'email'    => $item->getEmail(),
                    'locked'   => $this->renderView("users/admin/extras.html.twig", array("viewType" => "lock", 'item' => $item)),
                    'actions'  => $this->renderView("users/admin/extras.html.twig", array("viewType" => "actions", 'item' => $item))
                ];
            }
            return new JsonResponse($output);
        }
        return $this->render('users/admin/index.html.twig');
    }

    /**
     * @Route("/form/{id}", name="admins_form")
     */
    public function formAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, $id = null)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        if ($id)
        {
            $user = $em->getRepository(User::class)->find($id);
            $form = $this->createForm(UserType::class, $user, array('validation_groups' => array('update')))->remove("plainPassword");
        } else
        {
            $user = new User();
            $form = $this->createForm(UserType::class, $user, array('validation_groups' => array('create')));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            if (!$id)
                $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $user->setRoles(array("ROLE_ADMIN"));
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Your request has been successfully registered");
            return $this->redirectToRoute("admins_list");
        }
        return $this->render('users/admin/form.html.twig', array(
            'form' => $form->createView(),
            "user" => $user
        ));
    }


    /**
     * @Route("/delete/{id}", name="admins_delete")
     */
    public function deleteAction(User $user)
    {
        if ($this->getUser()->getId() == $user->getId())
            return new JsonResponse(false);
        $em = $this->get('doctrine.orm.entity_manager');
        try
        {
            $em->remove($user);
            $em->flush();
            return new JsonResponse(true);
        } catch (\Exception $ex)
        {
            return new JsonResponse(false);
        }
    }


    /**
     * @Route("/changeLock", name="admins_change_lock")
     */
    public function changeLockAction(Request $request,EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($request->get('id'));
        if($user)
        {
            $user->setLock(!$user->getLock());
            $em->persist($user);
            $em->flush();
            return new JsonResponse("1");
        }
        return new JsonResponse("0");
    }


}
