<?php 
  require("Function.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap 4 Website Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="LoginScreen.css">
<style>

</style>
</head>
<body>
<div class="container">
 <img src="usericon.png"/>
<form action="LoginScreen.php" method="post" name="form">
  <br><input type="str" name="Username" placeholder="Gebruikersnaam" ><br>
  <br><input type="password" name="Password" placeholder="Wachtwoord"><br><br>
  <input type="submit" name="Login" value="Inloggen" class="btn-login"><br><br>
  Als u uw wachtwood bent vergeten <br>
  kunt u contact opnemen met<br>
  Ton Koppers. 
</form><br>
</div>
</body>
</html>