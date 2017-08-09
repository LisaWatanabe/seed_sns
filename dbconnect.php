<?php
// DB接続
    $dsn = 'mysql:dbname=seed_sns;host=localhost';
    $user = 'root';
    $password='';
    $dbh = new PDO($dsn, $user, $password);
// 例外処理が使えるようになり、エラーメッセージを確認できるようにする
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->query('SET NAMES utf8');

// データベースが文字化けする場合はデータベースかこのページのutf8が間違っている可能性が高い


    ?>