<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class DefaultController extends AbstractController
{
    /**
     * @var UserService $userService
     */
    private $userService;


    /**
     * @Route("/", name="home")
     * @template("Front/default/index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return ['home' => 'inicio'];
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = new User();
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $formUser = $this->createForm(UserType::class, $user);//todo: if new user added. this is your form
        return $this->render('Front/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'formUser' => $formUser->createView(),
        ]);
    }


    /**
     * @Route("/new-user", name="newuser")
     * @template("Front/user/new.html.twig")
     */
    public function newUser(Request $request)
    {
        $user = new User();
        $formUser = $this->createForm(UserType::class, $user);//todo: if new user added. this is your form
        return ['home' => 'inicio',
            'formUser' => $formUser->createView()];
    }


    /**
     * @Route("/ajax/user",  options={"expose"=true}, name="ajax.user")
     * @return Response
     */
    public function newUserAjaxAction(Request $re, UserPasswordEncoderInterface $encoder): Response
    {
        $this->userService = new UserService($encoder, $this->getDoctrine()->getManager());
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $us = $this->userService->addUser($re, $form, $user);
        if ($us) {
            return $this->json("se grabo el usuario");
        } else {
            return $this->render('user');
        }


    }


}