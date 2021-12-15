<?php

/**
 * Note: Service for User
 * Created by: Javier Orti
 * Date: 29 - 11 - 2021
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


class ServiceService
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


    public function addService(Request $request, FormInterface $form, $service)
    {
        $data = $request->request;
        /**
         * @var Service $service
         */
        $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $data->get('company')]);
        $service->setCompany($company);
        $service->setCreatedAt(new \DateTime('now'));
        $service->setActive(1);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($service);
            $this->em->flush();
        }
        return $data;
    }

    public function stateService($id)
    {

        $service = $this->em->getRepository(Service::class)->findOneBy(['id' => $id]);
        if ($service->getActive()) {
            $service->setActive(0);
        } else {
            $service->setActive(1);
        }
        $service->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($service);
        $this->em->flush();
    }


}