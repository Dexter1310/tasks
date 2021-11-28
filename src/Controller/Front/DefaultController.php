<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
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
     * @Route("/", name="home")
     * @template("Front/default/index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $user = new User();
        $formUser = $this->createForm(UserType::class, $user);//todo: if new user added. this is your form

        return ['home' => 'inicio',
            'formUser' => $formUser->createView()];
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
     * @Route("/ajax/user",  options={"expose"=true}, name="ajax.user")
     * @return Response
     */
    public function newUserAction(Request $request,UserPasswordEncoderInterface $encoder):Response
    {
        $data=$request->request;
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setActive(0);
        $pass=$data->get('user')['password'];
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $encoded = $encoder->encodePassword($user, $pass);
            $user->setPassword($encoded);
            $user->setToken($data->get('user')['_token']);
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->json("se grabo el usuario");
        }
        return $this->render('user');

    }


}