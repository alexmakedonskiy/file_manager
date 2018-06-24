<?php
require_once "vendor/autoload.php";

$Manager = new FileManager();
$dir = "";
$file = "";
if (!empty($_GET['dir'])){
	$dir = $_GET['dir'];
}
$result = $Manager->getDir($dir);
if (!empty($_GET['file']))
{
	$file = $_GET['file'];
	$result['OpenFile'] = $Manager->openFile($file);
}

$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new Twig_Environment($loader, []);

$template = $twig->load('index.html');
//xprint($result);
echo $template->render($result);