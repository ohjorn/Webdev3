<?php 
  require("LoginScreenPHP.php");
  if (session_status() == PHP_SESSION_NONE) 
  {
    session_start();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap 4 Website Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" href="LoginScreen.css">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<style>

</style>
</head>
<body>
<div class="container">
 <img src="usericon.png"/>
<form action="LoginScreenPHP.php" method="post" name="form">
  <br><input type="text" name="Username" placeholder="Gebruikersnaam" >
  <br><input type="password" name="Password" placeholder="Wachtwoord">
  <br><input type="submit" name="Login" value="Inloggen" class="btn-login">
</form>
<?php 
  if (isset($_SESSION['WrongInput']))
  {
    echo "<span style='color:red;'> ".$_SESSION['WrongInput']."<span>";
    unset($_SESSION['WrongInput']);
  }
?>
</div>
</body>
</html>