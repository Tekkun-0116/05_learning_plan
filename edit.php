<?php

require_once('config.php');
require_once('functions.php');

$id = $_GET['id'];

$dbh = connectDb();

$sql = "select * from plans where id = :id";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(":id", $id);
$stmt->execute();

$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  $title = $_POST['title'];
  $due_date = $_POST['due_date'];
  
  $errors = [];
  
  if ($title == $plan['title']) {
    $errors['title'] = 'タスク名を入力して下さい';
  }

  if ($due_date == $plan['due_date']) {
    $errors['due_date'] = '期限を入力して下さい';
  }

  if (empty($errors)) {
    $sql = "update plans set title = :title," . "due_date = :due_date" . "update_at = now()" . "where id = :id";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":due_date", $due_date);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

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
  <title>編集</title>
</head>
<body>
  <h1>編集</h1>
  <p>
  <form action="" method="post">
    <input type="text" name="title" id="" value="<?php echo h($plan['title']); ?>">
    期限日:
    <input type="date" name="due_date" id="" value="<?php echo  h($plan['due_date']); ?>"> 
    <input type="submit" value="編集"><br>
    <?php if (count($errors) > 0) : ?>
      <ul style="color:red;">
        <?php foreach ($errors as $key => $value) : ?>
          <li><?php echo h($value); ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    </form>
  </p>
  </form>
  </p>
</body>
</html>