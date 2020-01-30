<?php

namespace App\Controller;


use App\Model\RoomModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    private $roomModel;

    public function __construct(RoomModel $roomModel)
    {
        $this->roomModel = $roomModel;
    }

    /**
     * @Route("/room/{id}", methods={"GET"}, name="getRoomById")
     */
    public function getRoomById($id)
    {
        $statuscode = 200;
        $rooms = null;
        try {
            $rooms = $this->roomModel->getRoomById($id);
            if ($rooms == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($rooms, $statuscode);
    }

    /**
     * @Route("/rooms", methods={"GET"}, name="getRooms")
     */
    public function getRooms()
    {
        $statuscode = 200;
        $rooms = null;
        try {
            $rooms = $this->roomModel->getRooms();
            if ($rooms == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($rooms, $statuscode);
    }
    /**
     * @Route("/room/happy/{name}", methods={"GET"}, name="findHappinessByName")
     */
    public function findHappinessByName($name)
    {
        $statuscode = 200;
        $happinessScore = null;
        try {
            $happinessScore = $this->roomModel->findHappinessByName($name);
            if ($happinessScore == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($happinessScore, $statuscode);
    }
    /**
     * @Route("/rooms", methods={"PUT"}, name="addHappinessScore")
     */
    public function addHappinessScore(Request $request)
    {
        $statuscode = 200;
        $happinessScore = null;
        try {
            $happinessScore = $this->roomModel->addHappinessScore($request->query->get('roomName'), $request->query->get('happiness'));
            if ($happinessScore == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($happinessScore, $statuscode);
    }
    /**
     * @Route("/rooms", methods={"GET"}, name="getRoomsLowerThanHappinessScore")
     */
    public function getRoomsLowerThanHappinessScore(Request $request)
    {
        $statuscode = 200;
        $room = null;
        try {
            $room = $this->roomModel->getRoomsLowerThanHappinessScore($request->query->get('happinessScore'));
            if ($room == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($room, $statuscode);
    }



    /**
     * @Route("/rooms/status/{status}", methods={"GET"}, name="getRoomByReservationStatus")
     */
    public function getRoomByReservationStatus($status)
    {
        $statuscode = 200;
        $rooms = null;
        try {
            $rooms = $this->roomModel->getRoomByReservationStatus($status);
            if ($rooms == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($rooms, $statuscode);
    }

    /**
     * @Route("/roomstatus/{id}", methods={"GET"}, name="getRoomStatusById")
     */
    public function getRoomStatusById($id)
    {
        $statuscode = 200;
        $rooms = null;
        try {
            $room = $this->roomModel->getRoomStatusById($id);
            if ($room == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($room, $statuscode);
    }

    /**
     * @Route("/room/reserve", methods={"PUT"}, name="reserveRoom")
     */
    public function reserveRoom(Request $request)
    {
        $statuscode = 200;
        $reservation = null;
        try {
            $reservation = $this->roomModel->reserveRoom($request->query->get('roomId'), $request->query->get('reserveName'));
            if ($reservation == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        return new JsonResponse($reservation, $statuscode);
    }

    
}
