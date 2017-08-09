<?php

    if (!empty($_POST)) {
      if ($_POST['nick_name'] == '') {
      $error['nick_name'] = 'blank';
      }
      if ($_POST['email'] == '') {
      $error['email'] = 'blank';
      }
      if ($_POST['password'] == '') {
      $error['password'] = 'blank';
      } else {
        if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
        }
      }
      $file_name = $_FILES['picture_path']['name'];
      if (!empty($file_name)) {
        $ext = substr($file_name, -3);
        $ext2 = substr($file_name, -4);
        if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png' && $ext2 != 'jpeg') {
        $error['picture_path'] = 'type';
        }
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
              <?php if (isset($_POST['nick_name'])) { ?>
          <!-- valueをつけてechoを入れ履歴を残す htmlspecialcharsでサニタイズする -->
                <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun" value="<?php echo htmlspecialchars($_POST['nick_name'],ENT_QUOTES,'UTF-8'); ?>">
              <?php } else { ?>
                <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun">
                 <?php }  ?>
              <!-- isset 存在していた時 -->
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
            <?php } else {?>
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
              <p class="error">ファイルは、jpg、gif、png、jpegのいずれかを指定してください</p>
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
