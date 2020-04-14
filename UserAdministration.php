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
    <style>

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        .sidebar {

            width: 0px;
            position: fixed;
            top: 80px;
            left: 0;
            height: 100vh;
            z-index: 999;
            background: ghostwhite;
            color: ghostwhite;
            transition: all 0.3s;
            bottom: 0px;
            overflow-y: scroll;
        }



        /* Style the submit button */
        .button1 {
            float: left;
            width: 100%;
            height: 40px;
            background: #2196F3;
            color: white;
            font-size: 14px;
            border: 1px solid grey;
            border-left: none; /* Prevent double borders */
            cursor: pointer;
        }

        .button1:hover {
            background: #0ca6da;
        }

        .searchbar{
            float:right;
            border-radius: 5px;
            height: 40px;

            font-size: 14px;
            border: 1px solid grey;
            background: white;
        }

        .license{
            width: 100%;
            height: 40px;
            background: white;
            color: gray;
            font-size: 14px;
            border: 1px solid lightgray;
            border-left: none; /* Prevent double borders */
            cursor: pointer;

        }

        #main{
            margin-top: -40px;
            transition: all 0.3s;
        }



        .btn{
            color: white;

        }

        .btn1{
            color: black;

        }

        .modalstyle {

            text-align: center;
            border-radius: 5px;
            font-size: 13px;
            padding: 10px 5px 4px;
            margin: 250px 10px 5px;
        }
        .container{
            margin-top: 100px;
        }


    </style>
</head>
<body>
<nav style="height: 80px; background: #008487; border-radius: 0px;" class="navbar fixed-top navbar-expand-lg">
    <a class="navbar-brand" href="/"> <img  src="nhl.png" width="70px" height="70px" alt=""></a>


    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto navbar-right">
            <li class="nav-item">
                <h2>nieuwe gebruiker aanmaken</h2>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="row">
  <div class="col-6">
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

        <div class="col-3">
            <?php  loadAdmins();
            ?>
        </div>
  <div class="col-3">
      <?php


      LoadReaders();

      ?>
  </div>
    </div>
</div>


</body>
</html>