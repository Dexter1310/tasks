<?php
namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UseController extends AbstractController{
    /**
     * @var UserService $userService
     */
    private $userService;

    /**
     * @Route("/new-user", name="newuser")
     * @template("Front/user/new.html.twig")
     */
    public function newUser(Request $request)
    {
        $user = new User();
        $formUser = $this->createForm(UserType::class, $user);//todo: if new user added. this is your form
        return ['formUser' => $formUser->createView()];
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

    /**
     * @Route("/show-user/{id}", name="show.user")
     * @template("Front/user/show.html.twig")
     * @param integer $id
     */
    public function nshowUserAction(Request $request,int $id)
    {
        return [];
    }



}