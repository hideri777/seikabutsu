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

// $appNames = [];
$appInfos = [];
foreach($apps as $app) {
  $imageUrl = 'https://steamcdn-a.akamaihd.net/steam/apps/' . $app['appid'] . '/header.jpg';
  $appName = $app['name'];
  $appInfos[] = [
    'name' => $appName, 
    'image' => $imageUrl
  ];
}

// $appInfo = [
//   'appid' => $appIds,
//   'name' => $appNames,
// ]

// appidがあっても単なるテストだったり、画像がないものは取得しない
// foreach($appId as $value) {
//   echo $value;
// }
// $imageUrl = 'https://steamcdn-a.akamaihd.net/steam/apps/' . $testAppId . '/header.jpg';

$isLogin = Bootstrap::returnLoginState();

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]); 

// 直近の10件の投稿を取得する
$recentPosts = $post->getRecentPost();
// var_dump($appInfos);
$context = [];
$context['appInfos'] = $appInfos;
$context['recentPosts'] = $recentPosts;
$context['isLogin'] = $isLogin;
echo $twig->render('index.twig', $context);
// var_dump($appInfos);
// var_dump($appInfos['image'][0]);