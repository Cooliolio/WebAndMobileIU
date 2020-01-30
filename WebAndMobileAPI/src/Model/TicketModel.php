<?php
namespace App\Model;
/**
 * Created by PhpStorm.
 * User: caglarcelikoz
 * Date: 24/09/2019
 * Time: 11:56
 */
interface TicketModel
{
    public function findTicketById($id);
    public function voteForTicket($id);
}