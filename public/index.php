<?php

namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\Post;

session_start();
$isLogin = isset($_SESSION['user_id']) ? true : false;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]); 

// 直近の10件の投稿を取得する
$recentPosts = $post->getRecentPost();

$context = [];
$context['recentPosts'] = $recentPosts;
$context['isLogin'] = $isLogin;
echo $twig->render('index.twig', $context);
