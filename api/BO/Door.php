<?php

/**
 * Description of Door
 *
 * @author KevinMann
 */

include_once "RoomOBJ.php";

class Door extends RoomOBJ
{
    const UFACTORS = array(
        "Hollow Wood"=>0.46,
        "Solid Wood"=>0.33,
        "Single Pane Glass"=>1.10,
        "Dual Pane Glass"=>0.62
    );

    public function calcBTUH()
    {
        return parent::calc(Door::UFACTORS);
    }
}
