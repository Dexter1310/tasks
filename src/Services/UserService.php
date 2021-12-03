<?php

/**
 * Note: Service for User
 * Created by: Javier Orti
 * Date: 29 - 11 - 2021
 */

namespace App\Services;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
        if ($type == "admin") {
            $user->setRoles(User::R_ADMIN);
        } elseif ($type == "operator") {
            $user->setRoles(User::R_OPERATOR);
        } else {
            $user->setRoles(User::R_USER);
        }
        $user->setActive(0);
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

    public function updateUser($user)
    {
        /**
         * @var User $user
         */
        $pass = $user->getPassword();
        $en = $this->encoder->encodePassword($user, $pass);
        $user->setPassword($en);
        $this->em->persist($user);
        $this->em->flush();
    }


}