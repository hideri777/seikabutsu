<?php

namespace App\functions;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;
use App\config\Bootstrap;
use App\models\Post;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

header('Content-Type: application/json; charset=UTF-8');

$response_arr = [];
$user_id = filter_input(INPUT_POST, 'user_id');
$comment = filter_input(INPUT_POST, 'comment');
$post_id = filter_input(INPUT_POST, 'post_id');

$table = 'comments';
$post->insertComment($table, $user_id, $comment, $post_id);
// TODO: selectでpost_idが一致するコメント全部拾ってjsに返す
// 優先度は低め
$res = $post->getCommentsInfo($post_id);

// jsに返すデータ
// $response_arr = [
//   'user_id' => $user_id,
//   'comment' => $comment,
//   'post_id' => $post_id,
// ];

echo json_encode($res);
