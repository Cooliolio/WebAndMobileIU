<?php

namespace App\Tests\Model;
use App\Model\Connection;
use \App\Model\PDOTicketModel;
use PHPUnit\Framework\TestCase;

class PDOTicketModelTest extends TestCase
{
    private $connection;

    protected function setUp()
    {
        $user = 'root';
        $password = '';
        $database = 'test_webandmobile_api';
        $server = 'localhost:3306';
        $this->connection = new Connection("mysql:host=$server;dbname=$database", $user, $password);
        $this->connection->getPDO()->exec('DROP TABLE IF EXISTS tickets');
        $this->connection->getPDO()->exec('CREATE TABLE tickets (
                                                    id int(11) NOT NULL,
                                                    assetId int(11) NOT NULL,
                                                    numberOfVotes int(11) NOT NULL,
                                                    description TEXT,
                                                    PRIMARY KEY (id)
                                                    )');

        $tickets = $this->providerTickets();
        foreach ($tickets as $ticket) {
            $id = $ticket['id'];
            $assetId = $ticket['assetId'];
            $numberOfVotes = $ticket['numberOfVotes'];
            $description = $ticket['description'];
            $this->connection->getPDO()->exec(
                "INSERT INTO tickets (id, assetId, numberOfVotes, description) VALUES ($id, $assetId, $numberOfVotes, '$description')"
            );
        }
    }

    public function providerTickets()
    {
        return [
                   ['id'=>'1','assetId'=>'1','numberOfVotes'=>'5','description'=>'testdescription1'],
                   ['id'=>'2','assetId'=>'2','numberOfVotes'=>'3','description'=>'testdescription2'],
                   ['id'=>'3','assetId'=>'3','numberOfVotes'=>'2','description'=>'testdescription3']
               ];
    }

    /** 
     * @dataProvider providerTickets()
     **/
    public function testFindTicketByName($id, $assetId, $numberOfVotes, $description)
    {
        $ticketModel = new PDOTicketModel($this->connection);
        $expectedTicket = ['id'=>$id, 'assetId'=>$assetId, 'numberOfVotes'=>$numberOfVotes, 'description'=>$description ];
        $actualTicket = $ticketModel->findTicketById($id);

        $this->assertIsArray($actualTicket);
        $this->assertArrayHasKey('id', $actualTicket);
        $this->assertArrayHasKey('assetId', $actualTicket);
        $this->assertArrayHasKey('numberOfVotes', $actualTicket);
        $this->assertArrayHasKey('description', $actualTicket);
        $this->assertEquals($expectedTicket['id'], $actualTicket['id']);
    }

    /** 
     * @dataProvider providerTickets()
     **/
    public function testVoteForTicket(){
        $ticketModel = new PDOTicketModel($this->connection);
        $oldTicket = $ticketModel->findTicketById('1');
        $ticketModel->voteForTicket('1');
        $newTicket = $ticketModel->findTicketById('1');

        $this->assertIsArray($oldTicket);
        $this->assertIsArray($newTicket);
        $this->assertEquals($oldTicket['numberOfVotes']+1, $newTicket['numberOfVotes']);
    }

    public function testValidateId(){
        $ticketModel = new PDOTicketModel($this->connection);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('id moet een int > 0 bevatten');
        $ticketModel->validateId('0');
    }
}