<?php
namespace App\config;

class Common
{
  private $dataArr = [];
  private $errArr = [];

  // 初期化
  public function __construct()
  {
    
  }

  public function errorCheck($dataArr)
  {
    $this->dataArr = $dataArr;
    // クラス内のメソッドを読み込む
    $this->createErrorMessage();
  
    $this->userNameCheck();
    $this->mailCheck();
    $this->passCheck();
    $this->birthCheck();

    return $this->errArr;
  }

  private function createErrorMessage()
  {
    foreach ($this->dataArr as $key => $val) {
      $this->errArr[$key] = '';
    }
  }

  private function userNameCheck()
  {
    if($this->dataArr['user_name'] === '') {
      $this->errArr['user_name'] = 'ユーザー名を入力してください';
    }
  }

  private function mailCheck()
  {
    if(preg_match('/^([a-zA-Z0-9])+([a-zA-z0-9\._-])*@([a-zA-Z0-9_-])+[a-zA-Z0-9\._-]+$/', $this->dataArr['email']) === 0) {
      $this->errArr['email'] = 'メールアドレスを正しい形式で入力してください';
    }
  }

  private function passCheck()
  {
    if(strlen($this->dataArr['password']) < 4 || strlen($this->dataArr['password']) > 20) {
      $this->errArr['password'] = 'パスワードは4文字以上20文字以下で入力してください';
    }
  }

  private function birthCheck()
  {
    if($this->dataArr['year'] === '') {
      $this->errArr['year'] = '生年月日の年を選択してください';
    }
    if($this->dataArr['month'] === '') {
      $this->errArr['month'] = '生年月日の月を選択してください';
    }
    if($this->dataArr['day'] === '') {
      $this->errArr['day'] = '生年月日の日を選択してください';
    }

    if(checkdate(intval($this->dataArr['month']), intval($this->dataArr['day']), intval($this->dataArr['year'])) === false) {
      $this->errArr['year'] = '正しい日付を入力してください';
    }

    if(strtotime($this->dataArr['year'] . '-' . $this->dataArr['month'] . '-' . $this->dataArr['day']) - strtotime('now') > 0) {
      $this->errArr['year'] = '正しい日付を入力してください';
    }
  }

  public function getErrorFlg()
  {
    $err_check = true;
    foreach ($this->errArr as $key => $value) {
      if($value !== '') {
        $err_check = false;
      }
    }
    return $err_check;
  }

  // public function imageCheck($file_obj)
  // {
  //   if ($file_obj['image']['error'] !== 4) {
  //     $tmp_image = $file_obj['image'];
  //     if ($tmp_image['error'] === 0 && $tmp_image['size'] !== 0) {
  //       if (is_uploaded_file($tmp_image['tmp_name']) === true) {
  //         $image_info = getimagesize($tmp_image['tmp_name']);
  //         $image_mime = $image_info['mime'];
  //       }
  //       if ($tmp_image['size'] > 1048576) {
  //         $this->errArr['image'] = 'アップロードできる画像のサイズは 1MB までです';
  //       } elseif (preg_match('/^image\/jpeg$/', $image_mime) === 0) {
  //         $this->errArr['image'] = 'アップロードできる画像の形式は JPEG 形式だけです';
  //       }
  //     }
  //   }
  // }
}