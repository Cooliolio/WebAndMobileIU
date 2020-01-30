<?php

/**
 * Created by PhpStorm.
 * User: caglarcelikoz
 * Date: 14/10/2019
 * Time: 21:39
 */

namespace App\Tests\Model;

use App\Model\Connection;
use \App\Model\PDORoomModel;
use PHPUnit\Framework\TestCase;

class PDORoomModelTest extends TestCase
{
    private $connection;

    protected function setUp()
    {
        $user = 'root';
        $password = '';
        $database = 'test_webandmobile_api';
        $server = 'localhost:3306';
        $this->connection = new Connection("mysql:host=$server;dbname=$database", $user, $password);
        $this->connection->getPDO()->exec('DROP TABLE IF EXISTS rooms');
        $this->connection->getPDO()->exec('CREATE TABLE rooms (
                                                    id int(11) NOT NULL,
                                                    name varchar(255),
                                                    happinessScore int(11) NOT NULL,
                                                    reserved tinyint(1),
                                                    reservedBy varchar(255),

                                                    PRIMARY KEY (id)
                                                    )');

        $rooms = $this->providerRooms();
        foreach ($rooms as $room) {
            $id = $room['id'];
            $name = $room['name'];
            $happinessScore = $room['happinessScore'];
            $reserved = $room['reserved'];
            $reservedBy = $room['reservedBy'];
            $this->connection->getPDO()->exec(
                "INSERT INTO rooms (id, name, happinessScore, reserved, reservedBy) VALUES ($id, '$name', $happinessScore, $reserved, '$reservedBy')"
            );
        }
    }

    public function providerRooms()
    {
        return [
            ['id' => '1', 'name' => 'testname1', 'happinessScore' => '1000', 'reserved' => '1', 'reservedBy' => 'testnaam1'],
            ['id' => '2', 'name' => 'testname2', 'happinessScore' => '950', 'reserved' => '0', 'reservedBy' => ''],
            ['id' => '3', 'name' => 'testname3', 'happinessScore' => '1050', 'reserved' => '1', 'reservedBy' => 'testnaam2']
        ];
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testFindHappinessByName($id, $name, $happinessScore)
    {
        $roomModel = new PDORoomModel($this->connection);
        $expectedRoom = ['happinessScore' => $happinessScore];
        $actualRoom = $roomModel->findHappinessByName($name);

        $this->assertIsArray($actualRoom);
        $this->assertArrayHasKey('happinessScore', $actualRoom);
        $this->assertEquals($expectedRoom['happinessScore'], $actualRoom['happinessScore']);
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testAddHappinessScore_Happy($id, $name, $happinessScore)
    {
        $roomModel = new PDORoomModel($this->connection);
        $expectedRoom = ['happinessScore' => $happinessScore + 2];
        $actualRoom = $roomModel->addHappinessScore($name, 'happy');

        $this->assertIsArray($actualRoom);
        $this->assertArrayHasKey('happinessScore', $actualRoom);
        $this->assertEquals($expectedRoom['happinessScore'], $actualRoom['happinessScore']);
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testAddHappinessScore_SomewhatHappy($id, $name, $happinessScore)
    {
        $roomModel = new PDORoomModel($this->connection);
        $expectedRoom = ['happinessScore' => $happinessScore + 1];
        $actualRoom = $roomModel->addHappinessScore($name, 'somewhatHappy');

        $this->assertIsArray($actualRoom);
        $this->assertArrayHasKey('happinessScore', $actualRoom);
        $this->assertEquals($expectedRoom['happinessScore'], $actualRoom['happinessScore']);
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testAddHappinessScore_SomewhatUnhappy($id, $name, $happinessScore)
    {
        $roomModel = new PDORoomModel($this->connection);
        $expectedRoom = ['happinessScore' => $happinessScore - 1];
        $actualRoom = $roomModel->addHappinessScore($name, 'somewhatUnhappy');

        $this->assertIsArray($actualRoom);
        $this->assertArrayHasKey('happinessScore', $actualRoom);
        $this->assertEquals($expectedRoom['happinessScore'], $actualRoom['happinessScore']);
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testAddHappinessScore_Unhappy($id, $name, $happinessScore)
    {
        $roomModel = new PDORoomModel($this->connection);
        $expectedRoom = ['happinessScore' => $happinessScore - 2];
        $actualRoom = $roomModel->addHappinessScore($name, 'unhappy');

        $this->assertIsArray($actualRoom);
        $this->assertArrayHasKey('happinessScore', $actualRoom);
        $this->assertEquals($expectedRoom['happinessScore'], $actualRoom['happinessScore']);
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testAddHappinessScore_Empty($id, $name, $happinessScore)
    {
        $roomModel = new PDORoomModel($this->connection);
        $expectedRoom = ['happinessScore' => $happinessScore];
        $actualRoom = $roomModel->addHappinessScore($name, '');

        $this->assertIsArray($actualRoom);
        $this->assertArrayHasKey('happinessScore', $actualRoom);
        $this->assertEquals($expectedRoom['happinessScore'], $actualRoom['happinessScore']);
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testGetRoomsLowerThanHappinessScore($id, $name, $happinessScore)
    {
        $roomModel = new PDORoomModel($this->connection);
        $rooms = $roomModel->getRoomsLowerThanHappinessScore(1000);

        $this->assertIsArray($rooms);
        $this->assertEquals(1, count($rooms));
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testgetRoomByReservationStatus($status)
    {
        $roomModel = new PDORoomModel($this->connection);
        $rooms = $roomModel->getRoomByReservationStatus(1);

        $this->assertIsArray($rooms);
        $this->assertEquals(2, count($rooms));
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testGetRoomStatusById($id)
    {
        $roomModel = new PDORoomModel($this->connection);
        $rooms = $roomModel->GetRoomStatusById($id);

        $this->assertIsArray($rooms);
        $this->assertArrayHasKey('reserved', $rooms[0]);
        $this->assertArrayNotHasKey('id', $rooms[0]);
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testReserveRoomNotFull($id)
    {
        $roomModel = new PDORoomModel($this->connection);

        $expectedRoom = ['reservedBy' => 'reserverTest', 'reserved' => 1];
        $actualRoom = $roomModel->reserveRoom(2, 'reserverTest');


        $this->assertIsArray($actualRoom);
        $this->assertArrayHasKey('reserved', $actualRoom[0]);
        $this->assertArrayHasKey('reservedBy', $actualRoom[0]);
        $this->assertEquals($expectedRoom['reservedBy'], $actualRoom[0]['reservedBy']);
        $this->assertEquals($expectedRoom['reserved'], $actualRoom[0]['reserved']);
    }

    /** 
     * @dataProvider providerRooms()
     **/
    public function testReserveRoomFull()
    {
        $roomModel = new PDORoomModel($this->connection);

        $actualRoom = $roomModel->reserveRoom(1, 'reserverTest');

        $this->assertEquals('full', $actualRoom);
    }



    public function testValidateHappinessScoreNonNumeric()
    {
        $happinessScore = 'test';
        $roomModel = new PDORoomModel($this->connection);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given Happiness score is less than 0 or is not a number' . $happinessScore);
        $roomModel->validateHappinessScore($happinessScore);
    }

    public function testValidateHappinessScoreNegative()
    {
        $happinessScore = -1;
        $roomModel = new PDORoomModel($this->connection);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given Happiness score is less than 0 or is not a number' . $happinessScore);
        $roomModel->validateHappinessScore($happinessScore);
    }

    public function testValidateRoomNameNumeric()
    {
        $roomName = 111;
        $roomModel = new PDORoomModel($this->connection);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Roomname not valid: ' . $roomName);
        $roomModel->validateRoomName($roomName);
    }

    public function testValidateRoomNameLengthLessThanThree()
    {
        $roomName = 'ro';
        $roomModel = new PDORoomModel($this->connection);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Roomname not valid: ' . $roomName);
        $roomModel->validateRoomName($roomName);
    }
}
