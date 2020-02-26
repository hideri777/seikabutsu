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

// game_idを取得する
$game_id = (isset($_GET['game_id']) === true && preg_match('/^\d+$/', $_GET['game_id']) === 1) ? $_GET['game_id'] : '';

// game_idが取得できていない場合、トップページへ遷移させる
if ($game_id === '') {
  // headerでリダイレクト処理
  header('Location: index.php');
}

// 対象のゲームの情報を取得
$gameData = $game->getGameDetail($game_id);

// 対象のゲームに関する投稿を取得する
$postDatas = $post->getPostsInfo($game_id);

// 対象のゲームに関連するコメント
$comments = $post->getCommentsForGame($game_id);

$context = [];
// $context['isLogin'] = $isLogin;
// if (!empty($isLiked)) {
//   $context['isLiked'] = $isLiked[0]['is_liked'];
// } else {
//   $context['isLiked'] = 'first';
// }
// if ($isLogin['isLogin']) {
//   $context['login_user_id'] = $_SESSION['user_id'];
//   if ($_SESSION['user_id'] === $postData[0]['user_id']) $context['editable'] = true;
// }
$context['gameData'] = $gameData[0];
$context['postDatas'] = $postDatas;
$context['comments'] = $comments;
echo $twig->render('game.twig', $context);
