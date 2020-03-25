<?php 
require("Connect.php");

function validate($str) 
{
  return trim(htmlspecialchars($str));
}

function validateUser($Username, $Password)
{
  $conn = connectDB();
  
  if (strlen($BridePass) > 5)
  {
    try
    {
      $sql = "SELECT Wachtwoord FROM gebruiker WHERE `UniekeLoginNaam` = :Username";
      $stmt = $conn->prepare($sql);
      $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
      $stmt->execute();
      foreach ($stmt->fetchAll() as $row) 
      {
        $result = $row["PassWD"];
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

  if (!empty($_POST["Username"]) && (!empty($_POST["Password"])) && validateBride($BrideLoginCode, $BridePass))
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
?>