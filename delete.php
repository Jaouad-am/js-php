<?php
require_once "pdo.php";
session_start();

if (isset($_SESSION['name']) == false) {
die('Not logged in');
}

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

// check that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing  profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT profile_id FROM profile where profile_id = :id");
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>

<html>
<head>
<title>Jaouad Amamou Delete Page</title>
</head>
<body>
<div class="container">

<p>Confirm: Are You Sure You Want To Delete This Profile With ID = <?= htmlentities($row['profile_id']) ?> ?</p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" value="Delete" name="delete">
<a href="index.php">Cancel</a>
</form>

</div>
</body>
</html>