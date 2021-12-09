<?php
/**
 * Created by Javier Orti
 * Date: 26 - 11 - 2021
 */


namespace App\DataFixtures;

use App\Entity\Company;
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
        $user->setName("Javier");
        $user->setLastname("Orti");
        $user->setRoles(User::R_USER);
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
        $userAdmin->setName("Norberto");
        $userAdmin->setLastname("Sanchez");
        $userAdmin->setRoles(User::R_ADMIN);
        $userAdmin->setActive(1);
        $userAdmin->setToken('xxx');
        $userAdmin->setType("admin");
        $userAdmin->setEmail("admin@admin.com");
        $userAdmin->setPassword($this->passwordHasher->hashPassword(
            $userAdmin,
            'admin'
        ));

        //Added SUPER Admin User
        $userSuperAdmin = new User();
        $userSuperAdmin->setUsername('superadmin');
        $userSuperAdmin->setName("Norberto");
        $userSuperAdmin->setLastname("Sanchez");
        $userSuperAdmin->setRoles(User::R_SUPER_ADMIN);
        $userSuperAdmin->setActive(1);
        $userSuperAdmin->setToken('xxx');
        $userSuperAdmin->setType("super");
        $userSuperAdmin->setEmail("superadmin@superadmin.com");
        $userSuperAdmin->setPassword($this->passwordHasher->hashPassword(
            $userSuperAdmin,
            'superadmin'
        ));


        //Added COMPANY  THE CIRCLE
        $company = new Company();
        $company->setName('THE CIRCLE');
        $company->setCreatedAt(new \DateTime('now'));//created  in date today now
        $company->setEmail('thecircle@thecircle.es');
        $company->setLogo("https://thecirclemgt.com/img/logo.png");
        $company->setAddress('c/ Gavarres, nave 6 , La Pera (Gerona)');
        $company->setDescription("Empresa de servicios");


        $manager->persist($company);
        $manager->persist($user);
        $manager->persist($userAdmin);
        $manager->persist($userSuperAdmin);
        $manager->flush();
    }
}
