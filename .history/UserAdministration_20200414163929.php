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
  <title>Gebruikers administratie NHLStenden Hogeschool</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="UserAdministration.css">
</head>
<body>
<div class="row justify-content-center text-center rowedit" id="adminlezermain">
  <div class="col-sm-3">
    <?php
      loadAdmins();
    ?>
  </div>
  <div class="col-sm-3">
    <?php 
      LoadReaders();
    ?>
  </div>
  </div>
<br>
  <div class="col-sm-3">
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
<div class="row justify-content-center text-center rowedit" id="alertfont">
  <?php
    if (isset($_SESSION["UserAdminEcho"]))
    {
      echo $_SESSION["UserAdminEcho"];
      unset($_SESSION["UserAdminEcho"]);
    }
    if(isset($_POST["DeleteUser"]))
    {
      if ($_POST["UserIDForm"] == $_SESSION["UserID"])
      {
        $_SESSION["UserAdminEcho"] = "U kan niet uw eigen account verwijderen.";
        header("Location: UserAdministration.php");
      }
      else
      {
        DeleteUserConfirmation($_POST["UserIDForm"]); 
      }
    }
  ?>
</body>
</html>