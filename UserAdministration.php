<?php 
  require("UserAdministrationPHP.php");
  if (!class_exists('connectDB'))
  {
    include_once("Connect.php");
  }
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
  <link rel="stylesheet" href="UserAdministration.css">
</head>
<body>
<div class="row text-center">
  <div class="col-sm-3">
    <?php
      loadAdmins();
    ?>
  </div>
  <div class="col-sm-6">
    <?php
      if(isset($_POST["EditUser"]))
      {
        EditUserInformationForm($_POST["UserIDForm"], $_POST["RightsForm"], $_POST["UniqueLoginNameForm"]); 
      }
      else{
        CreateUserForm();
      }
      ?>
  </div>
</div>
<div class="row text-center">
  <div class="col-sm-3">
    <?php 
      LoadReaders();
    ?>
  </div>
  <?php
    if (isset($_SESSION["UserAdminEcho"]))
    {
      echo $_SESSION["UserAdminEcho"];
      unset($_SESSION["UserAdminEcho"]);
    }
    if(isset($_POST["DeleteUser"]))
    {
      DeleteUserConfirmation($_POST["UserIDForm"]); 
    }
  ?>
</div>
</body>
</html>