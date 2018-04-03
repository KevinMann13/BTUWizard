<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';

//Load in all classes
spl_autoload_register(function ($class_name) {
    $path = 'api/BO/' . $class_name . '.php';
    if (file_exists($path)) {
        include $path;
    }
});

$datamap = array(
  "walls" => array_keys(Wall::UFACTORS),
  "doors" => array_keys(Door::UFACTORS),
  "windows" => array_keys(Window::UFACTORS),
  "ceilings" => array_keys(Ceiling::UFACTORS),
  "floors" => array_keys(Floor::UFACTORS),
  "ws" => range(1, 8)
);

$m = new Mustache_Engine([
  'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views'),
  'cache' => dirname(__FILE__).'/cache',
]);

$tpl = $m->loadTemplate('index'); // loads __DIR__.'/views/index.mustache';
echo $tpl->render($datamap);
