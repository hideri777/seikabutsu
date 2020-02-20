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

session_start();
if(!isset($_SESSION['user_id']))  header("Location: login.php");

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);

$table = 'posts';
// TODO: すでに存在している投稿を編集するとき
// if(isset($_POST['edit'])) {
// $db->select()して投稿済みの内容をとってくる
// }


$title = isset($_POST['title']) ? $_POST['title'] : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';

$is_uploaded = false;

$insData = [
  'title' => $title,
  'body' => $body,
  'user_id' => $_SESSION['user_id'],
];

if (isset($_POST['send'])) {
  // 空でさえなければ投稿は可能
  $db->insert($table, $insData, 'created_date');
  $is_uploaded = true;
}

// TODO: 投稿完了ページみたいなものを作る?
// アップロード完了時の処理
if ($is_uploaded) {
  exit('投稿が完了しました');
}

$context = [];
$context['insData'] = $insData;
$context['is_uploaded'] = $is_uploaded;

echo $twig->render('edit.twig', $context);
