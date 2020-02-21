<?php

namespace App\models;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;

class Post
{
  // 初期化処理
  public ?PDODatabase $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  // トップページの最近の投稿を取得する
  public function getRecentPost()
  {
    $query = "SELECT p.post_id, p.title, p.body, p.created_date, u.user_name FROM posts p LEFT JOIN users u ON p.user_id = u.user_id ORDER BY p.created_date DESC LIMIT 10";

    $res = $this->db->exeQuery($query);

    // falseじゃなく0個でなく、ちゃんとチェック
    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function getPostDetail($post_id)
  {
    $query = "SELECT p.post_id, p.title, p.body, p.created_date, u.user_id, u.user_name FROM posts p LEFT JOIN users u ON p.user_id = u.user_id WHERE post_id = $post_id";

    $res = $this->db->exeQuery($query);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function insertComment($table, $user_id, $comment, $post_id)
  {
    $insData = [
      'body' => $comment,
      'user_id' => $user_id,
      'target_posts_id' => $post_id,
      'created_date' => date('Y-m-d H:i:s'),
    ];
    $res = $this->db->insert($table, $insData);
    return $res;
  }

  public function getCommentsInfo($post_id)
  {
    $query = "SELECT u.user_name, c.body, c.created_date FROM comments c LEFT JOIN users u ON c.user_id = u.user_id WHERE target_posts_id = $post_id";

    $res = $this->db->exeQuery($query);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  // public function getItemDetailData($item_id)
  // {
  //   $table = ' item ';
  //   $col = ' item_id, item_name, detail, price, image, ctg_id ';

  //   $where = ($item_id !== '') ? 'item_id = ?' : '';
  //   // カテゴリーによって表示させるアイテムを変える
  //   $arrVal = ($item_id !== '') ? [$item_id] : [];

  //   $res = $this->db->select($table, $col, $where, $arrVal);

  //   return ($res !== false && count($res) !== 0) ? $res : false;
  // }
}