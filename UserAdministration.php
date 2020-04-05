<?php 
  require("Function.php");
  if (session_status() == PHP_SESSION_NONE) 
  {
    session_start();
  }
  try
  {
    IsLogged();
  }
  catch(exception $ex)
  {
    header("Location: LoginScreen.php");
    exit;
  }
  if (!(IsAdmin()))
  {
    header("Location: MainMenu.php");
    exit;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap 4 Website Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<style>

</style>
</head>
<body>
<form action="UserAdministration.php" method="post">
  Gebruikersnaam:<br><input type="str" name="Username"><br>
  Wachtwoord:<br><input type="password" name="Password"><br>
  Wachtwoord hertypen:<br><input type="password" name="Password2"><br><br>
  <input type="radio" name="Rights" value="0" checked>
  <label for="Lezer">Lezer</label><br>
  <input type="radio" name="Rights" value="1">
  <label for="Administrator">Administrator</label><br>
  <input type="submit" class="btn btn-primary" name="CreateAcc" value="Aanmaken">
</form><br>
<button type="submit" onclick="window.location.href = 'MainMenu.php';" class="btn btn-primary" name="BackToMainMenu">Terug</button>

</div>
</body>
</html>