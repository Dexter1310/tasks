<?php

/**
 * Note: Service for User
 * Created by: Javier Orti
 * Date: 29 - 11 - 2021
 */

namespace App\Services;

use App\Entity\Infoclient;
use App\Entity\Service;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     */
    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
    }


    public function addUser(Request $request, FormInterface $form, $user)
    {
        $data = $request->request;
        /**
         * @var User $user
         */
        $type = $data->get('user')['type'];
        $specialized = $data->get('specialized');
        if ($specialized) {// si existe servicio para el usuario
            $service = $this->em->getRepository(Service::class)->find(['id' => $specialized]);
        } else {
            $service = null;
        }

        if ($type == "admin") {
            $user->setRoles(User::R_ADMIN);
        } elseif ($type == "operator") {
            $user->setRoles(User::R_OPERATOR);
        } else {
            $user->setRoles(User::R_USER);
            $infoClient = new Infoclient();
            $infoClient->setAddress($data->get('address'));
            $infoClient->setProvince($data->get('province'));
            $infoClient->setTown($data->get('town'));
            $infoClient->setCP($data->get('cp'));
            $infoClient->setNumber($data->get('number'));
            $infoClient->setOtherTlf($data->get('otherTlf'));
            $infoClient->setInfoExtra($data->get('infoExtra'));
            $user->setInfoclient($infoClient);
        }
        $user->setActive(0);
        $user->setService($service);
        $user->setCreatedAt(new \DateTime('now'));
        $pass = $data->get('user')['password'];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $en = $this->encoder->encodePassword($user, $pass);
            $user->setPassword($en);
            $user->setToken($data->get('user')['_token']);
            $this->em->persist($user);
            $this->em->flush();
        }
        return $data;
    }

    public function updateUser($user, $password)
    {
        /**
         * @var User $user
         */

        $pass = $user->getPassword();


        if ($pass != 'null') {
            $en = $this->encoder->encodePassword($user, $pass);
            $user->setPassword($en);
        } else {
            $user->setPassword($password);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    public function countTaskPendienteOperator($user, $countTaskPendiente)
    {
        if ($user && $user->getTask()->toArray()) { //if exist task pendiente for operator
            $tasksUser = $user->getTask()->toArray();
            foreach ($tasksUser as $task) {
                if ($task->getState() == 0) {
                    $countTaskPendiente++;
                }
            }
        }
        return $countTaskPendiente;
    }

    public function showInformationClient($user)
    {
        return $this->em->getRepository(Infoclient::class)->findOneBy(['idUser' => $user->getId()]);
    }

    public function userShow($user)
    {
        return $this->em->getRepository(User::class)->findOneBy(['id' => $user]);

    }

}