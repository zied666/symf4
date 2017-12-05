<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class StatusControllerTest extends WebTestCase
{
    private $client = null;
    private $em;

    protected function setUp()
    {

        $this->client = static::createClient();
    }


    public function testAccessRoot()
    {
        $crawler = $this->client->request('GET', '/');
        //$this->assertEquals(1, 1);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }



}
