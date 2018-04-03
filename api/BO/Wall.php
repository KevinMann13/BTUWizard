<?php
/*
 * Wall.php
 * Object to hold each wall's information for a room
 */
include_once "RoomOBJ.php";

class Wall extends RoomOBJ
{
    public $doors = array();
    public $windows = array();
    public $totalBTUH = 0;
    private $totalWindowArea = 0;
    private $totalWindowBTUH = 0;
    private $totalDoorArea = 0;
    private $totalDoorBTUH = 0;

    const UFACTORS = array(
        "2x4 Wall, R11 (Most Common)" => 0.08,
        "2x6 Wall, R19" => 0.05,
        "R-30 Closed Cell insulation" => 0.03,
        "Single Pane Glass" => 1.10,
        "Dual Pane Glass" => 0.62,
        "Concrete" => 2.27
    );

    public function calcBTUH()
    {
        parent::calc(Wall::UFACTORS);
        $this->totalWindowBTUH = $this->calcTotalWindowBTU();
        $this->totalDoorBTUH = $this->calcTotalDoorBTU();

        $this->BTUH = $this->uFactor * ($this->area - $this->totalWindowArea - $this->totalDoorArea) * ($this->exterior_temp - $this->desired_temp);
        $this->totalBTUH = $this->totalDoorBTUH + $this->totalWindowBTUH + $this->BTUH;
        return $this->totalBTUH;
    }

    public function calcTotalWindowBTU()
    {
        $total = 0;
        foreach ($this->windows as $window) {
            $this->totalWindowArea += $window->area;
            $window->calcBTUH();
            $total += $window->BTUH;
        }

        return $total;
    }

    public function calcTotalDoorBTU()
    {
        $total = 0;
        foreach ($this->doors as $door) {
            $this->totalDoorArea += $door->area;
            $door->calcBTUH();
            $total += $door->BTUH;
        }
        return $total;
    }
}
