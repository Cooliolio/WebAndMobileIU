<?php
namespace App\Model;
/**
 * Created by PhpStorm.
 * User: caglarcelikoz
 * Date: 24/09/2019
 * Time: 11:55
 */
class PDOTicketModel implements TicketModel
{
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findTicketById($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("SELECT * FROM tickets WHERE id=:id");
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $statement->bindColumn(1, $id, \PDO::PARAM_INT);
        $statement->bindColumn(2, $assetId, \PDO::PARAM_INT);
        $statement->bindColumn(3, $numberOfVotes, \PDO::PARAM_INT);
        $statement->bindColumn(4, $desc, \PDO::PARAM_STR);
        $ticket = null;
        if ($statement->fetch(\PDO::FETCH_BOUND)) {
            $ticket = ['id' => $id, 'assetId'=> $assetId, 'description' => $desc, 'numberOfVotes'=> $numberOfVotes];
        }
        return $ticket;
    }

    public function voteForTicket($id){
        $this->validateId($id);
        $numberOfVotes = $this->findTicketById($id)['numberOfVotes'];
        $numberOfVotes = $numberOfVotes + 1;
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare("UPDATE tickets SET numberOfVotes=:numberOfVotes WHERE id=:id");
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->bindParam(':numberOfVotes', $numberOfVotes, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function validateId($id)
    {
        if (!(is_string($id) && preg_match("/^[0-9]+$/", $id) && (int)$id > 0)) {
            throw new \InvalidArgumentException('id moet een int > 0 bevatten');
        }
    }


}
