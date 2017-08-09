<?php
      session_start(); //SESSION変数を使うとき必ず記述
      // hello



      // データ取得
      // ログインしている人のデータを取得する
      // データベース接続がifの中に入っていると何回もDBに接続することになるのでよくないため、ここに書く
    // 外部ファイル内でエラーが出ると処理を中断する
    // include('');は表示を出すもの
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
    // 投稿を記録する（「つぶやく」ボタンをクリックした時）
      if (!empty($_POST)) {
      // つぶやき欄に何か書かれていたら、DBに値を送信し登録する
      // ''の部分をemptyで書いてしまうとユーザーが０やfalseを書いても送信されなくなってしまう
      if ($_POST['tweet'] != '') {

        // 投稿用のINSERT文作成
        // データーベースに会員登録するためのINSERT文を作成
        // sqlの? はサニタイズ
        // インジェクションを防ぐため
      $sql = 'INSERT INTO `tweets` SET `tweet`=?,
                                   `member_id`=?,
                              `reply_tweet_id`=?,
                                     `created`=NOW()';

// SQL文実行
// $_SESSION['login_member_id']で会員登録者の情報を取ってくる
      $data = array($_POST['tweet'], $_SESSION['login_member_id'], $_POST['reply_tweet_id']);
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
      //{ ["member_id"]    =>string(1) "4"
      //  ["nick_name"]    =>  string(4)"risa"
      //  ["email"]        =>string(14) "risa@gmail.com"
      //  ["password"]     =>string(40) "7ad42f4dacab56039644309d52e44093fd17706c"
      //  ["picture_path"] =>string(21) "2017071104460701.jpeg"
      //  ["created"]      =>string(19) "2017-07-11 10:46:08"
      //  ["modified"]     =>string(19) "2017-07-11 10:46:08"
      //}


// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー


// SELECT文作成（一覧表示用のデータを取得）
// 10件取得 LIMIT 0,10
// ページング機能
      $page = '';
// パラメータが存在していたらページ番号を取得する
      if (isset($_GET['page'])) {
        $page = $_GET['page'];
      }

      if ($page == '') {
        $page = 1;
      }

// 1以下のイレギュラーな数値が入って来た場合はページ番号を1とする
// max関数を使う URLに-1と入力されても1ページ目を表示する
      $page = max($page, 1);
// max(-1, 1)という指定の場合、大きい方の1が結果として返される


// データの件数から最大ページ数を計算する
// 取得するデータが何件あるか件数を取得したい時
// Mysqlの関数 COUNT(*)を使う
// このsql文を実行して取得したデータをvar_dumpで表示する
// `cnt`この文字と$cnt['この中を同じ言葉にする']
      $sql = "SELECT COUNT(*) AS `cnt` FROM `tweets` WHERE `delete_flag` =0";

      $stmt = $dbh->prepare($sql);
      $stmt->execute();

      $cnt = $stmt->fetch(PDO::FETCH_ASSOC);

      var_dump($cnt['cnt']);

// 1ページ目: $start = 0(1個目から取り出す)
// 2ページ目: $start = 10(10個目から取り出す)
// 3ページ目: $start = 20(20個目から取り出す)
      $start = 0;


      $tweet_number = 10; // 1ページに何個つぶやきを出すか指定
// ceil関数で小数点を切り上げた計算結果を代入できる
      $max_page = ceil($cnt['cnt'] / $tweet_number);


// パラメータのページ数が最大ページ数を超えていれば、最後のページ数に設定する
// min関数を使う 指定された複数の数値の中で最小の数値を返す関数
// $maxページを計算した後にこのminを使う必要がある
      $page = min($page, $max_page);
// min(100, 3)と指定されてたら3が返って来る



      $start = ($page -1) * $tweet_number;





// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー


// テーブル結合してそこからnick_nameを取り出す
// ORDER BY `created` DESC →作成日順が新しい順 ※DESCを書かないと古い順に並ぶ
// DESC降順 大きいものから並ぶ 名前など言葉でもいける
// ASC(省略可能)昇順 小さいものから並ぶ
      $sql = sprintf('SELECT * FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweets`.`delete_flag` =0 ORDER BY `tweets`.`created` DESC LIMIT %d, %d', $start, $tweet_number);

// SQL文実行
      // $data = array($start, $tweet_number);
      $stmt = $dbh->prepare($sql);
      $stmt->execute();

      $tweets = array();
// データを取得して配列に保存
      while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // $recordにfalseが代入された時、処理が終了します（データの一番最後まで取得指定しまい、次に取得するデータが存在しないとき）
        // $tweets[] = 配列の一番最後に新しいデータを追加する


        // like数の取得
      $sql = 'SELECT COUNT(*) as `like_count` FROM `likes` WHERE `tweet_id` ='.$record['tweet_id'];
      // while文でstmt使っている最中だから少し文字を変えておくとよい
      $stmt_cnt = $dbh->prepare($sql);
      $stmt_cnt->execute();
      $like_cnt = $stmt_cnt->fetch(PDO::FETCH_ASSOC);


      // like状態の取得（ログインユーザーごと）
      $sql = 'SELECT COUNT(*) as `like_count` FROM `likes` WHERE `tweet_id` ='.$record['tweet_id'].' AND `member_id`='.$_SESSION['login_member_id'];
      // while文でstmt使っている最中だから少し文字を変えておくとよい
      $stmt_flag = $dbh->prepare($sql);
      $stmt_flag->execute();
      $like_flag_cnt = $stmt_flag->fetch(PDO::FETCH_ASSOC);

      if ($like_flag_cnt['like_count'] == 0) {
        $like_flag = false; //likeされてない
      } else {
        $like_flag = true; //likeされてる
      }


        $tweets[] = array("tweet"=>$record['tweet'],
                      "nick_name"=>$record['nick_name'],
                        "created"=>$record['created'],
                       "tweet_id"=>$record['tweet_id'],
                   "picture_path"=>$record['picture_path'],
                 "reply_tweet_id"=>$record['reply_tweet_id'],
                      "member_id"=>$record['member_id'],
                      "like_flag"=>$like_flag,
                     "like_count"=>$like_cnt['like_count']
                 );
      }

// like_flagを使っていいね！かいいねを取り消すどちらか表示させる




    // 練習 配列を作りましょう
    // $tweets = array('aaa', 'bbb', 'ccc');
    // $tweets = array(array("tweet"=>"ハロー１",
    //                   "nick_name"=>"seedkun",
    //                     "created"=>"2017-07-13",
    //                    "tweet_id"=>1),
    //                 array("tweet"=>"ハロー２",
    //                   "nick_name"=>"seedku",
    //                     "created"=>"2017-07-13",
    //                    "tweet_id"=>2),
    //                 array("tweet"=>"ハロー３",
    //                   "nick_name"=>"seed",
    //                     "created"=>"2017-07-11",
    //                    "tweet_id"=>3)
    //                 );

    // var_dump($tweets[0]);

    // 問題；$tweet_eachを使って一覧のつぶやきの内容を書き換える


// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー


// 返信ボタンが押された時
    if (isset($_GET['tweet_id'])) {
      // 返信したいつぶやきデータを取得（ニックネームも一緒に）
      // sql文作成
      $sql = 'SELECT * FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweet_id` =?';
      $data = array($_GET['tweet_id']);
      // sql文実行
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
      // データ取得
      $record = $stmt->fetch(PDO::FETCH_ASSOC);
      // テキストエリアに表示する文字を作成（@返信したいつぶやき[つぶやいた人のニックネーム]）
      $re_str = '@'.$record["tweet"].'('.$record["nick_name"].')';
      $reply_tweet_id = $_GET['tweet_id'];

    } else {
      $reply_tweet_id = 0;
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
<!--   <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
      </div>
  </nav> -->

  <!-- 外部ファイルにエラーが発生しても処理を継続する（表示系の処理によく使用される） -->
  <!-- requireは外部ファイルにエラーが出ると処理を辞める違いがある -->
  <?php include('header.php') ?>

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
            <?php if (!empty($re_str)) { ?>
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo $re_str; ?></textarea>
            <?php } elseif (empty($restr)) { ?>
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
          <?php } ?>
          <input type="hidden" name="reply_tweet_id" value="<?php echo $reply_tweet_id; ?>" />
              </div>
            </div>

          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
            <!-- 前と次ボタンを値が入っていなかったら押せないようにする -->
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li>
                  <?php if ($page > 1) {?>
                  <a href="index.php?page=<?php echo $page -1; ?>" class="btn btn-default">前</a>
                  <?php }else{ ?>
                    前
                  <?php } ?>
                  </li>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li>
                  <?php if ($page < $max_page) { ?>
                  <a href="index.php?page=<?php echo $page +1; ?>" class="btn btn-default">次</a>
                <?php }else{ ?>
                  次
                <?php } ?>
                </li>
          </ul>

        </form>
      </div>

      <div class="col-md-8 content-margin-top">
<!-- foreach文：指定された配列の個数分繰り返し処理を行う制御文 -->
<!-- 回数を指定しなくて良い -->
      <?php foreach ($tweets as $tweet_each) { ?>

        <div class="msg">
          <img src="member_picture/<?php echo $tweet_each['picture_path']; ?>" width="48" height="48">
          <p>
<!-- Reの部分で自分のページに飛ばし選んだtweetを特定する -->
            <?php echo $tweet_each['tweet']; ?>
            <span class="name"> (<?php echo $tweet_each['nick_name']; ?>) </span>
            [<a href="index.php?tweet_id=<?php echo $tweet_each['tweet_id']; ?>">Re</a>]
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $tweet_each['tweet_id']; ?>">
            <?php echo $tweet_each['created']; ?></a>

<!-- ログインしている人が編集や削除設定ができる -->
<!-- $_SESSION['login_member_id']にいまログインしている人のmember_idが保存される -->
<!-- 削除の際にポップアップを表示させる javascriptで書いている -->
            <?php if ($_SESSION['login_member_id'] == $tweet_each["member_id"]) { ?>
            [<a href="edit.php?tweet_id=<?php echo $tweet_each['tweet_id']; ?>" style="color: #00994C;">編集</a>]
<!-- 何番を削除したいかGET送信で送られる -->
            [<a href="delete.php?tweet_id=<?php echo $tweet_each['tweet_id']; ?>" style="color: #F33;" onclick="return confirm('本当に削除しますか？')">削除</a>]
            <?php } ?>

<!-- いいねボタン -->
            <small><i class="fa fa-thumbs-up"></i><?php echo $tweet_each['like_count']; ?></small>
<!-- emptyを使ってもfalseを示すからempty使える -->
            <?php if($tweet_each['like_flag'] == false) { ?>
            <a href="like.php?tweet_id=<?php echo $tweet_each['tweet_id']; ?>"><small>いいね！</small></a>
            <?php }else{ ?>
            <a href="unlike.php?tweet_id=<?php echo $tweet_each['tweet_id']; ?>"><small>いいねを取り消す</small></a>
            <?php } ?>





<!-- 返信元のつぶやきを出す時と出さない時 -->
            <?php if(!empty($tweet_each["reply_tweet_id"])) { ?>
            <a href="view.php?tweet_id=<?php echo $tweet_each['reply_tweet_id']; ?>">返信元のつぶやき
            </a>
          </p>
          <?php } ?>
        </div>

      <?php } ?>
    </div>
  </div>
  <br>
  <br>
  <br>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
