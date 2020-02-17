<?php
namespace App\config;

/**
 * 汎用的な設定用ファイル
 * twigやpublicフォルダ内の読み込みに活用する
 */

// Composerでインストールしたときに入ってくるautoload
// Composerで入れた多くのファイルのrequireonceをまとめて行うことができる
// require_once dirname(__DIR__) . './../vendor/autoload.php';

class Bootstrap
{
  const DB_HOST = 'localhost';
  const DB_NAME = 'seikabutsu_db';
  const DB_USER = 'seikabutsu_user';
  const DB_PASS = 'seikabutsu_pass';

  const APP_DIR = '/Applications/XAMPP/xamppfiles/htdocs/seikabustu/';
  const TEMPLATE_DIR = self::APP_DIR . 'app/templates/';

  // キャッシュ、前回分のデータを利用して読み込み高速化する
  // TODO: 完成後false外す falseで利用しない
  const CACHE_DIR = false;
  // const CACHE_DIR = self::APP_DIR . 'app/templates_c/';

  const APP_URL = 'http://localhost/seikabutsu/public/';
  // const ENTRY_URL = self::APP_URL . 'shopping/';

  // public static function checkLogin() {
  //   if(!isset($_SESSION['login'])) {
  //     header("Location: login.php");
  //   }
  // }

}