<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Services\TaskService;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class TaskController extends AbstractController
{
    /**
     * @var TaskService $taskService
     */
    private $taskService;


    /**
     * @Route("/task", name="admin.task")
     * @template("Admin/task/index.html.twig")
     */
    public function taskAction(Request $request, DataTableFactory $dataTableFactory)
    {


        $table = $dataTableFactory->create()
            ->add('title', TextColumn::class, ['label' => 'Titulo', 'className' => 'bold'])
            ->add('name', TextColumn::class, ['label' => 'Servicio', 'render' => function ($value, $context) {
                if ($context->getService()) {
                    $nameService = $context->getService()->getName();
                } else {
                    $nameService = 'Otros';
                }
                return sprintf($nameService);
            }])
            ->add('username', TextColumn::class, ['label' => 'Operario/s', 'render' => function ($value, $context) {
                $user = $context->getIduser()->toArray();
                if ($user) {
                    $nameUser = '<a  href="/admin-user-show/' . $user[0]->getId() . '" title="visualiza"><span>' . $user[0]->getUsername() . '</span></a>';
                } else {
                    $nameUser = "Sin asignar";
                }
                return sprintf($nameUser);
            }
            ])
            ->add('description', TextColumn::class, ['label' => 'Descripción', 'className' => 'bold'])
            ->add('material', TextColumn::class, ['label' => 'Material usado', 'className' => 'bold'])
            ->add('actions', TextColumn::class, ['label' => 'Opciones', 'orderable' => false, 'render' => function ($value, $context) {
                $id = $context->getId();
                $show = 'show<br>';
                $update = 'update<br>';
                $delete = 'delete';
                return sprintf('
                    <div class="text-center">' . $show . $update . $delete . '</div>');
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Task::class,

            ])->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return ['datatable' => $table];
    }

    /**
     * @Route("/ajax/task",  options={"expose"=true}, name="ajax.new.task")
     * @return Response
     */
    public function newTaskAjaxAction(Request $re): Response
    {
        $this->taskService = new TaskService($this->getDoctrine()->getManager());
        $task = new Task();
        /***
         * @var User $user
         */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $re->request->get('user')]);
        $service = $this->getDoctrine()->getRepository(Service::class)->findOneBy(['id' => $re->request->get('service')]);
        $task->addIduser($user);
        $task->setService($service);
        $user->addTask($task);
        $formTask = $this->createForm(TaskType::class, $task);
        $ta = $this->taskService->addTask($re, $formTask, $task);

        if ($ta) {
            return $this->json("Se ha grabado la tarea");
        } else {
            return $this->json('No se ha creado la nueva tarea');
        }
    }


    /**
     * @Route("/new-task", name="new.multi.task")
     * @template("Admin/task/new.html.twig")
     */
    public function newTask(Request $request)
    {
        $task = new Task();
        $services = $this->getDoctrine()->getRepository(Service::class)->findAll();
        /**
         * @var User $users
         */
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(['type' => 'operator']);
        if (count($services) == 0) {
            $infoTask = "No hay servicios agregados";
        } elseif (count($users) == 0) {
            $infoTask = "No existen operarios  agregados";
        } else {
            $infoTask = null;
        }
        $formTask = $this->createForm(TaskType::class, $task);

        return ['formTask' => $formTask->createView(), 'operators' => $users, 'services' => $services, 'infoTask' => $infoTask];
    }

    /**
     * @Route("/ajax/task/advanced",  options={"expose"=true}, name="ajax.new.advanced.task")
     * @return Response
     */
    public function newTaskAdvancedAjaxAction(Request $re): Response
    {
        $data = $re->request;
        return $this->json($data);

    }


    /**
     * @Route("/ajax/task/advanced/select/operator",  options={"expose"=true}, name="ajax.select.operators")
     * @return Response
     */
    public function newTaskAdvancedSelectOperatorAjaxAction(Request $re, SerializerInterface $serializer): Response
    {
        $data = $re->request;
        $operators = $this->getDoctrine()->getRepository(User::class)->findBy(['service' => $data->get('id')]);
        $json = $serializer->serialize(
            $operators,
            'json',
            ['groups' => 'show_user']
        );
        return $this->json(json_decode($json));

    }


}
