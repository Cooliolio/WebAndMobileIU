<?php

namespace App\Model;
/**
 * Created by PhpStorm.
 * User: caglarcelikoz
 * Date: 24/09/2019
 * Time: 11:56
 */

interface RoomModel
{
    public function findHappinessByName($name);
    public function addHappinessScore($roomName,$happiness);
    public function getRoomsLowerThanHappinessScore($happinessScore);
    public function getRooms();
    public function getRoomById($id);

    //Uitbreiding
    public function getRoomByReservationStatus($status);
    public function getRoomStatusById($id);
    public function reserveRoom($roomid, $reservename);
}