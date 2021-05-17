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

	// Check if all filds of positions are complete
	for ($cont_year=1; $cont_year <= 9 ; $cont_year++)	{
		if (isset($_POST['year'.$cont_year]) && isset($_POST['desc'.$cont_year])) {
			if (strlen($_POST['year'.$cont_year]) < 1 || strlen($_POST['desc'.$cont_year]) < 1) {
				$_SESSION['error_add'] = 'All fields are required';
				header('Location: edit.php?profile_id='.$_POST['profile_id']);
				return;
			}
			// Check if year is numeric
			if (! is_numeric($_POST['year'.$cont_year.''])) {
				$_SESSION['error_add'] = "Position year must be numeric";
				header('Location: edit.php?profile_id='.$_POST['profile_id']);
				return;
			}
		}
	}


	// Clear out the old position entries
	$stmt_clear = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
	$stmt_clear->execute(array(':pid' => $_POST['profile_id']));


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

    $rank = 1;
    for ($i=1; $i <= 9; $i++) {
    	if (isset($_POST['year'.$i]) && isset($_POST['desc'.$i])) {
    		$stmt_pos = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rk, :yr, :dsc)');
    		$stmt_pos->execute(array(
    			':pid' => $_POST['profile_id'],
    			':rk' => $rank,
    			':yr' => $_POST['year'.$i],
    			':dsc' => $_POST['desc'.$i]));
    		$rank++;
    	}
    }

   
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
// Get data about Position
$data_pos =	$pdo->prepare('SELECT * FROM Position WHERE profile_id = :pid');
$data_pos->execute(array('pid' => $_REQUEST['profile_id']));
$row_pos = $data_pos->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Jaouad Amamou Edit Page</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

	
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
			Position: <input type="submit" id="addPos" value="+">
		</p>
		<div id="position_fields">
		<?php
		$cont_positions = 1;
		while ($row_pos !== false) {
			echo '<div id="position'.$cont_positions.'">';
			echo '<input type="hidden" name="position_id'.$cont_positions.'" value="'.$row_pos['position_id'].'">';
			echo '<p> Year: <input type="text" name="year'.$cont_positions.'" value="'.htmlentities($row_pos['year']).'" />';
			echo '<input type="button" value="-" onclick="$(\'#position'.$cont_positions.'\').remove(); return false;">';
			echo '</p>';
			echo '<textarea name="desc'.$cont_positions.'" rows ="8" cols="80">'.htmlentities($row_pos['description']).'</textarea>';
			echo '</div>';
			$row_pos = $data_pos->fetch(PDO::FETCH_ASSOC);
			$cont_positions++;
		}
		?>
		</div>
		<p>
		<input type="hidden" name="profile_id" value="<?= $_GET['profile_id']; ?>">
		<input type="submit" value="Save">
		<input type="submit" name="cancel" value="Cancel">
		</p>

		</form>

	</div>
	<script>
		var countPos = <?php echo $cont_positions - 1; ?>;

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