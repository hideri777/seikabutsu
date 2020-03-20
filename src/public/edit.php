<?php

namespace seikabutsu;

// TODO できれば
/**
 * https://papadays.com/post/7fh0bicxoctivnraohvxax/
 * 編集中にページ遷移しようとするときには確認のアラーム
 */

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\Game;
use App\models\Post;

$isLogin = Bootstrap::returnLoginState();
if (!$isLogin['isLogin']) header("Location: login.php");

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);
$game = new Game($db);


$table = 'posts';
$title = isset($_POST['title']) ? $_POST['title'] : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';
$isUpdate = isset($_POST['isUpdate']) ? $_POST['isUpdate'] : false;
$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
$target_game_id = isset($_POST['target_game_id']) ? $_POST['target_game_id'] : '';
$rate = isset($_POST['rate']) ? $_POST['rate'] : 0;
$isComplete = false;

// gameが存在するか確認
if(isset($_GET['target_game_id'])) {
  $res = $game->getGameDetail($_GET['target_game_id']);
  if(empty($res[0])) {
    // TODO: リダイレクトかエラーメッセージちゃんと表示する
    exit('no');
  }
  $target_game_id = $res[0]['game_id'];
}

// 既存ページの書き換えのとき
if (isset($_GET['post_id']) === true && preg_match('/^\d+$/', $_GET['post_id']) === 1) {
  $post_id = $_GET['post_id'];
  $res = $post->getPostDetail($post_id);
  // user_idが一致していなければトップページへリダイレクト
  if ($res[0]['user_id'] !== $_SESSION['user_id']) header("Location: index.php");
  $title = $res[0]['title'];
  $body = $res[0]['body'];
  $post_id = $res[0]['post_id'];
  $rate = $res[0]['rate'];
  $isUpdate = true;
}

// 初回書き込み時のデータ
$insertData = [
  'title' => $title,
  'body' => $body,
  'rate' => $rate,
  'user_id' => $_SESSION['user_id'],
  'created_date' => date('Y-m-d H:i:s'),
  'target_game_id' => $target_game_id
];

// 更新用データ
$updateData = [
  'title' => $title,
  'body' => $body,
  'rate' => $rate,
  'update_date' => date('Y-m-d H:i:s')
];

// 投稿ボタンが押されたら
if (isset($_POST['send'])) {
  if ($isUpdate) {
    $db->update($table, $updateData, 'post_id = ?', [$post_id]);
  } else {
    $db->insert($table, $insertData);
  }
  $isComplete = true;
}

// その記事のページに遷移
// アップロード完了時の処理
if ($isComplete) {
  header("Location: game.php?game_id=" . $target_game_id);
  exit('投稿が完了しました');
}

$context = [];
$context['insData'] = $isUpdate ? $updateData : $insertData;
$context['isUpdate'] = $isUpdate;
$context['post_id'] = $post_id;
$context['rateNums'] = [5, 4, 3, 2, 1];
$context['rate'] = $rate;
$context['target_game_id'] = $target_game_id;
$context['isLogin'] = $isLogin;

echo $twig->render('edit.twig', $context);
