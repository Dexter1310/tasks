<?php
/**
 * Created by Javier Orti 12-01-2022
 */


namespace App\Controller\Admin;


use App\Entity\User;
use App\Form\RequestType;
use App\Services\ClientService;
use App\Services\RequestService;
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

class RequestController extends AbstractController
{

    /**
     * @Route("/request-admin", name="admin.request",options={"expose"=true})
     * @template("Admin/request/index.html.twig")
     */

    public function requestClientsAdminAction(Request $request,RequestService $requestService)
    {
        if ($request->isMethod('POST')) {
            $requests = $requestService->allRequestClient($this->getUser()->getCompany(), $request->request->get('client'));
        }else{
            $requests = $requestService->allRequest($this->getUser()->getCompany());
        }

        $clients = $requestService->allClients($this->getUser()->getCompany());
        return ['requests' => $requests, 'clients' => $clients];

    }




}