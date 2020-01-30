<?php
namespace App\Model;
/**
 * Created by PhpStorm.
 * User: caglarcelikoz
 * Date: 24/09/2019
 * Time: 11:56
 */
interface AssetModel{
    public function addTicketForAssetName($name, $description);
    public function findTicketsByAssetName($name);
    public function getAssetsByRoomId($roomId);
}