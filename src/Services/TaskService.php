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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\AbstractUnicodeString;
use Symfony\Component\String\Slugger\SluggerInterface;

class TaskService
{
    /**
     * @var EntityManagerInterface $em
     */

    private $em;

    /**
     * @var SluggerInterface $slugger
     */
    private $slugger;

    /**
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     */
    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->slugger = $slugger;
    }


    public function addTask(Request $request, FormInterface $form, Task $task)
    {

        $task->setState('Pendiente');
        $task->setPeriodic($request->request->get('period'));
        $client = $this->em->getRepository(User::class)->findOneBy(['id' => $request->request->get('client')]);//Search client
        $task->setIdClient($client);
        $task->setTime(0);
        $task->setCreatedAt(new \DateTime('now'));
        $task->setUpdatedAt(new \DateTime('now'));
        $task->setViewOperator(false);
        $data = $request->request;
        $form->handleRequest($request);
        $this->setTargetDirectory('uploads/images');//TODO IMAGE TASK
        $nameImdage = $this->upload($form['imgTask']->getData());
        $task->setImgTask($nameImdage);

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
        $this->checkFile('../public/uploads/images/', $task->getImgTask());
        $this->em->remove($task);
        $this->em->flush();

    }

    public function clientsCompany($company)
    {
        $client = $this->em->getRepository(User::class)->findBy(['company' => $company, 'type' => 'client']);
        return $client;

    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function checkFile($path, $file)
    {
        if (file_exists($path . $file) && $file != null) {
            dump('existe');
            unlink($path . $file);
        }

    }

    /**
     * @return mixed
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * @param mixed $targetDirectory
     */
    public function setTargetDirectory($targetDirectory): void
    {
        $this->targetDirectory = $targetDirectory;
    }


}
