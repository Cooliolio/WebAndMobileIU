<?php

namespace App\Tests\Model;
use App\Model\Connection;
use \App\Model\PDOAssetModel;
use \App\Model\PDOTicketModel;
use PHPUnit\Framework\TestCase;

class PDOAssetModelTest extends TestCase
{
    private $connection;

    protected function setUp()
    {
        $user = 'root';
        $password = '';
        $database = 'test_webandmobile_api';
        $server = 'localhost:3306';
        $this->connection = new Connection("mysql:host=$server;dbname=$database", $user, $password);
        $this->connection->getPDO()->exec('DROP TABLE IF EXISTS assets');
        $this->connection->getPDO()->exec('CREATE TABLE assets (
                                                    id int(11) NOT NULL,
                                                    roomId int(11) NOT NULL,
                                                    name varchar(255),
                                                    PRIMARY KEY (id)
                                                    )');

        $assets = $this->providerAssets();
        foreach ($assets as $asset) {
            $id = $asset['id'];
            $roomId = $asset['roomId'];
            $name = $asset['name'];
            $this->connection->getPDO()->exec(
                "INSERT INTO assets (id, roomId, name) VALUES ($id, $roomId, '$name')"
            );
        }
    }

    public function providerAssets()
    {
        return [
                   ['id'=>'1','roomId'=>'1', 'name'=>'testname1'],
                   ['id'=>'2','roomId'=>'2', 'name'=>'testname2'],
                   ['id'=>'3','roomId'=>'3', 'name'=>'testname3']
               ];
    }

    /** 
     * @dataProvider providerAssets()
     **/
    public function testFindAssetByName($id, $roomId, $name)
    {
        $assetModel = new PDOAssetModel($this->connection);
        $expectedAsset = ['id'=>$id, 'roomId'=>$roomId, 'name'=>$name ];
        $actualAsset = $assetModel->findAssetByName($name);

        $this->assertIsArray($actualAsset);
        $this->assertArrayHasKey('id', $actualAsset);
        $this->assertArrayHasKey('roomId', $actualAsset);
        $this->assertArrayHasKey('name', $actualAsset);
        $this->assertEquals($expectedAsset['name'], $actualAsset['name']);
    }

    public function testAddTicketForAssetName(){
        $assetModel = new PDOAssetModel($this->connection);

        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT * FROM tickets");
        $statement->execute();
        $ticketsOld = $statement->fetchAll(\PDO::FETCH_UNIQUE);

        $assetModel->addTicketForAssetName('testname1', 'deniztest');
        $statement->execute();
        $ticketsNew = $statement->fetchAll(\PDO::FETCH_UNIQUE);

        $this->assertIsArray($ticketsOld);
        $this->assertIsArray($ticketsNew);
        $this->assertEquals(count($ticketsOld)+1, count($ticketsNew));
    }

    public function testFindTicketsByAssetName(){
        $assetModel = new PDOAssetModel($this->connection);
        $assets = $assetModel->findTicketsByAssetName('testname1');

        $this->assertIsArray($assets);
        $this->assertEquals(count($assets), 2);
    }

    public function testValidateAssetNameNumeric(){
        $assetName = 111;
        $assetModel = new PDOAssetModel($this->connection);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Asset name not valid: ' . $assetName);
        $assetModel->validateAssetName($assetName);
    }

    public function testValidateAssetNameLengthLessThanThree(){
        $assetName = 'ro';
        $assetModel = new PDOAssetModel($this->connection);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Asset name not valid: ' . $assetName);
        $assetModel->validateAssetName($assetName);
    }
}