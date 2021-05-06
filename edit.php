	<?php
session_start();
require_once "pdo.php";

if (! isset($_SESSION['name'])) {
	die("ACCESS DENIED");
}

if (isset($_POST['cancel'])) {
	header("Location: index.php");
	return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
	// Check if all filds of main form are complete
	if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
		$_SESSION['error_add'] = 'All fields are required';
		header('Location: edit.php?profile_id='.$_POST['profile_id']);
		return;
	}
	// Check if email have an @-sign
	if (! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$_SESSION['error_add'] = "Email address must contain @";
		header('Location: edit.php?profile_id='.$_POST['profile_id']);
		return;
	}


	// """Update""" all data
	$stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid');
    $stmt->execute(array(
        ':pid' => $_POST['profile_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );

   
    $_SESSION['success'] = "Profile updated";
	header("Location: index.php");
	return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :id");
$stmt->execute(array(":id" => $_REQUEST['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Jaouad Amamou Edit Page</title>


	
</head>
<body>
	<div class="container">
		<h1>Editing Profile for <?= htmlentities($_SESSION['name']); ?></h1>
		<?php
		if (isset($_SESSION['error_add'])) {
			echo('<p style="color: red;">'.htmlentities($_SESSION['error_add'])."</p>\n");
			unset($_SESSION['error_add']);
		}
		?>
		<form method="post">
			
		<p>First Name:
		<input type="text" name="first_name" size="60" value="<?= htmlentities($row['first_name']) ?>"/></p>
		<p>Last Name:
		<input type="text" name="last_name" size="60" value="<?= htmlentities($row['last_name']) ?>"/></p>
		<p>Email:
		<input type="text" name="email" size="30" value="<?= htmlentities($row['email']) ?>"/></p>
		<p>Headline:<br/>
		<input type="text" name="headline" size="80" value="<?= htmlentities($row['headline']) ?>"/></p>
		<p>Summary:<br/>
		<textarea name="summary" rows="8" cols="80"><?= htmlentities($row['summary']) ?></textarea>
	
		<p>
		<input type="hidden" name="profile_id" value="<?= $_GET['profile_id']; ?>">
		<input type="submit" value="Save">
		<input type="submit" name="cancel" value="Cancel">
		</p>

		</form>

	</div>
</body>
</html>