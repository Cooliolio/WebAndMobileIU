<?php

namespace App\Controller;

use App\Model\TicketModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TicketController extends AbstractController
{
    private $ticketModel;
    
    public function __construct(TicketModel $ticketModel)
    {
        $this->ticketModel = $ticketModel;
    }

    /**
     * @Route("/tickets/{id}", methods={"GET"}, name="getTicketById")
     */
    public function findTicketById($id)
    {
        $statuscode = 200;
        $ticket = null;
        try {
            $ticket = $this->ticketModel->findticketById($id);
            if ($ticket == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
            return new JsonResponse($exception->getmessage(), $statuscode);
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($ticket, $statuscode);
    }

    /**
     * @Route("/tickets", methods={"PUT"}, name="voteForTicket")
     */
    public function voteForTicket(Request $request)
    {
        $statuscode = 200;
        $numberOfVotes=null;
        try {
            $numberOfVotes=$this->ticketModel->voteForTicket($request->query->get('id'));
            if ($numberOfVotes == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($numberOfVotes, $statuscode);

    }


    
}
