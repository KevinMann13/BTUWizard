<?php
/*
 * Floor.php
 * Object to hold all of the floor information for a room
 */
include_once "RoomOBJ.php";

class Floor extends RoomOBJ
{
    const UFACTORS = array(
      "Slab Foundation"=>1.50,
      "R30 Raised Foundation"=>1.50,
      "R19 Raised Foundation"=>1.50
    );

    public function calcBTUH()
    {
        return parent::calc(Floor::UFACTORS);
    }
}
