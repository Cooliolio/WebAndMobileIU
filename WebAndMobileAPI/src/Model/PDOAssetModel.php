<?php

namespace App\Model;
/**
 * Created by PhpStorm.
 * User: caglarcelikoz
 * Date: 24/09/2019
 * Time: 10:29
 */
class PDOAssetModel implements AssetModel
{

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAssetsByRoomId($roomId){
        
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT name FROM assets WHERE roomId = :roomId ");
        $statement->bindParam(':roomId', $roomId, \PDO::PARAM_STR);
        $statement->execute();
        $assets = null;
        $assets = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $assets;
    }

    public function findAssetByName($name)
    {
        $this->validateAssetName($name);
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT * FROM assets WHERE name=:name");
        $statement->bindParam(':name', $name, \PDO::PARAM_STR);
        $statement->execute();
        $statement->bindColumn(1, $id, \PDO::PARAM_INT);
        $statement->bindColumn(2, $roomId, \PDO::PARAM_STR);
        $statement->bindColumn(3, $name, \PDO::PARAM_STR);
        $asset = null;
        if ($statement->fetch(\PDO::FETCH_BOUND)) {
            $asset = ['id' => $id, 'roomId' => $roomId, 'name' => $name];
        }
        return $asset;
    }

    public function addTicketForAssetName($name, $description)
    {
        $this->validateAssetName($name);
        $asset = $this->findAssetByName($name);
        $assetId = $asset['id'];
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("INSERT INTO tickets (assetId, description) VALUES (:assetId,:description)");
        $statement->bindParam(':assetId', $assetId, \PDO::PARAM_INT);
        $statement->bindParam(':description', $description, \PDO::PARAM_STR);
        $statement->execute();
    }

    public function findTicketsByAssetName($name)
    {
        $this->validateAssetName($name);
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT * FROM assets WHERE name=:name");
        $statement->bindParam(':name', $name, \PDO::PARAM_STR);
        $statement->execute();
        $statement->bindColumn(2, $name, \PDO::PARAM_STR);
        $statement->bindColumn(1, $id, \PDO::PARAM_INT);
        $id = null;
        if ($statement->fetch(\PDO::FETCH_BOUND)) {
            $id = ['id' => $id];
        }
        // return $id;
        $statement = $pdo->prepare("SELECT * FROM tickets WHERE assetId=:assetId");
        $statement->bindParam(':assetId', $id['id'], \PDO::PARAM_INT);
        $statement->execute();
        $ticketsResult = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $ticketsResult;
    }
    
    public function validateAssetName($assetName)
    {
        if (!(is_string($assetName)) ||
            (strlen($assetName) < 3)) {
            throw new \InvalidArgumentException('Asset name not valid: ' . $assetName);
        }
    }
}