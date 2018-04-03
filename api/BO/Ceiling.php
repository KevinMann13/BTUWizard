<?php
/*
 * Ceiling.php
 * Object to hold all of the ceiling information for a room
 */
include_once "RoomOBJ.php";

class Ceiling extends RoomOBJ
{
    const UFACTORS = array(
        "R19"=>0.05,
        "R30"=>0.04
    );

    public function calcBTUH()
    {
        return parent::calc(Ceiling::UFACTORS);
    }
}
