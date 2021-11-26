<?php
/**
 * Created by Javier Orti
 * Date: 26 - 11 - 2021
 */


namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {

        //Added Normal User
        $user = new User();
        $user->setUsername('tba');
        $user->setRoles(['ROLE_USER']);
        $user->setActive(1);
        $user->setToken('xxx');
        $user->setType("client");
        $user->setEmail("insorti@gmail.com");
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'usertba'
        ));
        //Added Admin User
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setActive(1);
        $userAdmin->setToken('xxx');
        $userAdmin->setType("company");
        $userAdmin->setEmail("admin@admin.com");
        $userAdmin->setPassword($this->passwordHasher->hashPassword(
            $userAdmin,
            'admin'
        ));
        $manager->persist($user);
        $manager->persist($userAdmin);
        $manager->flush();
    }
}
