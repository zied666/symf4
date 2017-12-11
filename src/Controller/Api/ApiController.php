<?php

namespace App\Controller\Api;

use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends Controller
{
    /**
     * @SWG\Tag(name="Test")
     * @Route("/api/test", methods={"GET"})
     * @SWG\Response( response=200,description="Returns the rewards of an user" )
     * @SWG\Parameter(name="param1", in="query", type="string", description="The field used to order rewards")
     * @SWG\Parameter(name="param2", in="query", type="string", description="The field used to order rewards")
     */
    public function testAction(Request $request)
    {
        return $this->json([$request->get('param1'),$request->get('param2')]);
    }
}
