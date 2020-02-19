<?php
namespace seikabutsu;

require_once __DIR__ . './../vendor/autoload.php';

session_start();
function logout() {
  unset($_SESSION['login']);
}
?>

<input type="button" value="ログアウト" onclick="<?php logout(); ?>">