<?php
    session_start(); //SESSION変数を使うとき必ず記述

      // データ取得
      // ログインしている人のデータを取得する
      // データベース接続がifの中に入っていると何回もDBに接続することになるのでよくないため、ここに書く
      require('dbconnect.php');

    // ログインチェック
    // ログイン中とみなせる条件
    // 1.セッションにログインしている人のmember_idが保存されている
    // 2.最後のアクションから1時間以内であること
      if (isset($_SESSION['login_member_id']) && ($_SESSION['time'] + 3600 > time())) {
      // 存在してたらログインしてる
      // 最終アクション時間を更新
      $_SESSION['time'] = time();

      // DBから取得できたら「ようこそ〇〇さん！」の部分をログインしてる人のnick_nameが表示されるように修正
      // login_member_idにmember_id が入っているのでそこから取得してくる
      // $recordに値が入っているのでそこをしたで取得する
      $sql = 'SELECT * FROM `members` WHERE `member_id` = ?';
      // ログインする際にはPOST送信で送信されているのでarray($POST())になるが
      // すでにログインしている人をSESSIONで情報を保存している
      // どこの画面からでも使えるSESSIONで使える
      // ログインしている情報をいろんなページで閲覧できるようにSESSIONで保存した方が使いやすい
      $data   = array($_SESSION['login_member_id']);
      $stmt   = $dbh->prepare($sql);
      $stmt->execute($data);
      $record = $stmt->fetch(PDO::FETCH_ASSOC);

    }else{


      // ログインしていない
      header('Location: login.php');

    }

    // if(!empty($_GET['type']) && ($_GET['type'] == 'delete')) {
    // // $sql = 'DELETE FROM `posts` WHERE `id`='.$_GET['id'];
    // // UPDATE文
    // $sql = 'UPDATE `twee` SET `delete_flag`=1 WHERE `tweet_id`='.$_GET['tweet_id'];

    // $stmt = $dbh->prepare($sql);
    // $stmt->execute();

    // header('Location: try.php');
    // exit();
    // }



    // 投稿を記録する（「つぶやく」ボタンをクリックした時）
      if (!empty($_POST)) {
      // つぶやき欄に何か書かれていたら、DBに値を送信し登録する
      // ''の部分をemptyで書いてしまうとユーザーが０やfalseを書いても送信されなくなってしまう
      if ($_POST['tweet'] != '') {

        // 投稿用のINSERT文作成
        // データーベースに会員登録するためのINSERT文を作成
        // sqlの? はサニタイズ
        // インジェクションを防ぐため
      $sql = 'INSERT INTO `twee` SET `tweet`=?,
                                 `member_id`=?,
                            `reply_tweet_id`=0,
                                   `created`=NOW()';

// SQL文実行
// $_SESSION['login_member_id']で会員登録者の情報を取ってくる
      $data = array($_POST['tweet'], $_SESSION['login_member_id']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);



        // 画面再表示（再送信防止）
      header('Location: index.php');
      exit();
      }
    }



//var_dump($data);
    //array(1) {  [0]=>  string(1) "4"}

//var_dump($sql);
    //string(45) "SELECT * FROM `members` WHERE `member_id` = ?"

//var_dump($record);
      //array(7)
      //{ ["member_id"]=>string(1) "4"
      //  ["nick_name"]=>  string(4)"risa"
      //  ["email"]=>string(14) "risa@gmail.com"
      //  ["password"]=>string(40) "7ad42f4dacab56039644309d52e44093fd17706c"
      //  ["picture_path"]=>string(21) "2017071104460701.jpeg"
      //  ["created"]=>string(19) "2017-07-11 10:46:08"
      //  ["modified"]=>string(19) "2017-07-11 10:46:08"
      //}



    $sql = 'SELECT * FROM `twee`';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    // postsデータ全件を格納する配列を空で用意


    while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $twee[] = array("tweet"=>$record['tweet'],
                    "nick_name"=>'Seedkun',
                      "created"=>$record['created'],
                     "tweet_id"=>$record['tweet_id']);

        }
        $twee_each = $twee[0];




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
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php
          echo $record['nick_name'];
         ?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="index.php" class="btn btn-default">前</a></li>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li><a href="index.php" class="btn btn-default">次</a></li>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">

          <?php foreach ($twee as $twee_each) { ?>
            <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            <?php echo $twee_each['tweet'];?><span class="name"> <?php echo $twee_each['nick_name'];?></span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $twee_each['tweet_id']; ?>">
              <?php echo $twee_each['created'];?>
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            <a href="try.php?type=delete&tweet_id=<?php echo $record[$i]['tweet_id'] ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
          </p>
        </div>
      <?php } ?>
<!--         <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき３<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.php">
              2016-01-28 18:03
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき２<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.php">
              2016-01-28 18:02
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき１<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.html">
              2016-01-28 18:01
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
      </div> -->


    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
