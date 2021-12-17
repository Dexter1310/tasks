<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Service;
use App\Entity\User;
use App\Form\ServiceType;
use App\Form\UserType;
use App\Services\ServiceService;
use App\Services\UserService;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ServiceController extends AbstractController
{
    /**
     * @Route("/service", name="service")
     * @template("Admin/service/index.html.twig")
     */
    public function serviceAction(Request $request, DataTableFactory $dataTableFactory)
    {
        $table = $dataTableFactory->create()
            ->add('company', TextColumn::class, ['label' => 'Empresa', 'render' => function ($value, $context) {
                $company = ' <img style="float: right;" src="' . $context->getCompany()->getLogo() . '" height="28" alt="CoolBrand"> ' . '<small >' . $context->getCompany()->getName() . '</small>';
                return $company;
            }])
            ->add('name', TextColumn::class, ['label' => 'Servicio', 'className' => 'bold'])
            ->add('active', TextColumn::class, ['label' => 'Estado', 'render' => function ($value, $context) {
                $id = $context->getId();
                if ($context->getActive() == 1) {
                    $state = " <button title='Desactivar' onClick='confirStateService(" . $id . ")'> <span style='color: green;'><i class='bi bi-circle-fill'></i></span></button>";
                } else {
                    $state = "    <button title='Activar' onClick='confirStateService(" . $id . ")'>  <span style='color: red;'><i class='bi bi-circle-fill'></i></span></button> ";
                }
                return sprintf($state);
            }])
            ->add('actions', TextColumn::class, ['label' => 'Opciones', 'orderable' => false, 'render' => function ($value, $context) {
                $id = $context->getId();
                $show = '<a  href="/admin-service-show/' . $id . '" title="visualiza"><span style="color:green"><i class="bi bi-eye"></i></span></a>';
                $update = '<a  class="p-2" href="/admin-service-update/' . $id . '" title="Edita"><i class="bi bi-gear"></i></a>';
                $delete = '<a href="/admin-service-delete/' . $id . '" title="Elimina"><span style="color: red"><i class="bi bi-trash"></i></span></a>';
                return sprintf(
                    '
                    <div class="text-center">
      ' . $show . $update . $delete . '
      </div>
');
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Service::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select(Service::ALIAS)
                        ->from(Service::class, Service::ALIAS);
                    if ($this->getUser()->getType() != 'super') {
                        $builder->andWhere(Service::ALIAS . '.company = :val')
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
     * @Route("/new-service", name="newservice")
     * @template("Admin/service/new.html.twig")
     */
    public function newServiceAction(Request $request)
    {
        $service = new Service();
        $company = $this->getDoctrine()->getRepository(Company::class)->findAll();
        $formUser = $this->createForm(ServiceType::class, $service);//todo: if new service added. this is your form
        return ['formService' => $formUser->createView(), 'company' => $company];
    }


    /**
     * @Route("/ajax/service",  options={"expose"=true}, name="ajax.new.service")
     * @return Response
     */
    public function newServiceAjaxAction(Request $re): Response
    {
        $this->serviceService = new ServiceService($this->getDoctrine()->getManager());
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $us = $this->serviceService->addService($re, $form, $service);
        if ($us) {
            return $this->json("Se grabo el servicio");
        } else {
            return $this->json("No se grabo el servicio");
        }
    }

    /**
     * @Route("/admin-service-show/{id}", name="admin.service.show", options={"expose"=true})
     * @ParamConverter("service", class="App\Entity\Service")
     * @Template("Admin/service/show.html.twig")
     * @return array|RedirectResponse
     */
    public function showServiceAction(Request $request, Service $service)
    {
        return ['service' => $service];
    }


    /**
     * @Route("/admin-service-update/{id}", name="admin.service.update", options={"expose"=true})
     * @Template("Admin/service/edit.html.twig")
     * @ParamConverter("service", class="App\Entity\Service")
     * @return array|RedirectResponse
     */
    public function updateServiceAction(Request $request, Service $service)
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        return ['formService' => $form->createView(), 'service' => $service];
    }


    /**
     * @Route("/ajax/edit/service",  options={"expose"=true}, name="editservice")
     */
    public function editServiceAction(Request $request, ServiceService $fileUploader)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request;
        $name = $data->get('service')['name'];
        $service = $em->getRepository(Service::class)->findOneBy(['id' => $data->get('id')]);//query for company
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var Service $service
             */
            $service->setName($name);
            $service->setUpdatedAt(new \DateTime('now'));
            $em->persist($service);
            $em->flush();
            return $this->json("Se actualizo " . $data->get('service')['name']);
        }
    }

    /**
     * @Route("/admin-service-delete/{id}", name="ajax.admin.service.delete", options={"expose"=true})
     * @ParamConverter("service", class="App\Entity\Service")
     */
    public function deleteSerAction(Request $request, Service $service)
    {
        $userService = $this->getDoctrine()->getRepository(User::class)->findBy(['service' => $service->getId()]);
        if (!$userService) { //Service will be deleted if there is no associated user
            $this->getDoctrine()->getManager()->remove($service);
            $this->getDoctrine()->getManager()->flush();
        } else {
            $this->addFlash('warning', 'No se puede eliminar el servicio seleccionado, existe relación asociada con varios o un usuario/s.');
        }
        return $this->redirectToRoute('service');
    }


    /**
     * @Route("/ajax/state/service",  options={"expose"=true}, name="ajax.state.service")
     */
    public function stateServiceAction(Request $request, ServiceService $serviceService)
    {

        $data = $request->request;
        $serviceService->stateService($data->get('id'));
        return $this->redirectToRoute('service');
    }

}