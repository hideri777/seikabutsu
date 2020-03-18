<?php

namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\initMaster;

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

// 初期データを設定
$dataArr = [
  'user_name' => '',
  'email' => '',
  'password' => '',
  'image' => '',
  'year' => '',
  'month' => '',
  'day' => '',
  'regist_date' => ''
];

// エラーメッセージの定義、初期
$errArr = [];
foreach ($dataArr as $key => $value) {
  $errArr[$key] = '';
}

list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$context = [];
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

$template = $twig->load('regist.twig');
$template->display($context);