<?php 
require("Connect.php");

//Multiple files

////Trims the string.
function validate($str) 
{
  return trim(htmlspecialchars($str));
}
//// ^^^^

////Checks if the user logged in, if this is not the case they will be transported to the login page.
function IsLogged()
{
  if (!(isset($_SESSION["Username"])))
  {
    header("Location: LoginScreen.php");
  }
}
//// ^^^^

////Checks if the user is an admin, returns true if this is the case.
function IsAdmin()
{
  $Username = $_SESSION['Username'];
  $conn = connectDB();
  try
  {
    $sql = "SELECT Rechten FROM gebruiker WHERE `UniekeLoginNaam` = :Username";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $Result = $row["Rechten"];
    }
    if ($Result == 1)
    {
      return true;
    } 
  }
  catch(exception $ex)
  {
    echo "Er was een fout met de connectie met het database.";
  }
}
//// ^^^^

//Multiple files ^^^^

//LoginScreen.php

////Checks if the entered username and password match and transports the user to the main menu if this is the case.
function validateUser($Username, $Password)
{
  $conn = connectDB();
  
  try
  {
    $sql = "SELECT Wachtwoord FROM gebruiker WHERE `UniekeLoginNaam` = :Username";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $result = $row["Wachtwoord"];
    }
  }
  catch (exception $ex)
  {
    return false;
  }
  if (password_verify($Password, $result))
  {
    return true;
  }
  else 
  {
    return false;
  }
}

if ((!empty($_POST["Login"])) && ($_POST["Login"] == "Inloggen"))
{
  $Username = null;
  $Username = filter_var($_POST["Username"], FILTER_SANITIZE_STRING);
  $Username = validate($Username);
  $Password = null;
  $Password = filter_var($_POST["Password"], FILTER_SANITIZE_STRING);
  $Password = validate($Password);

  if (($Password != null) && ($Username != null) && validateUser($Username, $Password))
  {
    if (session_status() == PHP_SESSION_NONE) 
    {
      session_start();
    }
    $_SESSION['Username'] = $Username;
    header("Location: MainMenu.php");
    exit;
  }
  else 
  {
    if (session_status() == PHP_SESSION_NONE) 
    {
      session_start();
    }
    $_SESSION['WrongInput'] = "
      De ingevoerde gegevens zijn incorrect.<br>
      Als u uw wachtwood vergeten bent <br>
      kunt u contact opnemen met<br>
      Ton Koppers.<br>
    ";
    header("Location: LoginScreen.php");
    exit;
  }
}
//// ^^^^

//LoginScreen.php ^^^^

//MainMenu

////Clears the session when pressing the logout button.
if (!empty($_POST["logout-submit"]))
{
  $_SESSION = array();
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
    );
  }
  session_destroy();
  header("Location: LoginScreen.php");
  exit;
}
//// ^^^^


if (!empty($_POST["toggle-account-management"]))
{
  header("Location: UserAdministration.php");
  exit;
}

//MainMenu ^^^^

//UserAdministration.php

////Calls upon the "validateNewAccount" function when the "Aanmaken" knop is pressed.
if ((!empty($_POST["CreateAcc"])) && ($_POST["CreateAcc"] == "Aanmaken"))
{
  validateNewAccount();
}
//// ^^^^

////Checks if the entered username is not already in use, if the username is longer than 2 characters and if the 2 entered passwords are the same, if these are all correct it puts the new account in the database.
function validateNewAccount()
{
  $conn = connectDB();

  $Password = filter_var($_POST["Password"], FILTER_SANITIZE_STRING);
  $Password = validate($Password);
  $Password2 = filter_var($_POST["Password2"], FILTER_SANITIZE_STRING);
  $Password2 = validate($Password2);
  $Hashed = password_hash($Password, PASSWORD_DEFAULT);
  $Rights = $_POST["Rights"];
  $Username = $_POST["Username"];

  try
  {
    $sql = "SELECT UniekeLoginNaam FROM gebruiker WHERE UniekeLoginNaam = :Username";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $Result = $row["UniekeLoginNaam"];
    }
    if ((!($Result == $Username)) && ($Password == $Password2) && (strlen($Username) > 2))
    {
      $sql = "INSERT INTO `gebruiker` (`GebruikerID`, `UniekeLoginNaam`, `Wachtwoord`, `Rechten`) VALUES (NULL, :Username, :Password, :Rights);";

      $stmt = $conn->prepare($sql);
      $stmt->bindValue("Password", $Hashed, PDO::PARAM_STR);
      $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
      $stmt->bindValue("Rights", $Rights, PDO::PARAM_STR);
      if($stmt->execute()) 
      {
        echo "Het account is succesvol aangemaakt!";
      }
    }
    else
    {
      if ($Result == $Username)
      {
        echo "Deze gebruikersnaam is al in gebruik.";
      }
      elseif (strlen($Username) < 3)
      {
        echo "Je gebruikersnaam is te kort.";
      }
      if (!($Password == $Password2))
      {
        echo "De wachtwoorden komen niet overeen.";
      }
    }
  }
  catch(exception $ex)
  {
    echo "Er was een fout met de connectie met het database.";
  }
}
//// ^^^^

//UserAdministration.php ^^^^
?>