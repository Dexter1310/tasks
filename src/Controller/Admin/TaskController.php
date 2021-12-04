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
            ->add('username', TextColumn::class, ['label' => 'Operario/s', 'render' => function ($value, $context) {
                /**
                 * @var Task $context
                 */
                $user = $context->getIduser()->toArray();
                if ($user) {
                    $nameUser = '<a  href="/admin-user-show/' . $user[0]->getId() . '" title="visualiza"><span>'.$user[0]->getUsername().'</span></a>';
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
                $show = '<a  href="/admin-user-show/' . $id . '" title="visualiza"><span style="color:green"><i class="bi bi-eye"></i></span></a>';
                $update = '<a  class="p-2" href="/admin-user-update/' . $id . '" title="Edita"><i class="bi bi-gear"></i></a>';
                $delete = ' <a  href="/admin-user-delete/' . $id . '" title="Elimina"><span style="color: red"><i class="bi bi-trash"></i></span></a>';
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
        $task->addIduser($user);
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
        $formTask = $this->createForm(TaskType::class, $task);

        return ['formTask' => $formTask->createView(), 'operators' => $users, 'services' => $services];
    }


}