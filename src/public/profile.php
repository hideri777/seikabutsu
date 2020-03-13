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

// user_idを取得する
$user_id = (isset($_GET['user_id']) === true && preg_match('/^\d+$/', $_GET['user_id']) === 1) ? $_GET['user_id'] : '';

// user_idが取得できていない場合、一覧へ遷移させる
if ($user_id === '') {
  // headerでリダイレクト処理
  header('Location: index.php');
}

$userInfo = $user->getUserInfo($user_id);
$userPosts = $user->getUserPosts($user_id);
$userLikes = $user->getUserLikes($user_id);

$context = [];
$context['isLogin'] = $isLogin;
$context['userInfo'] = $userInfo[0];
$context['userPosts'] = $userPosts;
$context['userLikes'] = $userLikes;
echo $twig->render('profile.twig', $context);
