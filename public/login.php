<?php
namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

use App\config\Bootstrap;
use App\config\PDODatabase;

// テンプレート指定
$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);

$user_name = '';
$password = '';
$err_msg = '';

// TODO: 後でUserクラス内に移す
if (isset($_POST['send'])) {
  if(isset($_POST['user_name']) && isset($_POST['password'])) {
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $table = 'users';
    $column = 'user_id, user_name, password';
    $where = 'user_name = ? AND password = ?';
    $arrVal = [$_POST['user_name'], $_POST['password']];
    $res = $db->select($table, $column, $where, $arrVal);
    
    if(!empty($res)) {
      session_start();
      $_SESSION['user_id'] = $res[0]['user_id'];
      $_SESSION['user_name'] = $res[0]['user_name'];
      header("Location: index.php");
      exit();
    } else {
      $err_msg = 'ユーザー名またはパスワードが違います';
    }
  }
}

$context = [];
$context['user_name'] = $user_name;
$context['err_msg'] = $err_msg;
echo $twig->render('login.twig', $context);