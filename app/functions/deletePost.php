<?php

namespace app\functions;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;
use App\config\Bootstrap;
use App\models\Post;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);

$post_id = filter_input(INPUT_POST, 'post_id');

$res = $db->setQuery('DELETE FROM posts WHERE post_id = ?', [$post_id]);
// TODO: コメントも一緒に削除?