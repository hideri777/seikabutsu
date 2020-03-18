<?php

namespace App\functions;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;
use App\config\Bootstrap;
use App\models\User;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$user = new User($db);

$following_id = filter_input(INPUT_POST, 'following_id');
$followed_id = filter_input(INPUT_POST, 'followed_id');
$isFollow = filter_input(INPUT_POST, 'isFollow');

$dataArr = [
  'following_id' => $following_id, 
  'followed_id' => $followed_id
];

if ($isFollow === 'true') {
  $res = $user->followUser($dataArr);
} else {
  $res = $user->unfollowUser($dataArr);
}

echo $res;