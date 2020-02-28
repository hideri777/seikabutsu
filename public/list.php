<?php

namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\Game;
use App\models\Post;

$isLogin = Bootstrap::returnLoginState();

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);
$game = new Game($db);


// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

// リストの種類を取得
$type = (isset($_GET['type']) === true && $_GET['type'] === 'pickup' || $_GET['type'] === 'new' || $_GET['type'] === 'popular') ? $_GET['type'] : '';
// 何ページ目か取得
$page = (isset($_GET['page']) === true && preg_match('/^\d+$/', $_GET['page']) === 1) ? $_GET['page'] : 1;

// typeがない時はトップページ
if ($type === '') {
  // headerでリダイレクト処理
  header('Location: index.php');
}

$listGameDatas = [];

if ($type === 'pickup') {
  $listGameDatas = $_SESSION['pickupGames'];
  $title = 'ピックアップゲーム';
} elseif ($type === 'new') {
  $listGameDatas = $_SESSION['newGames'];
  $title = '新作ゲーム';
} elseif ($type === 'popular') {
  $listGameDatas = $_SESSION['popularGames'];
  $title = '人気ゲーム';
}



$splitGameData = array_chunk($listGameDatas, 20);
$listGameData = $splitGameData[$page - 1];
// var_dump($listGameData);

$context = [];
$context['type'] = $type;
$context['title'] = $title;
$context['page'] = $page;
$context['pageNumbers'] = [1, 2, 3, 4, 5];
$context['listGameData'] = $listGameData;
$context['isLogin'] = $isLogin;

echo $twig->render('list.twig', $context);
