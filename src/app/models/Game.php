<?php

namespace App\models;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;

class Game
{
  // 初期化処理
  public $db = null;

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

  // ピックアップ、最新作品、人気作品に分割してreturnする
  public function splitGameData($gameData, $splitCounts)
  {
    $splitedGameData = array_chunk($gameData, $splitCounts);
    return $splitedGameData;
  }

  // 対象ゲームの詳細取得
  public function getGameDetail($game_id)
  {
    $res = $this->db->select('games', 'game_id, game_title, rate_score', 'game_id = ?', [$game_id]);
    return $res;
  }
  
}
