<?php

/**
 * Note: Service for Client
 * Created by: Javier Orti
 * Date: 05 - 01 - 2022
 */

namespace App\Services;


use App\Entity\Infoclient;
use App\Entity\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class ClientService
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

    public function infoClientUser($user)
    {
        return $this->em->getRepository(Infoclient::class)->findOneBy(['idUser' => $user->getId()]);
    }

    public function taskClientUser($user)
    {
        return $this->em->getRepository(Task::class)->findBy(['idClient' => $user->getId()]);
    }

    public function addRequest($description,$userClient){

        $newRequest= new \App\Entity\Request();
        $newRequest->setIdUser($userClient);
        $newRequest->setDescription($description);
        $newRequest->setCreatedAt(new \DateTime('now'));
        $this->em->persist($newRequest);
        $this->em->flush();

    }


}