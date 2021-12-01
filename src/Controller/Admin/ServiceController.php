<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Entity\User;
use App\Form\ServiceType;
use App\Form\UserType;
use App\Services\ServiceService;
use App\Services\UserService;
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
            ->add('name', TextColumn::class, ['label' => 'Servicio', 'className' => 'bold'])
            ->add('active', TextColumn::class, ['label' => 'Activo', 'className' => 'bold'])
            ->add('actions', TextColumn::class, ['label' => 'Opciones', 'orderable' => false, 'render' => function ($value, $context) {
                $id = $context->getId();
                return sprintf(
                    '
                    <div class="text-center">
      <a  href="/admin-service-show/' . $id . '" title="visualiza"><span style="color:green"><i class="bi bi-eye"></i></span></a>
      <a  class="p-2" href="/admin-service-update/' . $id . '" title="Edita"><i class="bi bi-gear"></i></a>
      <a class="deleteService" id="' . $id . '" href="#" title="Elimina"><span style="color: red"><i class="bi bi-trash"></i></span></a>
      </div>
');
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Service::class,
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
        $formUser = $this->createForm(ServiceType::class, $service);//todo: if new service added. this is your form
        return ['formService' => $formUser->createView()];
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
    public function showServiceAction(Request $request,Service $service)
    {
        return ['service'=>$service];
    }


    /**
     * @Route("/admin-service-delete", name="ajax.admin.service.delete", options={"expose"=true})
     */
    public function deleteServiceAction(Request $request)
    {
        $id=$request->request->get('id');
        $service=$this->getDoctrine()->getRepository(Service::class)->findOneBy(['id'=>$id]);
        $this->getDoctrine()->getManager()->remove($service);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($id);
    }



    /**
     * @Route("/admin-service-update/{id}", name="admin.service.update", options={"expose"=true})
     * @Template("Admin/service/edit.html.twig")
     * @ParamConverter("service", class="App\Entity\Service")
     * @return array|RedirectResponse
     */
    public function updateServiceAction(Request $request,Service $service)
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        return ['formService' => $form->createView(),'service'=>$service];
    }


    /**
     * @Route("/ajax/edit/service",  options={"expose"=true}, name="editservice")
     */
    public function editServiceAction(Request $request,ServiceService  $fileUploader)
    {

        $em=$this->getDoctrine()->getManager();
        $data=$request->request;
        $name=$data->get('service')['name'];
        $service=$em->getRepository(Service::class)->findOneBy(['id'=>$data->get('id')]);//query for company
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
            return $this->json("Se actualizo ".$data->get('service')['name']);
        }
    }


}