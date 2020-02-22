<?php

namespace App\functions;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;
use App\config\Bootstrap;
use App\models\Post;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

header('Content-Type: application/json; charset=UTF-8');

$table = 'liked';
$user_id = filter_input(INPUT_POST, 'user_id');
$post_id = filter_input(INPUT_POST, 'post_id');
$isLiked = filter_input(INPUT_POST, 'isLiked');
$isFirst = filter_input(INPUT_POST, 'isFirst');

$updateLiked = 1;
$likeStates = [];

if ($isFirst === 'true') {
  $likeStates = $post->insertLiked($table, $user_id, $post_id);
} else {
  $likeStates = $post->toggleLiked($table, $user_id, $post_id, $isLiked);
}

// jsに返すデータ
$response_arr = [
  'updateLiked' => $likeStates[0],
  'likedCount' => $likeStates[1],
];

// [1 or 0でいいねの状態, いいねの総数]
echo json_encode($response_arr);