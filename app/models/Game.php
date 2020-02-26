<?php

namespace App\models;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;

class Game
{
  // 初期化処理
  public ?PDODatabase $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getGameDetail($game_id)
  {
    $res = $this->db->select('games', 'game_id, game_title', 'game_id = ?', [$game_id]);
    return $res;
  }

  // トップページの最近の投稿を取得する
  // public function getRecentPost()
  // {
  //   $query = "SELECT p.post_id, p.title, p.body, p.created_date, u.user_name FROM posts p LEFT JOIN users u ON p.user_id = u.user_id ORDER BY p.created_date DESC LIMIT 5";

  //   $res = $this->db->exeQuery($query);

  //   // falseじゃなく0個でなく、ちゃんとチェック
  //   return ($res !== false && count($res) !== 0) ? $res : false;
  // }

  // public function getGameDetail($game_id)
  // {
  //   $query = "SELECT g.game_id, g.game_title, p.post_id, p.title, p.body, p.created_date, p.liked_count, u.user_id, u.user_name FROM games g LEFT JOIN posts p ON g.game_id = p.target_game_id LEFT JOIN users u ON p.user_id = u.user_id WHERE g.game_id = ?";

  //   $res = $this->db->exeQuery($query, [$game_id]);

  //   return ($res !== false && count($res) !== 0) ? $res : false;
  // }

  // public function getCommentsInfo($post_id)
  // {
  //   $query = "SELECT u.user_name, c.body, c.created_date FROM comments c LEFT JOIN users u ON c.user_id = u.user_id WHERE target_posts_id = ?";

  //   $res = $this->db->exeQuery($query, [$post_id]);

  //   return ($res !== false && count($res) !== 0) ? $res : false;
  // }

  public function insertComment($table, $user_id, $comment, $post_id)
  {
    $insData = [
      'body' => $comment,
      'user_id' => $user_id,
      'target_posts_id' => $post_id,
      'created_date' => date('Y-m-d H:i:s'),
    ];
    $this->db->insert($table, $insData);
  }

  public function getLikedState($user_id, $post_id)
  {
    $res = $this->db->select('liked', 'is_liked', 'user_id = ? AND post_id = ?', [$user_id, $post_id]);
    return $res;
  }

  public function insertLiked($table, $user_id, $post_id)
  {
    $insData = [
      'user_id' => $user_id,
      'post_id' => $post_id,
      'is_liked' => 1,
    ];

    $this->db->insert($table, $insData);
    $likedCount = $this->updateLikedCount('posts', $post_id);
    return [1, $likedCount];
  }

  public function toggleLiked($table, $user_id, $post_id, $isLiked)
  {
    $updateLiked = 0;
    if($isLiked === 'false') {
      $updateLiked = 1;
    } elseif ($isLiked === 'true') {
      $updateLiked = 0;
    }
     
    $insData = [
      // いいねの状態を反転
      'is_liked' => $updateLiked,
    ];

    $this->db->update($table, $insData, ' user_id = ? AND post_id = ? ', [$user_id, $post_id]);
    
    $likedCount = $this->updateLikedCount('posts', $post_id, $isLiked);

    return [$updateLiked, $likedCount];
  }

  private function updateLikedCount($table, $post_id, $isLiked = 'false')
  {
    $res = $this->db->select($table, 'liked_count', 'post_id = ?', [$post_id]);
    $likedCount = $res[0]['liked_count'];
    // もともといいねではなかった
    if($isLiked === 'false') {
      $likedCount += 1;
    } else {
      $likedCount -= 1;
    }
    $this->db->update($table, ['liked_count' => $likedCount], 'post_id = ?', [$post_id]);

    return $likedCount;
  }
}
