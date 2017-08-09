<?php
session_start();
require('dbconnect.php');
// ログインチェック
    if (isset($_SESSION['login_member_id'])) {


// DELETE文の構文は , ではなく AND を使う
      $sql = 'DELETE FROM `likes` WHERE `tweet_id` ='.$_GET['tweet_id'].' AND `member_id` ='.$_SESSION['login_member_id'];
      var_dump($sql);
      $stmt   = $dbh->prepare($sql);
      $stmt->execute();

}


// トップページに戻る
      header('Location: index.php');
// exit下に処理が何もなかったらつけなくても良い
      exit();
 ?>