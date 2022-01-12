<?php

/**
 * Note: Request for Admin
 * Created by: Javier Orti
 * Date: 12 - 01 - 2022
 */

namespace App\Services;


use App\Entity\Infoclient;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class RequestService
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

    public function allRequest($company)
    {
        $requests = $this->em->getRepository(\App\Entity\Request::class)->findBy(['idCompany' => $company]);

        return $requests;

    }

    public function allRequestClient($company,$user)
    {
        $requests = $this->em->getRepository(\App\Entity\Request::class)->findBy(['idCompany' => $company,'idUser'=>$user]);

        return $requests;

    }


    public function allClients($company)
    {
        $clients = $this->em->getRepository(User::class)->findBy(['company' => $company, 'type' => 'client']);
        return $clients;
    }


}