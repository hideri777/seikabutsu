<?php
namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\config\initMaster;
use App\config\Common;
use App\models\User;

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$user = new User($db);
$common = new Common();

// モード判定(どの画面から来たかの判定)
// 登録画面からきた場合
if(isset($_POST['confirm']) === true) {
  $mode = 'confirm';
}
// 戻る場合
if(isset($_POST['back']) === true) {
  $mode = 'back';
}
// 登録完了
if(isset($_POST['complete']) === true) {
  $mode = 'complete';
}

// ボタンのモードによって処理を変える
switch($mode) {
  case 'confirm': // 新規登録
                  // データを受け継ぐ
                  // ↓この情報は入力には必要ない
      unset($_POST['confirm']);

      $dataArr = $_POST;
      // エラーメッセージの配列作成
      $errArr = $common->errorCheck($dataArr);
      $err_check = $common->getErrorFlg();
      // err_check = false ->エラーがありますよ！
      // err_check = true ->エラーがないですよ！
      // エラーなければconfirm.tpl あるとregist.tpl
      $template = ($err_check === true) ? 'confirm.twig' : 'regist.twig';
    break;
  
  case 'back': // 戻ってきたとき
               // ポストされたデータをもとに戻すので、$dataArrにいれる
      $dataArr = $_POST;
      unset($dataArr['back']);

      // エラーも定義して置かないと、Undefinedエラーが出る
      $errArr = [];
      foreach($dataArr as $key => $value) {
        $errArr[$key] = '';
      }
      $template = 'regist.twig';
    break;
  
  case 'complete': // 登録完了
      $dataArr = $_POST;
      // ↓この情報はいらないので外しておく
      unset($dataArr['complete']);
      // TODO: Userクラスで行う、ユーザー名のかぶりがないか、アドレスすでに使われていないかなど
      // $res = $db->insert('users', $dataArr, 'regist_date');
      $res = $user->registUser('users', $dataArr);

      if($res === true) {
        // 登録成功時はログインページへ
        header('Location: ' . Bootstrap::APP_URL . 'login.php');
        exit();
      } else {
        var_dump($query);
        var_dump($res);
        // 登録失敗時は登録画面に戻る
        $template = 'regist.twig';

        // エラーの配列を空にしてもう一度登録画面に戻る
        foreach($dataArr as $key => $value) {
          $errArr[$key] = '';
        }
      }
    break;
}

// 右のものを左のそれぞれの変数に格納する
// $yearArr $monthArr $dayArrにそれぞれgetDateからの返り値を格納している
list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

// 上で入れたものをそれぞれ$contextに格納している
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

// $template = $twig->loadTemplate($template);
$template = $twig->load($template);
$template->display($context);

