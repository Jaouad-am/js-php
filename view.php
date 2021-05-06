<?php
session_start();
require_once "pdo.php";

if (isset($_GET['profile_id'])) {
	//Get data about Profile
	$data = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
	$data->execute(array('pid' => $_REQUEST['profile_id']));
	$row = $data->fetch(PDO::FETCH_ASSOC);

	if ($row === false) {
		$_SESSION['error'] = "Could not load profile";
		header("Location: index.php");
		return;
	}

} else {
	$_SESSION['error'] = "Missing profile_id";
	header("Location: index.php");
	return;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Jaouad Amamou DB</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
  
</head>
<body>
<div class="container">
	<h1>Profile information</h1>
	<p>First name: <?= htmlentities($row['first_name']) ?></p>
	<p>Last name: <?= htmlentities($row['last_name']) ?></p>
	<p>Email: <?= htmlentities($row['email']) ?></p>
	<p>Headline:<br/> <?= htmlentities($row['headline']) ?></p>
	<p>Summary:<br/> <?= htmlentities($row['summary']) ?></p>

	<a href="index.php">Done</a>
</div>
</body>
</html>