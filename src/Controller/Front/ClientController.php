<?php
/**
 * Created by Javier Orti 5-01-22
 */


namespace App\Controller\Front;


use App\Entity\User;
use App\Form\RequestType;
use App\Services\ClientService;
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

class ClientController extends AbstractController
{
    /**
     * @Route("/client/{id}", requirements={"id" = "^\d+$"}, name="front.client", options={"expose"=true})
     * @ParamConverter("user", class="App\Entity\User")
     * @template("Front/client/index.html.twig")
     */
    public function clientAction(Request $request, User $user, ClientService $clientService)
    {
        $taskClient = $clientService->taskClientUser($user);
        return ['user' => $user, 'task' => $taskClient];
    }

    /**
     * @Route("/request", name="front.client.request", options={"expose"=true})
     * @template("Front/client/request.html.twig")
     */

    public function requestAction()
    {
        $request= new \App\Entity\Request();
        $formRequest = $this->createForm(RequestType::class, $request);
        return ['formRequest'=>$formRequest->createView()];


    }
    /**
     * @Route("/ajax/new-request",  options={"expose"=true}, name="ajax.new.request")
     * @return Response
     */
    public function newRequestAjaxAction(Request $request): Response
    {

       $data= $request->request;
        return  $this->json($data);

    }


}