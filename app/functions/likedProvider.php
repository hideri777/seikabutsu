<?php

namespace App\functions;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;
use App\config\Bootstrap;
use App\models\Post;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

$table = 'liked';
$user_id = filter_input(INPUT_POST, 'user_id');
$post_id = filter_input(INPUT_POST, 'post_id');
$isLiked = filter_input(INPUT_POST, 'isLiked');
$isFirst = filter_input(INPUT_POST, 'isFirst');

if ($isFirst === 'true') {
  $post->insertLiked($table, $user_id, $post_id);
} else {
  $post->toggleLiked($table, $user_id, $post_id, $isLiked);
}

echo 'ok';