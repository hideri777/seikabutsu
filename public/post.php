<?php

namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\Post;

$isLogin = Bootstrap::returnLoginState();

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

// post_idを取得する
$post_id = (isset($_GET['post_id']) === true && preg_match('/^\d+$/', $_GET['post_id']) === 1) ? $_GET['post_id'] : '';

// item_idが取得できていない場合、商品一覧へ遷移させる
if ($post_id === '') {
  // headerでリダイレクト処理
  header('Location: index.php');
}



$game_id = 535530;
// 対象のゲームに関する投稿を取得する
$postData = $post->getPostsInfo($game_id);
foreach ($postData as $key => $value) {
  $post_ids[] = ($value['post_id']);
  // $commentDatas[] = $post->getCommentsInfo($post_id);
}
// $post_idsはゲームに対する投稿のidの配列
// var_dump($post_ids);
// 投稿に対するコメントを取得する
foreach ($post_ids as $post_id) {
  $comments[] = $post->getCommentsInfo($post_id);
}
// var_dump($comments);
// var_dump($comments[8]);




// 記事情報を取得する
$postData = $post->getPostDetail($post_id);
// コメント取得
$commentdata = $post->getCommentsInfo($post_id);
// いいねの状態取得
if ($isLogin['isLogin']) {
  $isLiked = $post->getLikedState($_SESSION['user_id'], $post_id);
}

$context = [];
$context['postData'] = $postData[0];
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
