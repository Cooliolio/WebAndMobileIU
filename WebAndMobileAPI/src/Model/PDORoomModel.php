<?php

namespace App\Model;

/**
 * Created by PhpStorm.
 * User: caglarcelikoz
 * Date: 24/09/2019
 * Time: 10:29
 */
class PDORoomModel implements RoomModel
{

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getRoomById($id)
    {

        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT * FROM rooms WHERE id = :id ");
        $statement->bindParam(':id', $id, \PDO::PARAM_STR);
        $statement->execute();
        $room = null;
        $room = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $room;
    }

    public function getRooms()
    {

        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT * FROM rooms");
        $statement->execute();
        $rooms = null;
        $rooms = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $rooms;
    }

    public function findHappinessByName($name)
    {
        $pdo = $this->connection->getPDO();
        $happinessScore = null;
        $statement = $pdo->prepare("SELECT * FROM rooms WHERE name=:name");
        $statement->bindParam(':name', $name, \PDO::PARAM_STR);
        $statement->execute();
        $statement->bindColumn(2, $name, \PDO::PARAM_STR);
        $statement->bindColumn(3, $happinessScore, \PDO::PARAM_INT);

        if ($statement->fetch(\PDO::FETCH_BOUND)) {
            $happinessScore = ['happinessScore' => $happinessScore];
        }
        return $happinessScore;
    }


    public function addHappinessScore($roomName, $happiness)
    {
        $this->validateRoomName($roomName);
        $score = $this->findHappinessByName($roomName)['happinessScore'];

        switch ($happiness) {
            case "happy":
                $score += 2;
                break;
            case "somewhatHappy":
                $score += 1;
                break;
            case "somewhatUnhappy":
                $score -= 1;
                break;
            case "unhappy":
                $score -= 2;
                break;
            default:
                break;
        }
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("UPDATE rooms SET happinessScore=:hapinessScore WHERE name=:roomName");
        $statement->bindParam(':hapinessScore', $score, \PDO::PARAM_INT);
        $statement->bindParam(':roomName', $roomName, \PDO::PARAM_STR);
        $statement->execute();

        return $this->findHappinessByName($roomName);
    }

    public function getRoomsLowerThanHappinessScore($happinessScore)
    {
        $this->validateHappinessScore($happinessScore);

        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT * FROM ROOMS WHERE happinessScore < :happinessScore");
        $statement->bindParam(':happinessScore', $happinessScore, \PDO::PARAM_INT);
        $statement->execute();
        $roomsResult = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $roomsResult;
    }


    //Uitbreiding GET
    public function getRoomByReservationStatus($status)
    {
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT * FROM rooms WHERE reserved = :status ");
        $statement->bindParam(':status', $status, \PDO::PARAM_STR);
        $statement->execute();
        $rooms = null;
        $rooms = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $rooms;
    }

    public function getRoomStatusById($id)
    {
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT reserved FROM rooms WHERE id = :id ");
        $statement->bindParam(':id', $id, \PDO::PARAM_STR);
        $statement->execute();
        $room = null;
        $room = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $room;
    }

    //Uitbreiding PUT
    public function reserveRoom($roomid, $reservename)
    {
        $pdo = $this->connection->getPDO();
        $reservedCheck = $this->getRoomStatusById($roomid);
        if ($reservedCheck[0]['reserved'] == 1) {
            return "full";
        }
        $statement = $pdo->prepare("UPDATE rooms SET reservedBy = :reservedBy, reserved = 1  WHERE id = :roomid");
        $statement->bindParam(':roomid', $roomid, \PDO::PARAM_INT);
        $statement->bindParam(':reservedBy', $reservename, \PDO::PARAM_STR);
        $statement->execute();
        $room = $this->getRoomById($roomid);
        return $room;
    }

    public function validateHappinessScore($happinessScore)
    {
        if ($happinessScore < 0 || !(is_numeric($happinessScore))) {
            throw new \InvalidArgumentException('Given Happiness score is less than 0 or is not a number' . $happinessScore);
        }
    }

    public function validateRoomName($roomName)
    {
        if (!(is_string($roomName)) || (strlen($roomName) < 3)) {
            throw new \InvalidArgumentException('Roomname not valid: ' . $roomName);
        }
    }
}
