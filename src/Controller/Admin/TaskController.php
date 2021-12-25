<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Service;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Services\TaskService;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
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
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Tests\Fixtures\AppBundle\AppBundle;
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
            ->add('company', TextColumn::class, ['label' => 'Home', 'render' => function ($valus, $context) {
                if ($context->getCompany()) {
                    $company = ' <div class="text-center mt-3"><img  src="' . $context->getCompany()->getLogo() . '" height="28" alt="CoolBrand"></div> ';
                } else {
                    $company = null;
                }
                return $company;
            }])
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
                    $nameUser = [];
                    foreach ($user as $us) {
                        $name = '<a style="color:green;" href="/admin-user-show/' . $us->getId() . '" title="visualiza"><span>' . $us->getUsername() . '</span></a>';
                        array_push($nameUser, $name . "</br>");
                    }
                } else {
                    $nameUser = "Sin asignar";
                }
                return $nameUser;
            }
            ])
            ->add('state', TextColumn::class, ['label' => 'Estado', 'render' => function ($value, $context) {
                $state = $context->getState();
                if ($state == 0) {
                    $state = '<span style="color:red">Pendiente</span>';
                } elseif ($state == 1) {
                    $state = '<span style="color:orange;">En proceso</span>';
                } elseif ($state == 2) {
                    $state = '<span style="color:blue;">Realizada</span>';
                } else {
                    $state = '<span style="color:green;">Verificada</span>';
                }
                return $state;
            }]);
        $table->createAdapter(ORMAdapter::class, [
            'entity' => Task::class,
            'query' => function (QueryBuilder $builder) {

                $builder
                    ->select(Task::ALIAS)
                    ->from(Task::class, Task::ALIAS);
                if ($this->getUser()->getType() != 'super') {
                    if ($this->getUser()->getType() == 'operator') { //TODO ONLY IF USER OPERATOR
                        $tasks = $this->getUser()->getTask()->toArray();
                        $builder->andWhere(Task::ALIAS . '.id IN (:valid)');
                        $id = [];
                        foreach ($tasks as $task) {
                            array_push($id, $task->getId());
                        }
                        $builder->setParameter('valid', $id);
                    }
                    $builder->andWhere(Task::ALIAS . '.company= :val')
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
        $task->setCompany($service->getCompany());
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
        if ($this->getUser()->getType() == 'super') {
            $company = $this->getDoctrine()->getRepository(Company::class)->findAll();
            $services = $this->getDoctrine()->getRepository(Service::class)->findAll();
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        } else {
            $company = null;
            $services = $this->getDoctrine()->getRepository(Service::class)->findBy(['company' => $this->getUser()->getCompany()]);
            $users = $this->getDoctrine()->getRepository(User::class)->findBy(['type' => 'operator', 'company' => $this->getUser()->getCompany()]);
        }

        if (count($services) == 0) {
            $infoTask = "No hay servicios agregados";
        } elseif (count($users) == 0) {
            $infoTask = "No existen operarios  agregados";
        } else {
            $infoTask = null;
        }
        $formTask = $this->createForm(TaskType::class, $task);

        return ['formTask' => $formTask->createView(), 'operators' => $users, 'services' => $services, 'infoTask' => $infoTask, 'company' => $company];
    }

    /**
     * @Route("/ajax/task/advanced",  options={"expose"=true}, name="ajax.new.advanced.task")
     * @return Response
     */
    public function newTaskAdvancedAjaxAction(Request $re): Response
    {
        $this->taskService = new TaskService($this->getDoctrine()->getManager());
        $data = $re->request;
        $service = $this->getDoctrine()->getRepository(Service::class)->findOneBy(['id' => $data->get('service')]);

        $operator = $data->get('operator');
        if (is_array($operator)) { //when is many opoerator task
            $task = new Task();
            foreach ($operator as $oper) {
                $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $oper]);
                $task->addIduser($user);
                $task->setService($service);
                if ($service) {
                    $task->setCompany($service->getCompany());
                } else {
                    $task->setCompany($this->getUser()->getCompany());
                }
                $user->addTask($task);
                $formTask = $this->createForm(TaskType::class, $task);
                $this->taskService->addTask($re, $formTask, $task);
            }
        } else { //when is one operator task
            $task = new Task();
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $operator]);
            $task->addIduser($user);
            $task->setService($service);
            if ($service) {
                $task->setCompany($service->getCompany());
            } else {
                $task->setCompany($this->getUser()->getCompany());
            }
            $user->addTask($task);
            $formTask = $this->createForm(TaskType::class, $task);
            $this->taskService->addTask($re, $formTask, $task);
        }
        return $this->json($data);
    }

    /**
     * @Route("/ajax/task/advanced/select/company",  options={"expose"=true}, name="ajax.select.company")
     * @return Response
     */
    public function newTaskAdvancedSelectCompanyAjaxAction(Request $re, SerializerInterface $serializer): Response
    {
        $data = $re->request;
        $service = $this->getDoctrine()->getRepository(Service::class)->findBy(['company' => $data->get('id')]);
        $json = $serializer->serialize(
            $service,
            'json',
            ['groups' => 'show_service']
        );
        return $this->json(json_decode($json));

    }

    /**
     * @Route("/ajax/task/advanced/select/operator",  options={"expose"=true}, name="ajax.select.operators")
     * @return Response
     */
    public function newTaskAdvancedSelectOperatorAjaxAction(Request $re, SerializerInterface $serializer): Response
    {
        $data = $re->request;
        $idService = $data->get('id');

        if ($idService) {
            $service = $this->getDoctrine()->getRepository(Service::class)->findOneBy(['id' => $idService]);
            $operators = $this->getDoctrine()->getRepository(User::class)->findBy(['service' => $idService, 'type' => 'operator', 'company' => $service->getCompany()]);
        } else {
            $operators = $this->getDoctrine()->getRepository(User::class)->findBy(['type' => 'operator', 'company' => $this->getUser()->getCompany()]);
        }

        $json = $serializer->serialize(
            $operators,
            'json',
            ['groups' => 'show_user']
        );
        return $this->json(json_decode($json));


    }

    /**
     * @Route("/admin/task/show/{id}", name="admin.task.show", options={"expose"=true})
     * @ParamConverter("task", class="App\Entity\Task")
     * @Template("Admin/task/show.html.twig")
     * @return array|RedirectResponse
     */
    public function showTaskAction(Request $request, Task $task, TaskService $taskService)
    {
        $time = 0;
        $timeTotal = null;
        if ($this->getUser()) {
            if ($task->getTime() != 0 && $task->getTimeEnd() != 0) {//calcul for hours execute task

                $horaInicio = Date($task->getTime());
                $horaTermino = Date($task->getTimeEnd());
                $total = $horaTermino - $horaInicio;
                $t = Date($total);
                $time = date(" H:i:s", strtotime('-1 hours', $t));
                if (($this->getUser()->getType() == 'operator' && $task->getState()!=3) || $task->getTimeTotal() == null ) {
                    $task->setTimeTotal($time);
                } else {
                    $time = $task->getTimeTotal();
                }

                if ($task->getTimeTotal() != null) {
                    $timeTotal = explode(':', $task->getTimeTotal());
                    $timeTotal[0] = (int)$timeTotal[0];//hour
                    $timeTotal[1] = (int)$timeTotal[1];//minute
                    $timeTotal[2] = (int)$timeTotal[2];//seconds
                }

            }
        }
        return ['task' => $task, 'time' => $time . 's.', 'timeTotal' => $timeTotal];
    }

    /**
     * @Route("/ajax/show/task",  options={"expose"=true}, name="ajax.admin.show.task")
     */
    public function showTaskAjaxAction(Request $request, TaskService $taskService)
    {
        $idTask = $request->request->get('id');
        $task = $taskService->showTask($idTask, $this->getUser());
        return $this->json($task->getId());

    }

    /**
     * @Route("/ajax/edit/time/task",  options={"expose"=true}, name="ajax.edit.time.task")
     */
    public function editTimeTaskAjaxAction(Request $request, TaskService $taskService)
    {
        $data = $request->request;
        /**
         * @var Task $task
         */
        $task = $taskService->showTask($data->get('id'), $this->getUser());
        $hour = (string)$data->get('time0');
        $minute = (string)$data->get('time1');
        $second = (string)$data->get('time2');
        $timeTotal = $hour . ':' . $minute . ':' . $second;
        $task->setTimeTotal($timeTotal);
        $taskService->updateTask($task);
        return $this->json($data);

    }


    /**
     * @Route("/ajax/edit/task/operator",  options={"expose"=true}, name="ajax.edit.task.operator")
     */
    public function editTaskOperatorAjaxAction(Request $request, TaskService $taskService)
    {
        $data = $request->request;
        $stateOper = $data->get('stateOper');
        $idTask = $data->get('id');
        $task = $taskService->showTask($idTask, $this->getUser());
        $task->setState($stateOper);
        $task->setDescription($data->get('description'));
        $task->setMaterial($data->get('material'));
        if ($stateOper == 0) {
            $task->setTime(0);
            $task->setTimeEnd(null);
            $task->setTimeTotal(null);
        }

        if ($stateOper == 1) { //en proceso
            $task->setTime(strtotime("now"));
            $task->setTimeEnd(0);
        }
        if ($stateOper == 2) {// realizado
            $task->setTimeEnd(strtotime("now"));
        }
        $taskService->updateTask($task);
        return $this->json($idTask);

    }

    /**
     * @Route("/admin-task-delete/{id}", name="admin.task.delete", options={"expose"=true})
     * @ParamConverter("task", class="App\Entity\Task")
     */
    public function deleteTaskAction(Request $request, Task $task, TaskService $taskService)
    {
        $taskService->deleteTask($task);
        return $this->redirectToRoute('admin.task');
    }


}
