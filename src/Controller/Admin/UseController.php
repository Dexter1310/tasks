<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Services\UserService;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UseController extends AbstractController
{
    /**
     * @var UserService $userService
     */
    private $userService;


    /**
     * @Route("/user", name="admin.user")
     * @template("Admin/user/index.html.twig")
     */
    public function userAction(Request $request, DataTableFactory $dataTableFactory)
    {
        $table = $dataTableFactory->create()
            ->add('username', TextColumn::class, ['label' => 'Usuario', 'className' => 'bold'])
            ->add('type', TextColumn::class, ['label' => 'Tipo', 'className' => 'bold'])
            ->add('email', TextColumn::class, ['label' => 'E-mail', 'orderable' => false, 'render' => function ($value, $context) {
                    return sprintf('
                    <a href="mailto:'.$context->getEmail().'">'.$context->getEmail().'</a>
                    ');
                }]
            )
            ->add('actions', TextColumn::class, ['label' => 'Opciones', 'orderable' => false, 'render' => function ($value, $context) {
                $id = $context->getId();
                return sprintf(
                    '
                    <div class="text-center">
      <a  href="/admin-service-show/' . $id . '" title="visualiza"><span style="color:green"><i class="bi bi-eye"></i></span></a>
      <a  class="p-2" href="/admin-service-update/' . $id . '" title="Edita"><i class="bi bi-gear"></i></a>
      <a class="deleteService" id="' . $id . '" href="#" title="Elimina"><span style="color: red"><i class="bi bi-trash"></i></span></a>
      </div>
');
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => User::class,
            ])->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return ['datatable' => $table];
    }


    /**
     * @Route("/new-user", name="newuser")
     * @template("Admin/user/new.html.twig")
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
     * @template("Admin/user/show.html.twig")
     * @param integer $id
     */
    public function showUserAction(Request $request, int $id)
    {
        return [];
    }


}