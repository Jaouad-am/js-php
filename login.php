<?php
session_start();
require_once "pdo.php";

if (isset($_POST['cancel'])) {

    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
if (isset($_POST['pass']) && isset($_POST['email'])) {
    $check = hash('md5', $salt . $_POST['pass']);

    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');

    $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));


    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {

        $_SESSION['name'] = $row['name'];

        $_SESSION['user_id'] = $row['user_id'];


        header("Location: index.php");

        return;
    }


}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Jaouad Amamou Database</title>
</head>
<body>
<div class="container">
    <p><a href='login.php'>Please log in</a></p>;
    <?php
    if (isset($_SESSION['error'])) {
        echo('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
        unset($_SESSION['error']);
    }
    ?>
    <form method="POST" action="login.php">
        User Name <input type="text" name="email" id="em"><br/>
        Password <input type="text" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate(); return false" value="Log In">
<input type="submit" name="cancel" value="Cancel">
    </form>
    <p>
       
    </p>
    <!-- Hint: The password is php123 -->
</p>

<script type="text/javascript">
function doValidate() {

console.log('Validating...');
try {
pw = document.getElementById('id_1723').value;
em = document.getElementById('em').value;
console.log("Validating name="+em);
console.log("Validating pw="+pw);

if (pw == null || pw == "") {
alert("Both fields must be filled out");
return false;
}
if(em == null || em == ""){
  alert("E-mail must not be empty")
return false;
}

return true;

} catch(e) {

return false;

}

return false;

}
</script>
</div>
</body>
</html>