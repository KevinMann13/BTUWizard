<?php
/*
 * Floor.php
 * Object to hold all of the floor information for a room
 */
include_once "RoomOBJ.php";

class Floor extends RoomOBJ
{
    const UFACTORS = array(
      "Slab Foundation"=>0,
      "R30 Raised Foundation"=>0.04,
      "R19 Raised Foundation"=>0.05
    );

    public function calcBTUH()
    {
        return parent::calc(Floor::UFACTORS);
    }
}
