<?php

namespace App\Controller\Admin;


use App\Entity\Company;
use App\Form\CompanyType;
use App\Services\ServiceCompany;
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
            ->add('logo',TextColumn::class,['label'=>'logo','render'=>function($value,$context){
                if($context->getLogo()){
                    $logo = ' <div class="text-center mt-3"><img  src="'.$context->getLogo().'" height="28" alt="CoolBrand"></div> ' ;
                }else{
                    $logo=null;
                }
                return $logo;
            }])
            ->add('name', TextColumn::class, ['label'=>'Empresa', 'className' => 'bold'])
            ->add('address', TextColumn::class, ['label'=>'Dirección', 'className' => 'bold'])
            ->add('email', TextColumn::class, ['label'=>'E-mail', 'className' => 'bold'])
            ->add('actions', TextColumn::class, ['label' => 'Opciones', 'orderable' => false, 'render' => function ($value, $context) {
                $id = $context->getId();
                $show = 'show<br>';
                $update = 'update<br>';
                $delete = 'delete';
                return sprintf('
                    <div class="text-center">' . $show . $update . $delete . '</div>');
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



}
