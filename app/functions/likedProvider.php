<?php

namespace App\functions;

require_once __DIR__ . './../../vendor/autoload.php';

use App\config\PDODatabase;
use App\config\Bootstrap;
use App\models\Post;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);