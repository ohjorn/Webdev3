<?php 
if ((!class_exists('connectDB'))) {
  include_once("Connect.php");
}

if (session_status() == PHP_SESSION_NONE) 
{
  session_start();
}

//Checks if the entered username and password match and transports the user to the main menu if this is the case.
function validateUser($Username, $Password)
{
  $conn = connectDB();
  
  try
  {
    $sql = "SELECT Wachtwoord, GebruikerID FROM gebruiker WHERE `UniekeLoginNaam` = :Username";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $result = $row["Wachtwoord"];
      $_SESSION['UserID'] = $row["GebruikerID"];
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
    $_SESSION['Username'] = $Username;
    $_SESSION["UserID"] = GetUserID($Username);
    header("Location: MainMenu.php");
    exit;
  }
  else 
  {
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
// ^^^
?>