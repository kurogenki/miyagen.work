<?php 
  session_start();
  $mode = 'input';
  $errmessage = [];
  if( isset($_POST['back']) && $_POST['back']) {
    // 何もしない
  } else if(isset($_POST['confirm']) && $_POST['confirm']) {
    // 確認画面
    if(!$_POST['fullname']) {
      $errmessage[] = "名前を入力して下さい";
    } elseif (mb_strlen($_POST['fullname']) > 100) {
      $errmessage[] = '名前は100文字以内にしてください';
    } 
    $_SESSION['fullname'] = htmlspecialchars($_POST['fullname'], ENT_QUOTES);
 
    if(!$_POST['email']) {
      $errmessage[] = "Eメールを入力して下さい";
    } elseif (mb_strlen($_POST['email']) > 200) {
      $errmessage[] = 'Eメールは200文字以内にしてください';
    } elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $errmessage[] = '正しいメールアドレスを入力してください';
    } 
    $_SESSION['email'] = htmlspecialchars($_POST['email'], ENT_QUOTES);
    

    if(!$_POST['message']) {
      $errmessage[] = "問い合わせ内容を入力して下さい";
    } elseif (mb_strlen($_POST['message']) > 500) {
      $errmessage[] = '問い合わせ内容は500文字以内にしてください';
    }
    $_SESSION['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);

    if($errmessage) {
      $mode = 'input';
    } else {
      $token = bin2hex(random_bytes(32));
      $_SESSION['token'] = $token;
      $mode = 'confirm';
    }
  } else if(isset($_POST['send']) && $_POST['send']) {
    // 送信ボタンを押した時
    if(!$_POST['token'] || !$_SESSION['token'] || !$_SESSION['email']) {
      $errmessage[] = '不正な処理が行われました';
      $_SESSION  = array();
      $mode = 'input';
    } else if($_POST['token'] != $_SESSION['token']) {
      $errmessage[] = '不正な処理が行われました';
      $_SESSION  = array();
      $mode = 'input';
    } else {
      $message = "お問い合わせを受け付けました \r\n"
              ."名前: " . $_SESSION['fullname'] . "\r\n"
              ."email: " . $_SESSION['email'] . "\r\n"
              ."お問い合わせ内容:\r\n"
              .preg_replace("/\r\n|\r|\n/", "\r\n", $_SESSION['message']);
      mail($_SESSION['email'], 'お問い合せありがとうございます。', $message);
      mail("kurochange0522@gmail.com", 'お問い合せありがとうございます。', $message); //問い合わせ先に送るメール処理
  
      $_SESSION = array();
      $mode = 'send';
    }
  } else { //この後の処理はgetでアクセスされた際の処理。
    // $_SESSION = array();
    	$_SESSION['fullname'] = "";
      $_SESSION['email']    = "";
      $_SESSION['message']  = "";
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>お問合せフォーム</title>
  <style>
    body {
      padding: 10px;
      max-width: 600px;
      margin: 0px auto;
    }

    div.button {
      text-align: center;
      margin-top: 10px;
    }
    
  </style>
</head>
<body>
  <?php if($mode == 'input') { ?>
      <!-- 入力画面 -->
      <?php 
        if($errmessage) {
          echo '<div class="alert alert-danger" role="alert">';
          echo implode('<br>', $errmessage);
          echo '</div>';
        }
      ?>
      <form class="col" action="./ContactForm.php" method="post">
        名前<input class="form-control col-6" type="text" name="fullname" value="<?php echo $_SESSION['fullname']?>"><br>
        Eメール<input class="form-control" type="email" name="email" value="<?php echo $_SESSION['email']?>"><br>
        お問合せ内容<br>
        <textarea class="form-control" name="message" cols="30" rows="10"><?php echo $_SESSION['message']?></textarea>
        <div class="button">
          <input class="btn btn-primary btn-lg" type="submit" name="confirm" value="確認">
        </div>
      </form>

  <?php } else if($mode == 'confirm') { ?>
    <!-- 確認画面 -->
    <form action="./ContactForm.php" method="post">
      <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
      名前 <?php echo $_SESSION['fullname'] ?><br>
      Eメール <?php echo $_SESSION["email"]?><br>
      お問合せ内容<br>
      <?php echo nl2br($_SESSION['message'])?><br>
      <input class="btn btn-primary btn-lg" type="submit" name="back" value="戻る">
      <input class="btn btn-primary btn-lg" type="submit" name="send" value="送信">
    </form>

  <?php } else {?>
    <!-- 完了画面 -->
    送信しました。お問い合わせありがとうございました。<br>
    createブラ
  <?php } ?>

  </form>
</body>
</html>