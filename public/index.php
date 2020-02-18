<?php
namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;

session_start();
$isLogin = isset($_SESSION['login']) ? true : false;

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$context = [];
$context['isLogin'] = $isLogin;
echo $twig->render('index.twig', $context);