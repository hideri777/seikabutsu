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

  // ランダムにゲームを取得
  public function getGames($gameCounts)
  {
    $res = $this->db->exeQuery("SELECT * FROM games AS gm, ( SELECT game_id FROM games ORDER BY RAND() LIMIT $gameCounts) AS randam WHERE gm.game_id = randam.game_id; LIMIT $gameCounts");
    return $res;
  }

  // 対象ゲームの詳細取得
  public function getGameDetail($game_id)
  {
    $res = $this->db->select('games', 'game_id, game_title', 'game_id = ?', [$game_id]);
    return $res;
  }
  
}
