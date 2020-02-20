<?php
namespace App\models;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;

// TODO: パスワードをDBに登録する前にpassword_hashで暗号化して登録する
class User
{
  // 初期化処理
  public ?PDODatabase $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function registUser($table, $dataArr)
  {
    $hashPass = password_hash($dataArr['password'], PASSWORD_BCRYPT);
    $dataArr['password'] = $hashPass;
    $res = $this->db->insert($table, $dataArr, 'regist_date');
    return $res;
  }

  public function login($user_name, $password)
  {
    $table = 'users';
    $column = 'user_id, user_name, password';
    $where = 'user_name = ?';
    $arrVal = [$user_name];
    $user = $this->db->select($table, $column, $where, $arrVal);

    // ユーザー名で検索かけて出てきたときかつ
    // 保存されているパスワードと入力されているパスワードでチェック
    if(!empty($user) && password_verify($password, $user[0]['password'])) {
      return $user;
    }

    return false;
  }
}