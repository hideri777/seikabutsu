<?php
namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;
use App\models\Post;

$isLogin = Bootstrap::returnLoginState();

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$post = new Post($db);

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]); 

// post_idを取得する
$post_id = (isset($_GET['post_id']) === true && preg_match('/^\d+$/', $_GET['post_id']) === 1) ? $_GET['post_id'] : '';

// item_idが取得できていない場合、商品一覧へ遷移させる
if($post_id === '') {
  // headerでリダイレクト処理
  header('Location: index.php');
}

// 記事情報を取得する
$postData = $post->getPostDetail($post_id);
// コメント取得
$commentdata = $post->getCommentsInfo($post_id);

$context = [];
$context['postData'] = $postData[0];
$context['commentData'] = $commentdata;
$context['login_user_id'] = $_SESSION['user_id'];
$context['isLogin'] = $isLogin;
if($_SESSION['user_id'] === $postData[0]['user_id']) $context['editable'] = true;
echo $twig->render('post.twig', $context);