<?php

//Todo : created Javier Ortí 26-12-2021

namespace App\Command;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PeriodicTasks extends Command
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
        parent::__construct();
    }

    protected function configure()
    {
        error_log(print_r('test', true), 3, "/tmp/error.log");

        $this->setName('app:task-periodic')
            ->setDescription('Execute when auction is end.')
            ->setHelp("Task periodics users");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $tasks = $this->em->getRepository(Task::class)->findAll();
        foreach ($tasks as $task) {
            /**
             * @var Task $task
             */
            if ($task->getPeriodic() != null && (end($tasks)) ) {//if task is  periodic and element last
                $fechaCreated = $task->getCreatedAt();
                if ($task->getPeriodic() == 1 && (end($tasks))) { //todo: each day
                    $dataNoW = new \DateTime('now');//data actuality
                    date_add($fechaCreated, date_interval_create_from_date_string("1 day"));
                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
                    date_add($dataNoW, date_interval_create_from_date_string("1 day"));
                    $dataNoW = date_format($dataNoW, 'd-m-Y');
                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);

                }

                if ($task->getPeriodic() == 2&& (end($tasks))) { //todo: each week
                    $dataNoW = new \DateTime('now');//data actuality
                    date_add($fechaCreated, date_interval_create_from_date_string("1 week"));
                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
                    date_add($dataNoW, date_interval_create_from_date_string("1 week"));
                    $dataNoW = date_format($dataNoW, 'd-m-Y');
                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);

                }
            }

//                if ($task->getPeriodic() == 2) { //todo: each week
//                    dump($task->getPeriodic());die();
//                    date_add($fechaCreated, date_interval_create_from_date_string("1 week"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    date_add($dataNoW, date_interval_create_from_date_string("1 week"));
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);
//                }
//
//                if ($task->getPeriodic() == 3) { //todo: two  weeks
//                    date_add($fechaCreated, date_interval_create_from_date_string("2 week"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);
//                }
//
//                if ($task->getPeriodic() == 4) { //todo:  each month
//                    date_add($fechaCreated, date_interval_create_from_date_string("1 month"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);
//                }
//
//                if ($task->getPeriodic() == 5) { //todo: 3 month
//                    date_add($fechaCreated, date_interval_create_from_date_string("3 month"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);
//                }
//
//                if ($task->getPeriodic() == 6) { //todo: 6 month
//                    date_add($fechaCreated, date_interval_create_from_date_string("6 month"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);
//                }
//
//                if ($task->getPeriodic() == 7) { //todo: 9 month
//                    date_add($fechaCreated, date_interval_create_from_date_string("9 month"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);
//                }
//
//                if ($task->getPeriodic() == 8) { //todo: each  year
//                    date_add($fechaCreated, date_interval_create_from_date_string("1 year"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);
//                }
//
//                if ($task->getPeriodic() == 9) { //todo: 2  year
//                    date_add($fechaCreated, date_interval_create_from_date_string("2 year"));
//                    $fechaCreated = date_format($fechaCreated, 'd-m-Y');
//                    $dataNoW = date_format($dataNoW, 'd-m-Y');
//                    $this->createTaskPeriodic($dataNoW, $fechaCreated, $task);
//                }




            // outputs multiple lines to the console (adding "\n" at the end of each line)
            $output->writeln([
                'Periodic tasks execute success',
                '============',
                '',
            ]);



        }


        return Command::SUCCESS;

    }

    function createTaskPeriodic($dataNoW, $fechaCreated, $task)
    {
        if ($dataNoW == $fechaCreated) {
            $users = $task->getIduser()->toArray();
            $newTask = new Task();
            $newTask->setTitle($task->getTitle());
            $newTask->setMaterial($task->getMaterial());
            $newTask->setDescription($task->getDescription());
            $newTask->setService($task->getService());
            $newTask->setState(0);
            $newTask->setTime(0);
            $newTask->setViewOperator(0);
            $newTask->setCompany($task->getCompany());
            $newTask->setCreatedAt(new \DateTime('now'));
            $newTask->setPeriodic($task->getPeriodic());
            foreach ($users as $user) {
                $newTask->addIduser($user);
            }
            $this->em->persist($newTask);
            $this->em->flush();
        }
    }

}