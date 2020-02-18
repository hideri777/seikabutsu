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
  'family_name' => '',
  'first_name' => '',
  'family_name_kana' => '',
  'first_name_kana' => '',
  'year' => '',
  'month' => '',
  'day' => '',
  'zip1' => '',
  'zip2' => '',
  'address' => '',
  'email' => '',
  'tel1' => '',
  'tel2' => '',
  'tel3' => '',
  'contents' => ''
];

// エラーメッセージの定義、初期
$errArr = [];
foreach ($dataArr as $key => $value) {
  $errArr[$key] = '';
}

// array($yearArr, $monthArr, $dayArr)
// 静的クラス

list($yearArr, $monthArr, $dayArr) = initMaster::getDate();
// 右辺の配列の要素を、左辺の変数に代入する事ができる

$context = [];
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

// 古いバージョン
// $template = $twig->loadTemplate('regist.html.twig');

$template = $twig->load('regist.twig');
$template->display($context);

// echo $twig->render('regist.html.twig', $context);
