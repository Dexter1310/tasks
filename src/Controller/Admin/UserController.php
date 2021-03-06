<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Infoclient;
use App\Entity\Service;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Form\UserType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
     * @var int $idUserClient
     */

    private $idUserClient;

    /**
     * @Route("/user", name="admin.user")
     * @template("Admin/user/index.html.twig")
     */
    public function userAction(Request $request, DataTableFactory $dataTableFactory, UserRepository $userRepository)
    {
        $table = $dataTableFactory->create();
        if ($this->getUser()->getType() == 'super') {
            $table->add('logo', TextColumn::class, ['label' => 'Empresa', 'className' => 'd-none d-xl-block',
                'render' => function ($value, $context) {
                    if ($context->getCompany()) {
                        $company = ' <div class="text-center"><img  src="' . $context->getCompany()->getLogo() . '" height="28" alt="CoolBrand"></div> ';
                    } else {
                        $company = null;
                    }
                    return $company;
                }
            ]);
        }

        $table->add('type', TextColumn::class, ['label' => 'Usuario', 'orderable' => false, 'render' => function ($value, $context) {
            /**
             * @var User $context
             * Todo : defined service user if exist operator
             */
            $us= '<span style="color:green;">'.$context->getName().' '.$context->getLastname().'</span><br><br>';
            $type = $context->getType();
            if ($context->getService()) {
                $especialized = "<i style='font-size: 0.8em'> (" . $context->getService()->getName() . ")</i>";
            } else {
                $especialized = "";
            }
            return sprintf($us.$type . $especialized);
        }])
            ->add('email', TextColumn::class, ['label' => 'E-mail', 'className' => 'email-user', 'orderable' => false, 'render' => function ($value, $context) {
                    return sprintf('
                    <a href="mailto:' . $context->getEmail() . '">' . $context->getEmail() . '</a>
                    ');
                }]
            )
            ->add('actions', TextColumn::class, ['label' => 'Opciones', 'orderable' => false, 'render' => function ($value, $context) {
                $id = $context->getId();
                $show = '<a  href="/admin-user-show/' . $id . '" title="visualiza"><span style="color:green"><i class="bi bi-eye"></i></span></a>';
                $update = '<a  class="p-2" href="/admin-user-update/' . $id . '" title="Edita"><i class="bi bi-gear"></i></a>';
                $delete = ' <a  href="/admin-user-delete/' . $id . '" title="Elimina"><span style="color: red"><i class="bi bi-trash"></i></span></a>';
                if ($context->getType() == 'admin' or $context->getType() == 'super') {
                    $delete = "";
                }
                return sprintf('
                    <div class="text-center">' . $show . $update . $delete . '</div>');
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => User::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select(User::ALIAS)
                        ->from(User::class, User::ALIAS);
                    if ($this->getUser()->getType() != 'super') {
                        $builder->andWhere(User::ALIAS . '.company = :val')
                            ->setParameter('val', $this->getUser()->getCompany()->getId());
                    }
                }
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
        if ($this->getUser()) {
            if ($this->getUser()->getCompany()) {
                $services = $this->getDoctrine()->getRepository(Service::class)->findBy(['company' => $this->getUser()->getCompany()]);
            } else {
                $services = $this->getDoctrine()->getRepository(Service::class)->findAll();
            }
        } else {
            $services = null;
        }
        $company = $this->getDoctrine()->getRepository(Company::class)->findAll();
        $formUser = $this->createForm(UserType::class, $user);//todo: if new user added. this is your form
        return ['formUser' => $formUser->createView(), 'services' => $services, 'company' => $company];
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
        if ($this->getUser()->getCompany()) {// if user is ADMIN
            $user->setCompany($this->getUser()->getCompany());
        } else { // if user is  SUPER ADMIN
            $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy(['id' => $re->request->get('company')]);
            $user->setCompany($company);
        }
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
    public function showUserAction(Request $request, User $user, TaskRepository $taskRepository, UserService $userService, DataTableFactory $dataTableFactory)
    {

        $task = new Task();//if show user is operator
        $client = $userService->showInformationClient($user);
        $this->idUserClient = $user->getId();
        $table = $dataTableFactory->create()
            ->add('title', TextColumn::class, ['label' => 'Tarea', 'className' => 'bold'])
            ->add('description', TextColumn::class, ['label' => 'Descripci??n', 'className' => 'bold'])
            ->add('updatedAt', DateTimeColumn::class, ['label' => 'Actualizada', 'className' => 'bold', 'render' => function ($value, $context) {
                return date_format($context->getUpdatedAt(), 'd-m-Y (H:i \h.\)');
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Task::class,
                'query' => function (QueryBuilder $builder, $client) {
                    $builder
                        ->select(Task::ALIAS)
                        ->from(Task::class, Task::ALIAS)
                        ->andWhere(Task::ALIAS . '.idClient = :val')
                        ->setParameter('val', $this->idUserClient);

                }
            ])->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }
        $services = $this->getDoctrine()->getRepository(Service::class)->findBy(['company' => $user->getCompany()]);
        $operators = $this->getDoctrine()->getRepository(User::class)->findBy(['type' => 'operator']);
        $formTask = $this->createForm(TaskType::class, $task);
        $taskUser = $user->getTask()->toArray();

        return ['user' => $user, 'formTask' => $formTask->createView(),
            'operators' => $operators, 'services' => $services, 'taskUser' => $taskUser,
            'pendientes' => $userService->countTaskPendienteOperator($this->getUser(), 0), 'client' => $client, 'datatable' => $table];
    }


    /**
     * @Route("/admin-user-update/{id}", name="admin.user.update", options={"expose"=true})
     * @Template("Admin/user/edit.html.twig")
     * @ParamConverter("user", class="App\Entity\User")
     * @return array|RedirectResponse
     */
    public function updateUserAction(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user);
        if ($this->getUser()->getType() == 'super') {
            $services = $this->getDoctrine()->getRepository(Service::class)->findAll();
            $company = $this->getDoctrine()->getRepository(Company::class)->findAll();

        } else {
            $services = $this->getDoctrine()->getRepository(Service::class)->findBy(['company' => $this->getUser()->getCompany()]);
            $company = $this->getDoctrine()->getRepository(Company::class)->findAll();
        }
        if ($user->getType() == 'super') {
            $form->add('type');
        }

        $form->handleRequest($request);
        return ['formUser' => $form->createView(), 'user' => $user, 'services' => $services, 'company' => $company];
    }


    /**
     * @Route("/ajax/edit/user",  options={"expose"=true}, name="edituser")
     */
    public function editUserAction(Request $request, UserService $userService)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request;
        $user = $userService->userShow($data->get('id'));
        $pass = $user->getPassword();

        $service = $em->getRepository(Service::class)->findOneBy(['id' => $data->get('specialized')]);
        $form = $this->createForm(UserType::class, $user);
        $form->add('type');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setService($service);
            $userAdmin = $this->getUser();
            if ($userAdmin->getRoles() == USER::R_SUPER_ADMIN) {// if user is ADMIN
                if ($userAdmin) {
                    $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy(['id' => $request->request->get('company')]);
                    $user->setCompany($company);
                }
            }
            $userService->updateUser($user, $pass);

            return $this->json("Se actualizo ");
        } else {
            return $this->json('no se actulizado');
        }
    }

    /**
     * @Route("/admin-user-delete/{id}", name="ajax.admin.user.delete", options={"expose"=true})
     * @ParamConverter("user", class="App\Entity\User")
     */
    public function deleteUserAction(Request $request, User $user)
    {
        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('admin.user');
    }

}
