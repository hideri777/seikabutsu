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

$errMsg = '';

if (isset($_POST['sendImage'])) {
  // 簡易版で検査
  if ($_FILES['image']['size'] !== 0) {
    $file_name = $_FILES['image']['name'];
    $update_name = time() . $file_name;
    var_dump($update_name);
    
    $res = $db->update('users', ['image' => $update_name], 'user_id = ?', [$_SESSION['user_id']]);

    if ($res === true) {
      move_uploaded_file($_FILES['image']['tmp_name'], './img/profile/' . $update_name);
    }
  } else {
    $errMsg = '画像をアップロードしてください';
  }
}

if (isset($_POST['logout'])) {
  session_unset();
  header('Location: index.php');
}

$userInfo = $user->getUserInfo($_SESSION['user_id']);

$context = [];
$context['isLogin'] = $isLogin;
$context['userInfo'] = $userInfo[0];
$context['errMsg'] = $errMsg;
echo $twig->render('mypage.twig', $context);
