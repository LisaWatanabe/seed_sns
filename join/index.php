<?php
    session_start();//SESSION変数使う時に頭に必ず指定する

// $errorという変数を用意 入力チェックに引っかかった項目情報を保存する
// $errorはhtmlの表示部分で、入力を促す表示を作る時に作る
// 例 もしnick_nameに何も入ってなかったら
// $error['nick_name'] = 'blank'; という情報を保存

// フォームからデータが送信された時
// emptyとは変数は存在する前提で中身が空かどうかを判定する関数
// emptyが認識する空の状態 null 0 false ''
// !empty($_POST)は何にもなくなかったら
if (!empty($_POST)) {
  // エラー項目の確認
  // ニックネームが未入力
      if ($_POST['nick_name'] == '') {
      $error['nick_name'] = 'blank';
      }
  // メールが未入力
      if ($_POST['email'] == '') {
        $error['email'] = 'blank';
      }
  // パスワードが未入力
      if ($_POST['password'] == '') {
        $error['password'] = 'blank';
      } else {
  // パスワードの文字チェック
  // ここのチェックした結果を使ってHTMLに「パスワードは４文字以上入力してください」というメッセージを表示させる
 // strlenはString(=文字列）のStrと、Length(=長さ）のLenの合成
        if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
      }
    }

// 画像ファイルの拡張子ファイルチェック
// jpg,gif,png この三つを許可して他はエラーにする
// 注意：画像ファイルの拡張子を自分で手入力して変えないこと
// 画像サイズは2MB以下のものを用意すること！
    $file_name = $_FILES['picture_path']['name'];
    // ファイルが指定された時に実行
    if (!empty($file_name)) {
    // 拡張子を取得
    // $file_nameに『3.png』が代入されている場合、後ろの３文字を取得する
    // substr()文字列の場所を指定して一部分を切り出す関数
    // -3は後ろから３文字を指定する意味 ただの3だと前から３文字の意味
      $ext = substr($file_name, -3);
// チャレンジ問題 チェックする拡張子にjpegを追加してみてください
      // 3文字のファイルの時と4文字のファイルの時
      $ext2 = substr($file_name, -4);
      if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png' && $ext2 != 'jpeg') {
        // $error['picture_path'] = 'type';だったら『ファイルは、jpg、gif、pngのいずれかを指定してください』というエラーメッセージを表示
      $error['picture_path'] = 'type';
      }
      // $ext = substr($file_name, -4);
      // if ($ext != 'jpeg') {
      //  }
      }

      // エラーがない場合
      if (empty($error)) {
      // 画像をアップロードする
      // アップロード後のファイル名を作成
        $picture_path = date('YmdHis').$_FILES['picture_path']['name'];
      // post送信された際に一度tmp_nameでサーバー上に仮に置かれる
        move_uploaded_file($_FILES['picture_path']['tmp_name'],'../member_picture/'.$picture_path);

      // セッションに値を保存
      // $_SESSION どの画面でもアクセス可能なスーパーグローバス変数
      // $_SESSIONに['join']を入れて次のthanks.phpの時に繋げられるようにする
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['picture_path'] = $picture_path;
      // check.phpに移動
        header('Location: check.php');
        exit(); //ここで処理を終了する
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
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <!--
      designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！
     -->

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
        <legend>会員登録</legend>
        <form method="post" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
          <!-- ニックネーム -->
          <!-- ニックネームの内容を残すための操作 -->
          <div class="form-group">
            <label class="col-sm-4 control-label">ニックネーム</label>
            <div class="col-sm-8">
            <!-- issetとは変数の存在を確認する関数 -->
            <!-- 変数の存在がある上でemptyで中身を確認する中身があるかどうかをemptyでみる -->
            <!-- $_POST['nick_name'])が存在したら=POST送信されたデータの中にnick_nameの項目があったら=ユーザーがnicknameを入力して、「確認へ」ボタンを押したあとだったら -->
            <!-- emptyでも書くことができる -->
              <?php if (isset($_POST['nick_name'])) { ?>
              <!-- ['nick_name']='' だったら
                   isset($_POST['nick_name'])→true
                   empty($_POST['nick_name'])→true

                   ['nick_name']='seedkun'だったら
                   isset($_POST['nick_name'])→true
                   empty($_POST['nick_name'])→false
                   !empty($_POST['nick_name'])→true
                    -->
          <!-- valueをつけてechoを入れ履歴を残す htmlspecialcharsでサニタイズする -->
                <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun" value="<?php echo htmlspecialchars($_POST['nick_name'],ENT_QUOTES,'UTF-8'); ?>">
              <?php } else { ?>
                <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun">
                 <?php }  ?>
              <!-- isset 存在していた時 -->
              <!-- $error['nick_name'] == 'blank')だけの判定をしたいが、これだけの場合$error['nick_name']が存在しない場合にエラー画面になる -->
              <!-- それを防ぐために最初に存在をチェックをつけている -->
              <?php if (isset($error['nick_name']) && ($error['nick_name'] == 'blank')) { ?>
                <p class="error">*ニックネームを入力してください。</p>
              <?php } ?>
            </div>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
          <!-- valueをつけてechoを入れ履歴を残す htmlspecialcharsでサニタイズする -->
            <?php if (isset($_POST['email'])) { ?>
                <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com" value="<?php echo htmlspecialchars($_POST['email'],ENT_QUOTES,'UTF-8'); ?>">
            <?php } else { ?>
                <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">
            <?php } ?>
            <?php if (isset($error['email']) && ($error['email'] == 'blank')) { ?>
                <p class="error">*メールアドレスを入力してください。</p>
            <?php } ?>

            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
            <?php if (isset($_POST['password'])) { ?>

          <!-- valueをつけてechoを入れ履歴を残す htmlspecialcharsでサニタイズする -->
              <input type="password" name="password" class="form-control" placeholder="4文字入力してください。" value="<?php echo htmlspecialchars($_POST['password'],ENT_QUOTES,'UTF-8'); ?>">
            <?php } else { ?>
              <input type="password" name="password" class="form-control" placeholder="4文字入力してください。">
            <?php } ?>

              <!-- 空白の時と4文字以下の時とどちらかなので別々で書く -->
            <?php if (isset($error['password']) && ($error['password'] == 'blank')) { ?>
                <p class="error">*パスワードを入力してください。</p>
            <?php } else { ?>
            <?php if (isset($error['password']) && ($error['password'] = 'length')) { ?>
                <p class="error">*パスワードを4文字入力してください。</p>
            <?php }} ?>

            </div>
          </div>
          <!-- プロフィール写真 -->
          <div class="form-group">
            <label class="col-sm-4 control-label">プロフィール写真</label>
            <div class="col-sm-8">
              <input type="file" name="picture_path" class="form-control">
            <?php if (isset($error['picture_path']) && ($error['picture_path'] == 'type')) { ?>
              <p class="error">ファイルは、jpg、gif、pngのいずれかを指定してください</p>
            <?php } ?>
            </div>
          </div>
          <input type="submit" class="btn btn-default" value="確認画面へ">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
  </body>
</html>
