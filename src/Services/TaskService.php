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


}
