<?php
namespace App\config;
use PDO;

class PDODatabase
{
  /**
   * データベースハンドラ = new POD('DSN', 'ユーザー名', 'パスワード', オプション);
   * @var PDO
   */
  private $dbh = null;

  /**
   * データベース接続情報
   * @var $db_host データベースのサーバー名
   * @var $db_user データベースのユーザー名
   * @var $db_pass データベースのパスワード
   * @var $db_name データベース名
   */
  private $dsn, $db_host, $db_user, $db_pass, $db_name;

  /**
   * SQL発行時のオプション
   * @var $order 並び替え
   * @var $limit 数の制限
   * @var $offset 取得開始位置
   * @var $groupby グループ化
   */
  private $order, $limit, $offset, $groupby;

  public function __construct($db_host, $db_user, $db_pass, $db_name)
  {
    $this->dbh = $this->connectDB($db_host, $db_user, $db_pass,$db_name);
  }

  private function connectDB($db_host, $db_user, $db_pass, $db_name)
  {
    try {
      // dsnでcharsetの指定をしておく
      $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8mb4';
      $dbh = new PDO(
        $dsn, 
        $db_user, 
        $db_pass, 
        [
          // エラーコードの設定、PDOExceptionをスロー
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          // デフォルトで連想配列
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    // 投げられた例外クラスを受け取る
    catch (\PDOException $e) {
      // TODO: 接続失敗時にはユーザーに適切にメッセージを見せる
      var_dump($e->getMessage());
      exit();
    }

    return $dbh;
  }
}