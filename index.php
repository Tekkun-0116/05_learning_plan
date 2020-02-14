<?php

require_once('config.php');
require_once('functions.php');

$dbh = connectDb();

// レコードの取得(未完了)
$sql = "select * from plans where status = 'notyet'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$notyet_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// レコードの取得(完成の場合)
$sql2 = "select * from plans where status = 'done'";
$stmt = $dbh->prepare($sql2);
$stmt->execute();
$done_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 新規タスクの追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  $title = $_POST['title'];
  $due_date = $_POST['due_date'];
  
  $errors = [];
  
  if ($title == '') {
    $errors['title'] = 'タスク名を入力して下さい';
  }

  if ($due_date == '') {
    $errors['due_date'] = '期限を入力して下さい';
  }

  if (empty($errors)) {
    $sql = "insert into plans (title, due_date, created_at, updated_at) values (:title, :due_date, now(), now())";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":due_date", $due_date);
    $stmt->execute();

    // index.phpに戻る
    header('Location: index.php');
    exit;
  }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>学習管理アプリ</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>学習管理アプリ</h1>
    <form action="" method="post">学習内容:
      <input type="text" name="title" id=""><br>
      期限日:
      <input type="date" name="due_date" id="">
      <input type="submit" value="追加"><br>
      <?php if ($errors) : ?>
        <ul style="color:red;">
          <?php foreach ($errors as $error) : ?>
            <li>
              <?php echo h($error); ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <? endif; ?>
    </form>

<h2>未達成</h2>
  <ul>
    <?php foreach ($notyet_plans as $plan) : ?>
      <?php if (date('Y-m-d') >= $plan['due_date']) : ?>
        <li class="expired">
      <?php else : ?>
        <li>
      <?php endif; ?>
      <a href="done.php?id=<?php echo h($plan['id']); ?>">[完了]</a>
      <a href="edit.php?id=<?php echo h($plan['id']); ?>">[編集]</a>
      <?php echo h($plan['title']); ?>
      <?php echo '･･･完了期限:' .  h(date('Y/m/d', strtotime($plan['due_date']))); ?>
    </li>
    <?php endforeach; ?>
  </ul>
  <hr>
  <h2>達成済み</h2>
  <ul>
    <?php foreach ($done_plans as $plan) : ?>
      <li>
        <?php echo($plan['title']); ?>
      </li>
    <?php endforeach; ?>
  </ul>
