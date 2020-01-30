<?php

namespace App\Controller;

use App\Model\AssetModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class AssetController extends AbstractController
{
    public function __construct(AssetModel $assetModel)
    {
        $this->assetModel = $assetModel;
    }

    /**
     * @Route("/asset", methods={"PUT"}, name="addTicketForAssetName")
     */
    public function addTicketForAssetName(Request $request){
        $statuscode = 200;
        $ticket=null;
        try{
            $ticket = $this->assetModel->addTicketForAssetName($request->query->get('name'),$request->query->get('description'));
            if ($ticket == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($ticket, $statuscode);
    }

    /**
     * @Route("/assets/{roomId}", methods={"GET"}, name="getAssetsByRoomId")
     */
    public function getAssetsByRoomId($roomId)
    {
        $statuscode = 200;
        $assets = null;
        try {
            $assets = $this->assetModel->getAssetsByRoomId($roomId);
            if ($assets == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($assets, $statuscode);
    }

    /**
     * @Route("/asset/tickets/{name}", methods={"GET"}, name="findTicketsByAssetName")
     */
    public function findTicketsByAssetName($name){
        $statuscode = 200;
        $tickets = null;
        try {
            $tickets = $this->assetModel->findTicketsByAssetName($name);
            if ($tickets == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($tickets, $statuscode);
    }

    

}
