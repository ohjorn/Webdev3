<?php 
if (!class_exists('connectDB'))
{
  include_once("Connect.php");
}
if (session_status() == PHP_SESSION_NONE) 
{
  session_start();
}

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
    $Result = NULL; 
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
        echo "De gebruikersnaam is te kort.";
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

function EditUserInformationForm($ID)
{
  echo "
    <form action=\"UserAdministration.php\" method=\"post\">
      <input type=\"hidden\" name=\"id\" value=\"".$ID."\">
      <label>Nieuwe gebruikersnaam:</label><br>
      <input type=\"text\" name=\"NewUsername\"><br>
      <label>Nieuwe wachtwoord:</label><br>
      <input type=\"password\" name=\"NewPassword\"><br>
      <label>Wachtwoord hertypen:</label><br>
      <input type=\"password\" name=\"NewPassword2\"><br>
      <input type=\"radio\" name=\"NewRights\" value=\"0\" checked>
      <label for=\"Lezer\">Lezer</label><br>
      <input type=\"radio\" name=\"NewRights\" value=\"1\">
      <label for=\"Administrator\">Administrator</label><br>
      <input type=\"submit\" class=\"btn btn-primary\" name=\"EditUserConfirmation\" value=\"Gegevens aanpassen\">
    </form>
  ";
}

function EditUserInformation($UserID)
{
  $conn = connectDB();

  $Password = filter_var($_POST["NewPassword"], FILTER_SANITIZE_STRING);
  $Password = validate($Password);
  $Password2 = filter_var($_POST["NewPassword2"], FILTER_SANITIZE_STRING);
  $Password2 = validate($Password2);
  $Hashed = password_hash($Password, PASSWORD_DEFAULT);
  $Rights = $_POST["NewRights"];
  //controleert of er een naam is ingevoerd. 
  if(!(empty($_POST["NewUsername"])))
  {
    $Username = $_POST["NewUsername"];
  }
  //als er geen naam is ingevoerd is de waarde van Username de oude gebruikersnaam
  else 
  {
    $Username = GetUserName($UserID); 
  }

  //controleren of de gebruikersnaam hetzelfde is ingevuld als de huidige gebruikersnaam. 
  try
  {
    $Result = NULL; 
    //selecteerd alle gebruikersnamen uit de tabel gebruiker die hetzelfde zijn als de ingevoerde waarde. 
    $sql = "SELECT UniekeLoginNaam FROM gebruiker WHERE UniekeLoginNaam = :Username"; 
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $Result = $row["UniekeLoginNaam"];
    }
    $PastUsername = GetUserName($UserID);
    //als er al een naam in de database staat die hetzelfde is als de ingevoerde waarde of als deze niet hetzelfde is als de vorige gebruikersnaam
    //krijgt de gebruiker een melding dat deze gebruikersnaam al bestaat. 
    if ($Result == $Username && $Result != $PastUsername)
      {
        echo "Deze gebruikersnaam is al in gebruik.";
      }
    if ((!($Result == $Username)) && ($Password == $Password2)) 
    {
     
      if (empty($Password))
      {
        $sql = "SELECT Wachtwoord FROM gebruiker WHERE GebruikerID = :UserID";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("UserID", $UserID, PDO::PARAM_STR);
        if($stmt->execute()) 
        {
          foreach ($stmt->fetchAll() as $row) 
          {
            $Password = $row["Wachtwoord"];
          }
        }
     }   

      $sql = "UPDATE `gebruiker` SET UniekeLoginNaam=:Username, Wachtwoord=:Password, Rechten=:Rights WHERE GebruikerID = $UserID;";

      $stmt = $conn->prepare($sql);
      $stmt->bindValue("Password", $Hashed, PDO::PARAM_STR);
      $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
      $stmt->bindValue("Rights", $Rights, PDO::PARAM_STR);
      if($stmt->execute()) 
      {
        echo "De gebruikersgegevens zijn succesvol aangepast!";
        header("Location: UserAdministration.php"); 
      }
    }
    else
    {
      if (strlen($Username) < 3 && $Result = !$PastUsername)
      {
        echo "De gebruikersnaam is te kort.";
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

function DeleteUserConfirmation($ID)
{
  echo "
      <div class=\"col-7\">
      <form action=\"UserAdministration.php\" method=\"post\">
        <input type=\"hidden\" name=\"id\" value=\"".$ID."\">
        <label>Weet u zeker dat u deze gebruiker wilt verwijderen?</label><br>
        <input type=\"submit\" class=\"btn btn-primary\" name=\"DeleteUserPerm\" value=\"Gebruiker verwijderen\">
        <input type=\"submit\" class=\"btn btn-primary\" name=\"KeepUser\" value=\"Gebruiker behouden\">
      </form>
    </div>
  "; 
}

function DeleteUser($UserID)
{
$sql = "DELETE FROM gebruiker WHERE GebruikerID=:UserID";
$conn = connectDB();
$stmt = $conn->prepare($sql);
$stmt->bindValue("UserID", $UserID, PDO::PARAM_STR);
  if($stmt->execute())
  {
    header("Location: UserAdministration.php"); 
  }
}
//UserAdministration.php ^^^^

//Bij het aanmaken van een licentie kan de doelgroep worden ingevuld
//Doelgroepen die eerder zijn ingevuld komen als suggesties in een dropdownmenu 
//Bij licenties aanpassen kan ook de doelgroep worden aangepast. 
//De licenties kunnen worden gesorteerd op doelgroep. 

?>