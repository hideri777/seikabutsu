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

// post_idを取得する
$post_id = (isset($_GET['post_id']) === true && preg_match('/^\d+$/', $_GET['post_id']) === 1) ? $_GET['post_id'] : '';

// item_idが取得できていない場合、一覧へ遷移させる
if ($post_id === '') {
  // headerでリダイレクト処理
  header('Location: index.php');
}

// 記事情報を取得する
// 情報が取れなかった場合はトップページ
$postData = $post->getPostDetail($post_id);
if($postData[0] === null) header('Location: index.php');

// コメント取得
$commentdata = $post->getCommentsInfo($post_id);
// いいねの状態取得
if ($isLogin['isLogin']) {
  $isLiked = $post->getLikedState($_SESSION['user_id'], $post_id);
}
// 対象のゲームの情報を取得
$gameData = $game->getGameDetail($postData[0]['target_game_id']);

$context = [];
$context['postData'] = $postData[0];
$context['gameData'] = $gameData[0];
$context['commentData'] = $commentdata;
$context['isLogin'] = $isLogin;
if (!empty($isLiked)) {
  $context['isLiked'] = $isLiked[0]['is_liked'];
} else {
  $context['isLiked'] = 'first';
}
if ($isLogin['isLogin']) {
  $context['login_user_id'] = $_SESSION['user_id'];
  if ($_SESSION['user_id'] === $postData[0]['user_id']) $context['editable'] = true;
}

echo $twig->render('post.twig', $context);
