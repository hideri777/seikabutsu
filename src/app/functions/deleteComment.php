<?php

namespace App\functions;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;
use App\config\Bootstrap;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);

$comment_id = filter_input(INPUT_POST, 'comment_id');

$res = $db->setQuery('DELETE FROM comments WHERE comment_id = ?', [$comment_id]);
