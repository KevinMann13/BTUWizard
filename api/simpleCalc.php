<?php

error_reporting(E_ALL);

//Load in all classes
function __autoload($class_name)
{
    include 'BO/' . $class_name . '.php';
}

$btuRange = 1000;

$walls = array(); //Store wall objects here

$height = $_POST['height'];
$width = $_POST['width'];
$depth = $_POST['depth'];

$exterior_temp = $_POST['exterior_temp'];
$material = $_POST['material'];

$cc = isset($_POST['cc']);

$g_height = isset($_POST['glass_height'])?$_POST['glass_height']:0;
$g_width = isset($_POST['glass_width'])?$_POST['glass_width']:0;
$g_material = isset($_POST['glass_material'])?$_POST['glass_material']:false;

/************************************************* */
/************* LOADING IN DATA ******************* */
/************************************************* */

$room = new Room();
$output = [];

if ($_POST['form_type'] == "simple") {
    $totalBTU = $room->calcSimple($height, $width, $depth, $material, $g_height, $g_width, $g_material, $exterior_temp);

    $output["dimensions"] = "$width' x $height' x $depth'";
    $output["wall_material"] = $material;
    $output["glass_dimensions"] = "$g_height' x $g_width'";
    $output["glass_material"] = $g_material;
    $output["exterior_temp"] = $exterior_temp;

    $output["floor"] = $room->floor;
    $output["floor_btu"] = $room->floor->calcBTUH();
} else {
    $totalBTU = $room->calcAdvanced($_POST);
    $output["walls"] = $room->getWallData();
    $output["ceiling"] = $room->ceiling;
    $output["floor"] = $room->floor;
    $output["floor_btu"] = $room->floor->calcBTUH();
}

/**************************************************/
/**                 Head Loads                   **/
/**************************************************/

$tempLow = 0;
$tempHigh = 0;
$tempModels = "";

$output["load"] = $totalBTU;

if ($cc) {
    $file = "cc_units.json";
} else {
    $file = "wk_units.json";
}

$string = file_get_contents($file);
$json = json_decode($string, true);

$output["models"] = [];
foreach ($json as $unit) {
    if ($totalBTU < $unit['btuh'] && $unit['btuh'] < $totalBTU + $btuRange) {
        $output["models"][] = $unit;
    }
}

echo json_encode($output);
