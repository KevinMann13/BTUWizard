<?php
/*
 * RoomOBJ.php
 * Base class for all object in a room and the basic calculations they will need
 */
class RoomOBJ
{
    public $contruction_type;
    public $exterior_temp;
    public $desired_temp;
    public $temp_difference;
    public $area;
    public $BTUH;
    public $uFactor;
    public $DD;
    public $height;
    public $width;

    public function set_size($pWidth, $pHeight)
    {
        $this->width = $pWidth;
        $this->height = $pHeight;
        $this->area = $this->height * $this->width;
    }

    public function calcUFactor()
    {
        if (!isset($this->UFactors[$this->contruction_type])) {
            $this->uFactor = $this->UFactors[array_keys($this->UFactors)[0]];
        } else {
            $this->uFactor = $this->UFactors[$this->contruction_type];
        }
    }

    public function calc($uFactors)
    {
        $this->UFactors = $uFactors;
        $this->calcUFactor();
        $this->BTUH = $this->area * ($this->exterior_temp - $this->desired_temp) * $this->uFactor;
        return $this->BTUH;
    }
}
