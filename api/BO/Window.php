<?php

include_once "RoomOBJ.php";

class Window extends RoomOBJ
{
    const UFACTORS = array(
      "Dual Pane Glass (Most Common)"=>0.62,
      "Single Pane Glass"=>1.10,
      "Triple Pane Glass"=>0.39
    );

    public function calcBTUH()
    {
        return parent::calc(Window::UFACTORS);
    }
}
