<?php
namespace App\config;

/**
 * 汎用的な設定用ファイル
 * twigやpublicフォルダ内の読み込み
 * ログイン状態のチェックなど
 */

class Bootstrap
{
  const DB_HOST = 'localhost';
  const DB_NAME = 'seikabutsu_db';
  const DB_USER = 'seikabutsu_user';
  const DB_PASS = 'seikabutsu_pass';

  const APP_DIR = '/Applications/XAMPP/xamppfiles/htdocs/seikabutsu/';
  const TEMPLATE_DIR = self::APP_DIR . 'app/templates/';

  // キャッシュ、前回分のデータを利用して読み込み高速化する
  // TODO: 完成後false外す falseで利用しない
  const CACHE_DIR = false;
  // const CACHE_DIR = self::APP_DIR . 'app/templates_c/';

  const APP_URL = 'http://localhost/seikabutsu/public/';
  const ENTRY_URL = 'http://localhost/seikabutsu/app/';

  // TODO: SESSIONで扱う情報増えてきたらまとめてSession.phpを作る
  public static function returnLoginState() {
    session_start();
    $isLogin = isset($_SESSION['user_id']) ? true : false;
    if($isLogin) {
      $user_id = $_SESSION['user_id'];
      $user_name = $_SESSION['user_name'];
    } else {
      $user_id = '';
      $user_name = '';
    }
    return [
      'isLogin' => $isLogin,
      'user_id' => $user_id,
      'user_name' => $user_name
    ];
  }
}