<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Form\UserType;
use App\Services\UserService;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
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
                    <a href="mailto:' . $context->getEmail() . '">' . $context->getEmail() . '</a>
                    ');
                }]
            )
            ->add('actions', TextColumn::class, ['label' => 'Opciones', 'orderable' => false, 'render' => function ($value, $context) {
                $id = $context->getId();
                $show='<a  href="/admin-user-show/' . $id . '" title="visualiza"><span style="color:green"><i class="bi bi-eye"></i></span></a>';
                $update='<a  class="p-2" href="/admin-user-update/' . $id . '" title="Edita"><i class="bi bi-gear"></i></a>';
                $delete=' <a  href="/admin-user-delete/' . $id . '" title="Elimina"><span style="color: red"><i class="bi bi-trash"></i></span></a>';
                if($context->getType() == 'admin'){
                    $delete="";
                }
                return sprintf('
                    <div class="text-center">'.$show.$update.$delete.'</div>');
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
            return $this->json('No se pudo grabar el usuario');
        }
    }


    /**
     * @Route("/admin-user-show/{id}", name="admin.user.show", options={"expose"=true})
     * @ParamConverter("user", class="App\Entity\User")
     * @Template("Admin/user/show.html.twig")
     */
    public function showUserAction(Request $request,User $user)
    {
        $task=new Task();
        $formTask = $this->createForm(TaskType::class, $task);
        return ['user'=>$user,'formTask'=>$formTask->createView()];
    }


    /**
     * @Route("/admin-user-update/{id}", name="admin.user.update", options={"expose"=true})
     * @Template("Admin/user/edit.html.twig")
     * @ParamConverter("user", class="App\Entity\User")
     * @return array|RedirectResponse
     */
    public function updateUserAction(Request $request,User $user)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        return ['formUser' => $form->createView(),'user'=>$user];
    }


    /**
     * @Route("/ajax/edit/user",  options={"expose"=true}, name="edituser")
     */
    public function editUserAction(Request $request,UserService  $userService)
    {

        $em=$this->getDoctrine()->getManager();
        $data=$request->request;
        $user=$em->getRepository(User::class)->findOneBy(['id'=>$data->get('id')]);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userService->updateUser($user);
            return $this->json("Se actualizo ".$data->get('user')['username']);
        }else{
            return $this->json('no se actulizado');
        }
    }

    /**
     * @Route("/admin-user-delete/{id}", name="ajax.admin.user.delete", options={"expose"=true})
     * @ParamConverter("user", class="App\Entity\User")
     */
    public function deleteUserAction(Request $request,User $user)
    {
        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('admin.user');
    }

}