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

  // 対象ゲームの詳細取得
  public function getGameDetail($game_id)
  {
    $res = $this->db->select('games', 'game_id, game_title', 'game_id = ?', [$game_id]);
    return $res;
  }
  
}
