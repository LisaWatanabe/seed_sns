<?php

    session_start();
// セッション情報の破棄（削除）
    $_SESSION = array(); //中身を空っぽの配列で上書き

// セッション情報を呼び出すために使うクッキー情報の削除
// コピペで使う
// 使いたい時にはこのままコピペで何も変えなくて良い
// クッキーの有効期限を過去にセットすると、すでに無効な状態にできるので削除したのと同じ状態にできる 42000秒前の意味 違う時間でも良い
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secuer'],$params['httponly']);
    }

// セッション情報を完全に消滅させる
    session_destroy();

// index.phpに戻る（ログインチェックのため）
    header('Location: index.php');

?>