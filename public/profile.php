<?php
namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\User;

$isLogin = Bootstrap::returnLoginState();

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$user = new User($db);

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

if(isset($_POST['send'])) {
  session_unset();
  header('Location: index.php');
}

$userInfo = $user->getUserInfo($_SESSION['user_id']);

$context = [];
$context['isLogin'] = $isLogin;
$context['userInfo'] = $userInfo[0];
echo $twig->render('profile.twig', $context);