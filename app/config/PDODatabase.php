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
   * @param $db_host データベースのサーバー名
   * @param $db_user データベースのユーザー名
   * @param $db_pass データベースのパスワード
   * @param $db_name データベース名
   */
  private $dsn, $db_host, $db_user, $db_pass, $db_name;

  /**
   * SQL発行時のオプション
   * @param $order 並び替え
   * @param $limit 数の制限
   * @param $offset 取得開始位置
   * @param $groupby グループ化
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


  public function setQuery($query = '', $arrVal = [])
  {
    // stmt プリペアドステートメントの略？
    // prepateはパラメータを自動的にエスケープしてくれる
    // 値が固定でないSQLを使うとときにprepareを使うといい
    // 今回なら$queryのところに?があって
    // arrValで実際の値を入れてexecuteで実行
    $stmt = $this->dbh->prepare($query);
    $stmt->execute($arrVal);
  }

  public function select($table, $column = '', $where = '', $arrVal = [])
  {
    $sql = $this->getSql('select', $table, $where, $column);
    $this->sqlLogInfo($sql, $arrVal);

    $stmt = $this->dbh->prepare($sql);
    // executeで直接値を指定しているのでbindしなくてもOK
    $res = $stmt->execute($arrVal);
    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    // データを連想配列に格納
    $data = [];
    // fetchでデータを取得する
    while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data, $result);
    }
    return $data;
  }

  public function count($table, $where = '', $arrVal = [])
  {
    $sql = $this->getSql('count', $table, $where);

    // SQL文をログに吐き出すのはデバックようにとても大切
    $this->sqlLogInfo($sql, $arrVal);
    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    // fetchでデータを取得する
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return intval($result['NUM']);
  }

  // もとが$orderになっていたが、、、
  // public function setOrder($order = '')
  public function setOrder($strOrder = '')
  {
    if ($strOrder !== '') {
      $this->order = 'ORDER BY' . $strOrder;
    }
  }

  public function setLimitOff($limit = '', $offset = '')
  {
    if ($limit !== "") {
      $this->limit = "LIMIT" . $limit;
    }
    if ($offset !== "") {
      $this->offset = "OFFSET" . $offset;
    }
  }

  public function setGroupBy($groupby)
  {
    if ($groupby !== "") {
      $this->groupby = 'GROUP BY' . $groupby;
    }
  }



  // selectかcountのときの挙動をコントロール
  private function getSql($type, $table, $where = '', $column = '')
  {
    switch ($type) {
      case 'select':
        // カラムの指定があればそのまま入れる
        // カラムの指定がなければ全部取得する
        $columnKey = ($column !== '') ? $column : "*";
        break;

      case 'count':
        $columnKey = 'COUNT(*) AS NUM';
        break;

      default:
        break;
    }

    $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';
    $other = $this->groupby . " " . $this->order . " " . $this->limit . " " . $this->offset;

    // sql文の作成
    $sql = "SELECT " . $columnKey . " FROM " . $table . $whereSQL . $other;

    return $sql;
  }

  public function insert($table, $insData = [])
  {
    $insDataKey = [];
    $insDataVal = [];
    $preCnt = [];

    $columns = '';
    $preSt = '';

    // 引数で渡されてきた$insDataをキーバリューに分解
    // その数だけ$preCntに?を配列として格納
    foreach ($insData as $col => $val) {
      $insDataKey[] = $col;
      $insDataVal[] = $val;
      $preCnt[] = '?';
    }

    $columns = implode(",", $insDataKey);
    $preSt = implode(",", $preCnt);

    // ex. item_idを追加するのであれば
    // INSERT INTO  cart  (customer_no,item_id) VALUES (?,?) →これを?に入れる [1,1]
    $sql = "INSERT INTO "
      . $table
      . " ("
      . $columns
      . ") VALUES ("
      . $preSt
      . ") ";

    $this->sqlLogInfo($sql, $insDataVal);

    // prepareの時は?で
    // executeするときに実際の値を?に入れている
    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($insDataVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    return $res;
  }

  public function update($table, $insData = [], $where, $arrWhereVal = [])
  {
    $arrPreSt = [];
    foreach ($insData as $col => $val) {
      $arrPreSt[] = $col . " = ? ";
    }

    // 原本implode($arrPreSt, ',');
    // implodeの引数順番が逆？？
    $preSt = implode(',', $arrPreSt);

    // sql文の作成
    // UPDATE  cart  SET delete_flg = ?  WHERE  crt_id = ? 
    $sql = "UPDATE "
      . $table
      . " SET "
      . $preSt
      . " WHERE "
      . $where;

    // array_valuesで配列の全ての値を出す
    // 連想配列ではなく、数字の添字をつけた配列

    // UPDATE  cart  SET delete_flg = ?  WHERE  crt_id = ?  [1,6]
    // updateDataは更新するinsDataの値とwhereの場所の指定の値がarrWhereValにはいっている
    // updateする対象のカラムも?でwhereで指定する場所も?なので
    // それをつなげたのがupdateData
    $updateData = array_merge(array_values($insData), $arrWhereVal);
    $this->sqlLogInfo($sql, $updateData);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($updateData);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    return $res;
  }

  public function getLastId()
  {
    return $this->dbh->lastInsertId();
  }

  private function catchError($errArr = [])
  {
    // dieで一旦処理を止める
    // exitと同じ
    $errMsg = (!empty($errArr[2])) ? $errArr[2] : "";
    die("SQLエラーが発生しました。" . $errMsg);
    // die("SQLエラーが発生しました。" . $errArr[2]);
  }

  private function makeLogFile()
  {
    // __DIR__ と __FILE__は基本同じ
    // https://sachips.byeto.jp/php/dirname__file__or__dir__.html
    // __DIR__ で一個上?
    $logDir = dirname(__DIR__) . "/logs";
    if (!file_exists($logDir)) {
      mkdir($logDir, 0777);
    }
    $logPath = $logDir . '/shopping.log';
    if (!file_exists($logPath)) {
      touch($logPath);
    }
    return $logPath;
  }

  private function sqlLogInfo($str, $arrVal = [])
  {
    $logPath = $this->makeLogFile();
    // 1つ目の%sにdate関数、2つ目に$str、3つ目にimplodeで結合されたarrValが入ってくる
    // sprintfにはsで文字列、dで数字など指定できる、詳しくはPHPManual
    $logData = sprintf(
      "[SQL_LOG:%s]: %s [%s]\n",
      date('Y-m-d H:i:s'),
      $str,
      implode(",", $arrVal)
    );
    // destinationは$logPath、対象ファイルは$logData、ログのタイプは今回は3
    error_log($logData, 3, $logPath);
  }
}