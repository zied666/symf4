<?php
namespace App\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setRoles(['ROLE_ADMIN'])
            ->setFullName("Administrateur")
            ->setMobile(123456789)
            ->setPassword($this->encoder->encodePassword($user, 'admin@gmail.com'))
            ->setEmail("admin@gmail.com");
        $manager->persist($user);
        $this->addReference('user-admin', $user);



        $manager->flush();
    }
}