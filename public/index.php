<?php

namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\Post;

$testFilePath = dirname(__DIR__). '/app/logs/';
$testFile = file_get_contents($testFilePath . 'test.json');
$file = json_decode($testFile, true);
$applist = $file['applist'];
$apps = $applist['apps'];

$appInfos = [];
foreach($apps as $app) {
  $appId = $app['appid'];
  $appName = $app['name'];
  $imageUrl = 'https://steamcdn-a.akamaihd.net/steam/apps/' . $appId . '/header.jpg';
  $appInfos[] = [
    'id' => $appId,
    'name' => $appName, 
    'image' => $imageUrl
  ];
}

$isLogin = Bootstrap::returnLoginState();

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]); 

var_dump($_POST);

// 直近の10件の投稿を取得する
$recentPosts = $post->getRecentPost();
$context = [];
$context['appInfos'] = $appInfos;
$context['recentPosts'] = $recentPosts;
$context['isLogin'] = $isLogin;
echo $twig->render('index.twig', $context);