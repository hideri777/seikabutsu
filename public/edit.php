<?php

namespace seikabutsu;

/**
 * https://papadays.com/post/7fh0bicxoctivnraohvxax/
 * 編集中にページ遷移しようとするときには確認のアラーム
 * 
 * 最低限
 * タイトル、本文を書いて投稿するボタンを押すと記事の投稿ができる
 */

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\Post;

$isLogin = Bootstrap::returnLoginState();
if (!$isLogin) header("Location: login.php");

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

$table = 'posts';
$title = isset($_POST['title']) ? $_POST['title'] : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';
$isUpdate = isset($_POST['isUpdate']) ? $_POST['isUpdate'] : false;
$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
$isComplete = false;

// 既存ページの書き換えのとき
if (isset($_GET['post_id']) === true && preg_match('/^\d+$/', $_GET['post_id']) === 1) {
  $post_id = $_GET['post_id'];
  $res = $post->getPostDetail($post_id);
  // user_idが一致していなければトップページへリダイレクト
  if ($res[0]['user_id'] !== $_SESSION['user_id']) header("Location: index.php");
  $title = $res[0]['title'];
  $body = $res[0]['body'];
  $post_id = $res[0]['post_id'];
  $isUpdate = true;
}

$insertData = [
  'title' => $title,
  'body' => $body,
  'user_id' => $_SESSION['user_id'],
];

$updateData = [
  'title' => $title,
  'body' => $body,
  'update_date' => date('Y-m-d H:i:s')
];

if (isset($_POST['send'])) {
  if (!$isUpdate) {
    $db->insert($table, $insertData, 'created_date');
  } else {
    $db->update($table, $updateData, 'post_id = ?', [$post_id]);
  }
  $isComplete = true;
}

// TODO: 投稿完了ページを作る
// その記事のページor個人ページに遷移
// アップロード完了時の処理
if ($isComplete) {
  exit('投稿が完了しました');
}

$context = [];
$context['insData'] = $isUpdate ? $updateData : $insertData;
$context['isUpdate'] = $isUpdate;
$context['post_id'] = $post_id;
$context['isLogin'] = $isLogin;

echo $twig->render('edit.twig', $context);
