<?php

class Room
{
    public $desired_temp = 55;
    public $exterior_temp;
    public $infiltrationBTU;
    public $glass;
    public $cellar_height = 0;

    public $walls = [];
    public $ceiling;
    public $floor;

    public function calcSimple($h, $w, $d, $construction, $g_height, $g_width, $g_material, $temp)
    {
        $this->cellar_height = $h;
        $this->exterior_temp = $temp;

        $this->ceiling = new Ceiling();
        $this->ceiling->set_size($w, $d);
        $this->ceiling->exterior_temp = $this->exterior_temp;
        $this->ceiling->desired_temp = $this->desired_temp;

        $this->floor = new Floor();
        $this->floor->set_size($w, $d);
        $this->floor->exterior_temp = 62;
        $this->floor->desired_temp = $this->desired_temp;

        $w_area = ($h*$w*2) + ($h*$d*2);

        $this->glass = new Window();
        if ($g_height > 0 && $g_width) {
            $this->glass->set_size($g_width, $g_height);
            $this->glass->exterior_temp = $this->exterior_temp;
            $this->glass->desired_temp = $this->desired_temp;
            $this->glass->contruction_type = $g_material;

            //Subtract the total glass area from the wall area
            $w_area -= $g_height * $g_width;
        };

        $wall = new Wall();
        if ($w_area > 0) {
            $wall->set_size(sqrt($w_area), sqrt($w_area));
            $wall->exterior_temp = $this->exterior_temp;
            $wall->desired_temp = $this->desired_temp;
            $wall->contruction_type = $construction;
            $this->walls[] = $wall;
        }


        //$infiltrationCFM = ($h*$w*$d) / 60;
        //$this->infiltrationBTU = 1.1 * ($this->exterior_temp - $this->desired_temp);

        return $this->calc();
    }

    public function calcAdvanced($params)
    {
        $this->desired_temp = !empty($params['cellar_temp'])?$params['cellar_temp']:55;

        for ($x=1;$x<=$params['num_walls'];$x++) {
            $height = !empty($params["wall" . $x . "_height"])?$params["wall" . $x . "_height"]:0;
            $width = !empty($params["wall" . $x . "_width"])?$params["wall" . $x . "_width"]:0;
            $temp = !empty($params["wall" . $x . "_temp"])?$params["wall" . $x . "_temp"]:75;

            $this->cellar_height = $height;

            $wall = new Wall();
            $wall->set_size($width, $height);
            $wall->exterior_temp = $temp;
            $wall->desired_temp = $this->desired_temp;
            $wall->contruction_type = $params["wall" . $x . "_material"];

            if (!empty($params['enable_window_' . $x])) {
                $height = !empty($params["window" . $x . "_height"])?$params["window" . $x . "_height"]:0;
                $width = !empty($params["window" . $x . "_width"])?$params["window" . $x . "_width"]:0;

                $window = new Window();
                $window->set_size($width, $height);
                $window->contruction_type = $params["window" . $x . "_material"];
                $window->exterior_temp = $temp;
                $wall->desired_temp = $this->desired_temp;

                $wall->windows[] = $window;
            }

            if (!empty($params['enable_door_' . $x])) {
                $height = !empty($params["door" . $x . "_height"])?$params["door" . $x . "_height"]:0;
                $width = !empty($params["door" . $x . "_width"])?$params["door" . $x . "_width"]:0;

                $door = new Door();
                $door->set_size($width, $height);
                $door->contruction_type = $params["door" . $x . "_material"];
                $door->exterior_temp = $temp;
                $door->desired_temp = $this->desired_temp;

                $wall->doors[] = $door;
            }

            $this->walls[] = $wall;
        }


        $height = !empty($params["ceiling_height"])?$params["ceiling_height"]:0;
        $width = !empty($params["ceiling_width"])?$params["ceiling_width"]:0;
        $temp = !empty($params["ceiling_temp"])?$params["ceiling_temp"]:75;

        $this->ceiling = new Ceiling();
        $this->ceiling->set_size($width, $height);
        $this->ceiling->exterior_temp = $temp;
        $this->ceiling->contruction_type = $params["ceiling_material"];
        $this->ceiling->desired_temp = $this->desired_temp;


        $height = !empty($params["floor_height"])?$params["floor_height"]:0;
        $width = !empty($params["floor_width"])?$params["floor_width"]:0;
        $temp = !empty($params["floor_temp"])?$params["floor_temp"]:75;

        $this->floor = new Floor();
        $this->floor->set_size($width, $height);
        $this->floor->exterior_temp = $temp;
        $this->floor->contruction_type = $params["floor_material"];
        $this->floor->desired_temp = $this->desired_temp;

        return $this->calc();
    }

    public function calc()
    {
        $btu = 0;
        $btu += $this->ceiling->calcBTUH();
        $btu += $this->floor->calcBTUH();

        foreach ($this->walls as $wall) {
            $btu += $wall->calcBTUH();
        }

        if ($this->glass !== null) {
            $btu += $this->glass->calcBTUH();
        }

        $btu = round(($btu * 1.25)* 1.20) ;
        return $btu;
    }

    public function getVolume()
    {
        if ($this->ceiling !== null && count($this->walls) > 0) {
            return $this->ceiling->area * $this->cellar_height;
        } else {
            return 0;
        }
    }

    public function getAverageTemp()
    {
        $temp = 0;
        foreach ($this->walls as $wall) {
            $temp += $wall->exterior_temp;
        }
        return $temp / count($this->walls);
    }

    public function getWallData()
    {
        return $this->walls;
    }
}
