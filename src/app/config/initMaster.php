<?php

namespace App\config;

class initMaster
{
  public static function getDate()
  {
    // 空の配列を作る
    $yearArr = [];
    $monthArr = [];
    $dayArr = [];

    // date('Y')で今の年、それに1を足して来年を作成
    $next_year = date('Y') + 1;

    // 年を作成
    for ($i = 1900; $i < $next_year; $i++) {
      // sprintfで4桁になるように04で記述している
      // 年だと実際は必要ない
      $year = sprintf("%04d", $i);
      $yearArr[$year] = $year . '年';
    }

    // 月を作成
    for ($i = 1; $i < 13; $i++) {
      // 月だと常に2桁になるような調整になる
      $month = sprintf("%02d", $i);
      $monthArr[$month] = $month . '月';
    }

    // 日を作成
    for ($i = 1; $i < 32; $i++) {
      $day = sprintf("%02d", $i);
      $dayArr[$day] = $day . '日';
    }

    return [$yearArr, $monthArr, $dayArr];
  }
}
