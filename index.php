<?php
require_once "vendor/autoload.php";

$Manager = new FileManager();
$dir = "";
if (!empty($_GET['dir']))
	$dir = $_GET['dir'];
$listDir = $Manager->getDir($dir);


$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new Twig_Environment($loader, []);

$template = $twig->load('index.html');
echo $template->render($listDir);