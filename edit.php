<?php
    // var_dump($_GET['tweet_id']);
        require('dbconnect.php');

// 前の画面から送られてきたidを使ってSQLを作成
      $sql = 'SELECT * FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweet_id` =?';
      // ='.$_GET['tweet_id'];
      $data = array($_GET['tweet_id']);

// SQL文作成
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

// データ取得
      while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $tweets[] = array("tweet"=>$record['tweet'],
                      "nick_name"=>$record['nick_name'],
                        "created"=>$record['created'],
                       "tweet_id"=>$record['tweet_id'],
                   "picture_path"=>$record['picture_path']);
      }

// 取得したデータを表示に使用









// 更新ボタンが押された時、編集したつぶやきをUPDATEする
      if (isset($_POST['tweet'])) {
        // UPDATE文作成
        $sql = 'UPDATE `tweets` SET `tweet`=? WHERE `tweet_id`=?';
        $data = array($_POST['tweet'], $_GET['tweet_id']);
        // SQL文実行
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);

        // トップページに戻る
      header('Location: index.php');
      var_dump($sql);
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
                <li><a href="logout.html">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 content-margin-top">
  <form method="post" action="" class="form-horizontal" role="form">
  <!-- foreach文：指定された配列の個数分繰り返し処理を行う制御文 -->
  <?php foreach ($tweets as $tweet_each); { ?>
       <div class="msg">
          <img src="member_picture/<?php echo $tweet_each['picture_path']; ?>" width="100" height="100">
          <p>投稿者 : <span class="name"> (<?php echo $tweet_each['nick_name']; ?>) </span></p>
          <p>
            つぶやき : <br>
            <!-- phpを改行で書いてしまうとスペースが空いてしまうのでくっつけてかく -->
            <textarea name="tweet" class="form-control"><?php echo $tweet_each['tweet']; ?>
            </textarea>
          </p>
          <p class="day">
            <?php echo $tweet_each['created']; ?>
          </p>
          <!-- クラスで指定するとオレンジ色のボタンになる -->
          <input type="submit" value="更新" class="btn btn-warning btn-xs">
        </div>
  <?php } ?>
  </form>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
