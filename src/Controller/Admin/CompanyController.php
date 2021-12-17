<?php

namespace App\Controller\Admin;


use App\Entity\Company;
use App\Form\CompanyType;
use App\Services\ServiceCompany;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class CompanyController extends AbstractController
{
    /**
     * @var ServiceCompany $companyService
     */
    private $companyService;


    /**
     * @Route("/company", name="admin.company")
     * @template("Admin/company/index.html.twig")
     */
    public function companyAction(Request $request, DataTableFactory $dataTableFactory)
    {
        $table = $dataTableFactory->create()
            ->add('logo', TextColumn::class, ['label' => 'logo', 'render' => function ($value, $context) {
                if ($context->getLogo()) {
                    $logo = ' <div class="text-center mt-3"><img  src="' . $context->getLogo() . '" height="28" alt="CoolBrand"></div> ';
                } else {
                    $logo = null;
                }
                return $logo;
            }])
            ->add('name', TextColumn::class, ['label' => 'Empresa', 'className' => 'bold'])
            ->add('active', TextColumn::class, ['label' => '', 'className' => 'bold state-company', 'render' => function ($value, $context) {
                $id = $context->getId();
                if ($context->isActive() == 1) {

                    return " <button title='Desactivar' onClick='confirState(" . $id . ")'> <span style='color: green;'><i class='bi bi-circle-fill'></i></span></button>";
                } else {
                    return "    <button title='Activar' onClick='confirState(" . $id . ")'>  <span style='color: red;'><i class='bi bi-circle-fill'></i></span></button> ";
                }
            }])
            ->add('address', TextColumn::class, ['label' => 'Dirección', 'className' => 'bold address-company'])
            ->add('email', TextColumn::class, ['label' => 'E-mail', 'className' => 'bold email-company'])
            ->add('actions', TextColumn::class, ['label' => 'Opciones', 'orderable' => false, 'render' => function ($value, $context) {
                $id = $context->getId();
                $show = '<a  href="/admin-company-show/' . $id . '" title="visualiza"><span style="color:green"><i class="bi bi-eye"></i></span></a>';
                $update = '<a  class="p-2" href="/admin-company-update/' . $id . '" title="Edita"><i class="bi bi-gear"></i></a>';
                return sprintf('
                    <div class="text-center">' . $show . $update . '</div>');
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Company::class,
            ])->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return ['datatable' => $table];

    }

    /**
     * @Route("/new-company", name="newcompany")
     * @template("Admin/company/new.html.twig")
     */
    public function newCompanyAction(Request $request)
    {
        $company = new Company();
        $formCompany = $this->createForm(CompanyType::class, $company);//todo: if new company added. this is your form
        return ['formCompany' => $formCompany->createView()];
    }


    /**
     * @Route("/ajax/company",  options={"expose"=true}, name="ajax.new.company")
     * @return Response
     */
    public function newCompanyAjaxAction(Request $re): Response
    {
        $this->companyService = new ServiceCompany($this->getDoctrine()->getManager());
        $company = new Company();
        $formTask = $this->createForm(CompanyType::class, $company);
        $com = $this->companyService->addCompany($re, $formTask, $company);
        if ($com) {
            return $this->json("Se ha grabado la empresa");
        } else {
            return $this->json('No se ha creado la nueva empresa');
        }
    }

    /**
     * @Route("/admin-company-show/{id}", name="admin.company.show", options={"expose"=true})
     * @ParamConverter("company", class="App\Entity\Company")
     * @Template("Admin/company/show.html.twig")
     * @return array|RedirectResponse
     */
    public function showCompanyAction(Request $request, Company $company)
    {
        return ['company' => $company];
    }


    /**
     * @Route("/admin-company-update/{id}", name="admin.company.update", options={"expose"=true})
     * @Template("Admin/company/edit.html.twig")
     * @ParamConverter("company", class="App\Entity\Company")
     * @return array|RedirectResponse
     */
    public function updateCompanyAction(Request $request, Company $company)
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
        return ['formCompany' => $form->createView(), 'company' => $company];
    }

    /**
     * @Route("/ajax/edit/company",  options={"expose"=true}, name="editcompany")
     */
    public function editServiceAction(Request $request, ServiceCompany $companyService)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request;
        $name = $data->get('company')['name'];
        $company = $em->getRepository(Company::class)->findOneBy(['id' => $data->get('id')]);//query for company
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var Company $company
             */
            $company->setName($name);
            $company->setUpdatedAt(new \DateTime('now'));
            $em->persist($company);
            $em->flush();
            return $this->json("Se actualizo " . $data->get('company')['name']);
        }

    }


    /**
     * @Route("/ajax/state/company",  options={"expose"=true}, name="ajax.state.company")
     */
    public function stateCompanyAction(Request $request, ServiceCompany $serviceCompany)
    {

        $data = $request->request;
        $serviceCompany->stateCompany($data->get('id'));
        return $this->redirectToRoute('admin.company');
    }

}
