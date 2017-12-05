<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class StatusControllerTest extends WebTestCase
{

    private $client = null;
    private $em;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->client = static::createClient();
    }

    private function logIn()
    {
        $user = $this->em->getRepository(User::class)->getFirstByRole("ROLE_ADMIN");
        $session = $this->client->getContainer()->get('session');
        $firewallContext = 'main';
        $token = new UsernamePasswordToken($user, "", $firewallContext, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function testAccessRoot()
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlAjaxProvider
     */
    public function testStatusAjax($url)
    {
        $this->logIn();
        $this->client->request('GET', $url, array(), array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }


    /**
     * @dataProvider urlProvider
     */
    public function testStatusPages($url)
    {
        $this->logIn();
        $this->client->request('GET', $url);
        //echo $this->client->getResponse()->getContent();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function urlProvider()
    {
        return array(
            array('/'),
        );
    }

    public function urlAjaxProvider()
    {
        return array(
            array('/'),
            //array('/administrators/')
        );
    }

}