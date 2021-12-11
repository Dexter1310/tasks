<?php
/**
 * Created by Javier Orti
 * Date: 26 - 11 - 2021
 */


namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Service;
use App\Entity\Task;
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

        //Added SUPER_ADMIN User FOR ALL CONTROL
        $userSuperAdmin = new User();
        $userSuperAdmin->setUsername('superadmin');
        $userSuperAdmin->setName("Norberto");
        $userSuperAdmin->setLastname("Sanchez");
        $userSuperAdmin->setRoles(User::R_SUPER_ADMIN);
        $userSuperAdmin->setActive(1);
        $userSuperAdmin->setToken('xxx');
        $userSuperAdmin->setType("super");
        $userSuperAdmin->setEmail("super@super.com");
        $userSuperAdmin->setPassword($this->passwordHasher->hashPassword(
            $userSuperAdmin,
            'superadmin'
        ));


        //Added COMPANY
        $company = new Company();
        $company->setName('THE CIRCLE');
        $company->setCreatedAt(new \DateTime('now'));//created  in date today now
        $company->setEmail('thecircle@thecircle.es');
        $company->setLogo("https://thecirclemgt.com/img/logo.png");
        $company->setAddress('c/ Gavarres, nave 6 , La Pera (Gerona)');
        $company->setDescription("Empresa de servicios");


        //Added SERVICE FOR ONE COMPANY

        $service = new Service();
        $service->setCompany($company);
        $service->setCreatedAt(new \DateTime('now'));//created  in date today now
        $service->setName('mantenimiento');
        $service->setActive(1);
        $service->setDescription("mantenimientos de media tensión en centros de transformación");



        //Added ADMIN FOR ONE COMPANY
        $userAdmin = new User();
        $userAdmin->setCompany($company);
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

        //Added OPERATOR User
        $userOperator = new User();
        $userOperator->setCompany($company);
        $userOperator->setUsername('operator');
        $userOperator->setName("Juan");
        $userOperator->setLastname("Perez");
        $userOperator->setRoles(User::R_OPERATOR);
        $userOperator->setActive(1);
        $userOperator->setToken('xxx');
        $userOperator->setType("operator");
        $userOperator->setEmail("juan@gmail.com");
        $userOperator->setPassword($this->passwordHasher->hashPassword(
            $userOperator,
            'dexter1310'
        ));

        //Added TASK  FOR ONE COMPANY
        $task = new Task();
        $task->setCompany($company);
        $task->addIduser($userOperator);
        $task->setTitle('mantenimiento de la semana');
        $task->setState(0);
        $task->setDescription('mantenimmiento  de revisión de cuadros eléctricos');
        $task->setService($service);
        $task->setMaterial('Se utiliza equipos de medición de cat.1');
        $task->setViewOperator(0);


        //Added CLIENT User
        $user = new User();
        $user->setCompany($company);
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
            'dexter1310'
        ));





        $manager->persist($userSuperAdmin);
        $manager->persist($company);
        $manager->persist($service);
        $manager->persist($userAdmin);
        $manager->persist($userOperator);
        $manager->persist($task);
        $manager->persist($user);


        $manager->flush();
    }
}
