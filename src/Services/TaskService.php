<?php

/**
 * Note: Service for User
 * Created by: Javier Orti
 * Date: 29 - 11 - 2021
 */

namespace App\Services;

use App\Entity\Task;
use App\Entity\User;
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

class TaskService
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }


    public function addTask(Request $request, FormInterface $form, Task $task)
    {
        $task->setState('Pendiente');
        $task->setPeriodic($request->request->get('period'));
        $task->setTime(0);
        $task->setCreatedAt(new \DateTime('now'));
        $task->setViewOperator(false);
        $data = $request->request;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($task);
            $this->em->flush();
        }
        return $data;
    }

    public function updateTask($task)
    {
        /**
         * @var Task $task
         */
        $task->updatedTimestamps();
        $this->em->persist($task);
        $this->em->flush();
    }

    public function showTask($idTask, $user)
    {
        /**
         * @var Task $task
         */
        $task = $this->em->getRepository(Task::class)->findOneBy(['id' => $idTask]);
        if ($task->getViewOperator() == 0 && $user->getType() == 'operator') {
            $task->setViewOperator(1);
            $this->updateTask($task);
        }

        return $task;
    }

    public function deleteTask($task)
    {
        $this->em->remove($task);
        $this->em->flush();

    }


//    public function taskPeriodic($user)
//    {
//        $tasks = $user->getTask()->toArray();// created array user's tasks
//        $dataNoW = new \DateTime('now');//data actuality
//        foreach ($tasks as $task) {
//            /**
//             * @var Task $task
//             */
//            if ($task->getPeriodic() != null && ($task== end($tasks))) {//if task is  periodic and element last
//                $fechaCreated = $task->getCreatedAt();
//                if($task->getPeriodic()==1){ //todo: each day
//                    date_add($fechaCreated, date_interval_create_from_date_string("1 day"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    date_add($dataNoW, date_interval_create_from_date_string("1 day"));
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    if ($dataNoW == $fechaCreated) {
//                            $users=$task->getIduser()->toArray();
//                        $newTask= new Task();
//                        $newTask->setTitle($task->getTitle());
//                        $newTask->setMaterial($task->getMaterial());
//                        $newTask->setDescription($task->getDescription());
//                        $newTask->setService($task->getService());
//                        $newTask->setState(0);
//                        $newTask->setTime(0);
//                        $newTask->setViewOperator(0);
//                        $newTask->setCompany($task->getCompany());
//                        $newTask->setCreatedAt(new \DateTime('now'));
//                        $newTask->setPeriodic($task->getPeriodic());
//                        foreach ($users as $user){
//                            $newTask->addIduser($user);
//                        }
//                        $this->em->persist($newTask);
//                        $this->em->flush();
//                    } else {
//                        dump('No son iguales');
//                        die();
//                    }
//                }
//
//
//            }
//
//        }
//
//
//    }


}
