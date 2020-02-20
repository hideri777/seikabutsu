<?php

namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\User;

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$user = new User($db);

$user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$err_msg = '';

if (isset($_POST['send'])) {
  $res = $user->login($user_name, $password);
  if ($res) {
    session_start();
    $_SESSION['user_id'] = $res[0]['user_id'];
    $_SESSION['user_name'] = $res[0]['user_name'];
    header("Location: index.php");
    exit();
  } else {
    $err_msg = 'ユーザー名またはパスワードが違います';
  }
}

$context = [];
$context['user_name'] = $user_name;
$context['err_msg'] = $err_msg;
echo $twig->render('login.twig', $context);
