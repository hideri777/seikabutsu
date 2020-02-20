<?php
namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;

$isLogin = Bootstrap::returnLoginState();

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

if(isset($_POST['send'])) {
  session_unset();
  header('Location: index.php');
}

$context = [];
$context['isLogin'] = $isLogin;
echo $twig->render('profile.twig', $context);