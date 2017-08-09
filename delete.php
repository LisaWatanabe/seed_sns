<?php
session_start();
require('dbconnect.php');
// ログインチェック
    if (isset($_SESSION['login_member_id'])) {

// 指定されたtweet_idがログインユーザー本人のものかチェック
// 指定されたtweet_id,login_member_idでデータ取得
      $sql = 'SELECT * FROM `tweets` WHERE `member_id` =? AND `tweet_id` =?';
// 実行
      $data   = array($_SESSION['login_member_id'], $_GET['tweet_id']);
      $stmt   = $dbh->prepare($sql);
      $stmt->execute($data);
// $recordに１件も入っていないかったらfalseが代入される
      $record = $stmt->fetch(PDO::FETCH_ASSOC);

// if ($record != false) 一件取得できているとき
    if ($record != false) {
    // UPDATE文
    // 本人のものであれば、削除処理（論理削除）
      $sql = 'UPDATE `tweets` SET `delete_flag`=1 WHERE `tweet_id`=?';
      $data = array($_GET['tweet_id']);
      $stmt   = $dbh->prepare($sql);
      $stmt->execute($data);
}

}

// トップページに戻る
      header('Location: index.php');
// exit下に処理が何もなかったらつけなくても良い
      exit();
 ?>