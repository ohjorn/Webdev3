<?php 
require("Connect.php");

//LoginScreen.php
function validate($str) 
{
  return trim(htmlspecialchars($str));
}

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
  $Username = filter_var($_POST["Username"], FILTER_SANITIZE_STRING);
  $Username = validate($Username);
  $Password = filter_var($_POST["Password"], FILTER_SANITIZE_STRING);
  $Password = validate($Password);

  if (!empty($_POST["Username"]) && (!empty($_POST["Password"])) && validateUser($Username, $Password))
  {
    if (session_status() == PHP_SESSION_NONE) 
    {
      session_start();
    }
    $_SESSION['Username'] = $Username;
    header("Location: MainMenu.php");
  }
  else 
  {
    echo"De ingevoerde gegevens zijn incorrect.";
  }
}
//LoginScreen.php ^^^^


//UserAdministration.php
if ((!empty($_POST["CreateAcc"])) && ($_POST["CreateAcc"] == "Aanmaken"))
{
  validateNewAccount();
}

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
    if ((!($Result == $Username)) && ($password == $password2) && (strlen($Username) > 2))
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
//UserAdministration.php ^^^^
?>