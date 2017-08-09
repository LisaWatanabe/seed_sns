<?php
  session_start();

// ログインボタンが押された時
    if (!empty($_POST)) {
      // email、passwordどちらも値（文字）が入力された時
      // 空じゃない !=
      // 存在があるのを分かった上で中身を知りたい場合 !empty
      // どちらも空じゃなかったら = どちらかが空でもだめ
      if ($_POST['email'] != '' && $_POST['password'] != '') {
      // ログイン処理

      // DBに接続
      // 外部ファイルから処理の読み込み
        require('dbconnect.php');

      // いま入力された情報の会員登録が存在しているかチェック
      // SELECT文で入力されたemail,passwordを条件にして一致するデータを取得
        $sql = 'SELECT * FROM `members` WHERE `email`=? AND `password`=?';
      // 暗号化されたパスワードを暗号化したものをsha1で取り出す
        $data = array($_POST['email'], sha1($_POST['password'])); //入力されたデータを指定(最初はemailだけ)
      // sql実行
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);

      // データ取得
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

      // 一致しているメールアドレスを入れてパスワードを何か入れたらvar_dumpで出してくる
        // var_dump($record);
        if ($record == false) {
          // ログイン失敗
          // fetchしてデータが取得できないときは$recordにfalseが代入される
          $error['login'] = 'failed';
        }else{
          // ログイン成功
          // ログインしている人のmember_idをSESSIONに保存
          $_SESSION['login_member_id'] = $record['member_id'];

          // 現在の時刻を表す情報をSESSIONに保存（1時間使用していない場合に自動でログアウトさせるため）
          // $_SESSION['time']このtimeは自分でつけた名前
          // 1970/1/1 0:0:0から現在までの秒数が保存される
          $_SESSION['time'] = time();

          // 存在していたらindex.phpへ移動 ログイン成功
          header('Location: index.php');
          exit();

        }


      // 存在していなかったら、ログイン失敗のマークを保存

      }else{
      // どちらかが空の時
      // 必須エラーメッセージを出すためのマークをつけて、$errorに保存
      // 'login'は勝手につけた名前
          $error['login'] = 'blank';

      }
  }




 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>ログイン</legend>
        <form method="post" action="" class="form-horizontal" role="form">
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="">
            </div>
          </div>
          <!-- ここにエラーメッセージを出すよう入力するclass="$error"のレイアウトを使用して「*メールアドレスとパスワードをご記入ください」と表示 -->
          <!-- 上でloginを定義しているのでそれを当てはめるだけで表示できるようになる -->
          <!-- セキュリティの安全上どちらが入っていないかわからないようにしておく -->
          <?php if (isset($error['login']) && ($error['login'] == 'blank')) { ?>
          <p class="error">*メールアドレスとパスワードをご記入ください</p>
          <?php } ?>
          <!-- // $error['login'] = 'failed';がfailedの時、class='error'のレイアウトを使用して「＊ログインに失敗しました。再度正しい情報でログインしてください」と表示しましょうjoin/index.phpを参考に -->
          <!-- ==でないと代入されてしまうので両方表示されてしまう -->
          <?php if (isset($error['login']) && ($error['login'] == 'failed')) { ?>
          <p class="error">*ログインに失敗しました。再度正しい情報でログインしてください</p>
          <?php }
           ?>


          <input type="submit" class="btn btn-default" value="ログイン">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
