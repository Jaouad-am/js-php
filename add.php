<?php
session_start();

if (! isset($_SESSION['name'])) {
	die("ACCESS DENIED");
}

if (isset($_POST['cancel'])) {
	header("Location: index.php");
	return;
}
require_once "pdo.php";

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
	// Check if all filds of main form are complete
	if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
		$_SESSION['error_add'] = 'All fields are required';
		header("Location: add.php");
		return;
	}
	// Check if email have an @-sign
	if (! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$_SESSION['error_add'] = "Email address must contain @";
		header("Location: add.php");
		return;
	}

	// Check if all fields of positions are complete
    for ($cont_year=1; $cont_year <= 9 ; $cont_year++)  {
        if (isset($_POST['year'.$cont_year]) && isset($_POST['desc'.$cont_year])) {
            if (strlen($_POST['year'.$cont_year]) < 1 || strlen($_POST['desc'.$cont_year]) < 1) {
                $_SESSION['error_add'] = 'All fields are required';
                header('Location: add.php');
                return;
            }
            // Check if year is numeric
            if (! is_numeric($_POST['year'.$cont_year.''])) {
                $_SESSION['error_add'] = "Position year must be numeric";
                header("Location: add.php");
                return;
            }
        }
    }

	// Insert all the data into database
	$stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );

     $last_profile_id = $pdo->lastInsertId();

    $rank = 1;
    for ($i=1; $i <= 9; $i++) {
        if (isset($_POST['year'.$i]) && isset($_POST['desc'.$i])) {
            $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rk, :yr, :dsc)');
            $stmt->execute(array(
                ':pid' => $last_profile_id,
                ':rk' => $rank,
                ':yr' => $_POST['year'.$i],
                ':dsc' => $_POST['desc'.$i]));
        }
        $rank++;
    }


    $_SESSION['success'] = "Profile Added";
	header("Location: index.php");
	return;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Jaouad Amamou ADD Page</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <h1>Add Profile</h1>
    <?php
    if (isset($_SESSION['error_add'])) {
        echo('<p style="color: red;">' . htmlentities($_SESSION['error_add']) . "</p>\n");
        unset($_SESSION['error_add']);
    }
    ?>
    <form method="post">
        <p>First Name:
            <input type="text" name="first_name" size="60"/></p>
        <p>Last Name:
            <input type="text" name="last_name" size="60"/></p>
        <p>Email:
            <input type="text" name="email" size="30"/></p>
        <p>Headline:<br/>
            <input type="text" name="headline" size="80"/></p>
        <p>Summary:<br/>
            <textarea name="summary" rows="8" cols="80"></textarea>
        <p>
            Position: <input type="submit" id="addPos" value="+">
        </p>
        <div id="position_fields">
        </div>
        <p>
            <input type="submit" value="Add">
            <input type="submit" name="cancel" value="Cancel">
        </p>
    </form>
</div>
<script>
    var countPos = 0;

    $(document).ready(function(){
        window.console && console.log('Document ready called');
        $('#addPos').click(function(event){
            event.preventDefault();
            if ( countPos >= 9 ) {
                alert("Maximum of 9 position entries exceeded");
                return;
            }
            countPos++;
            window.console && console.log("Adding position "+countPos);
            $('#position_fields').append(
                '<div id="position'+countPos+'"> \
                <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                <input type="button" value="-" \
                    onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                </div>');
        });

      
    });

    </script>
</body>
</html>