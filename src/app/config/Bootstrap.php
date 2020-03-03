<?php

namespace App\config;

/**
 * 汎用的な設定用ファイル
 * twigやpublicフォルダ内の読み込み
 * ログイン状態のチェックなど
 */

class Bootstrap
{
  const DB_HOST = 'mysql';
  const DB_NAME = 'gamelog_db';
  const DB_USER = 'gamelog_user';
  const DB_PASS = "gamelog_pass";

  const APP_DIR = '/var/www/html/';
  const TEMPLATE_DIR = self::APP_DIR . 'app/templates/';

  // キャッシュ、前回分のデータを利用して読み込み高速化する
  // TODO: 完成後false外す falseで利用しない
  const CACHE_DIR = false;
  // const CACHE_DIR = self::APP_DIR . 'app/logs/templates_c';

  // 外部に公開されるルートフォルダ
  const APP_URL = 'http://localhost/public/';

  // TODO: SESSIONで扱う情報増えてきたらまとめてSession.phpを作る
  public static function returnLoginState()
  {
    session_start();
    $isLogin = isset($_SESSION['user_id']) ? true : false;
    if ($isLogin) {
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
