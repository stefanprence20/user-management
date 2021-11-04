<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setFullName('admin');
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $password = $this->passwordEncoder->encodePassword($user, 'pass_1234');
        $user->setPassword($password);
        $manager->persist($user);

        $manager->flush();
    }
}
