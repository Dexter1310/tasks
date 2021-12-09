<?php

/**
 * Note: Service for Company
 * Created by: Javier Orti
 * Date: 09 - 12 - 2021
 */

namespace App\Services;


use App\Entity\Company;
use App\Entity\Service;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class ServiceCompany
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

    public function addCompany(Request $request, FormInterface $form, Company $company)
    {
        $data = $request->request;
        $form->handleRequest($request);
        $company->setCreatedAt(new \DateTime('now'));//created  in date today now
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($company);
            $this->em->flush();
        }
        return $data;
    }

    public function updateTask($company)
    {
        /**
         * @var Company $task
         */
        $this->em->persist($company);
        $this->em->flush();
    }


}