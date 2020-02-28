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

// もととなる作品の抽出
if (!isset($_SESSION['games'])) {
  $_SESSION['games'] = $game->getGames(30);
  list($_SESSION['pickupGames'], $_SESSION['newGames'], $_SESSION['popularGames']) = $game->splitGameData($_SESSION['games'], 10);
}


// トップページで表示する分
for($i = 0; $i < 6; $i++) {
  $topPickupGames[] = $_SESSION['pickupGames'][$i];
  $topNewGames[] = $_SESSION['newGames'][$i];
  $topPopularGames[] = $_SESSION['popularGames'][$i];
}


// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]); 

// 直近の10件の投稿を取得する
$recentPosts = $post->getRecentPost();

$context = [];
$context['isLogin'] = $isLogin;
$context['pickupGames'] = $topPickupGames;
$context['newGames'] = $topNewGames;
$context['popularGames'] = $topPopularGames;
$context['recentPosts'] = $recentPosts;
echo $twig->render('index.twig', $context);