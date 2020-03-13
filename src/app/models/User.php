<?php
namespace App\models;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;

class User
{
  // 初期化処理
  public $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function registUser($table, $dataArr)
  {
    $hashPass = password_hash($dataArr['password'], PASSWORD_BCRYPT);
    $dataArr['password'] = $hashPass;
    $dataArr['regist_date'] = date('Y-m-d H:i:s');
    $res = $this->db->insert($table, $dataArr);
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

  // ユーザーの基本情報
  public function getUserInfo($user_id)
  {
    $table = 'users';
    $column = '';
    $where = 'user_id = ?';
    $arrVal = [$user_id];
    $user = $this->db->select($table, $column, $where, $arrVal);
    return $user;
  }

  // そのユーザーの投稿一覧
  public function getUserPosts($user_id)
  {
    $query = "SELECT p.title, p.body, p.liked_count, p.target_game_id, p.created_date, p.update_date, g.game_title, g.rate_score FROM users u LEFT JOIN posts p ON u.user_id = p.user_id LEFT JOIN games g ON p.target_game_id = g.game_id WHERE p.user_id = ?";
    
    $res = $this->db->exeQuery($query, [$user_id]);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }
  
}