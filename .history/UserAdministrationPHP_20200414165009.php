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

function loadAdmins()
{
  $conn = ConnectDB(); 
  $sql = "SELECT * FROM gebruiker WHERE Rechten = 1 ORDER BY UniekeLoginNaam ASC";
  $stmt = $conn->prepare($sql);
  $stmt->execute();

  //hier worder de arrays aangemaakt, anders komen er foutmeldingen als de array niet bestaat. 
  $UniqueLoginNameAdmin = [];
  $RightsAdmin = [];
  $PasswordAdmin = [];
  $UserIDAdmin = [];

  // voor elke rij in de table gaat hij erlangs om de array te vullen
  foreach ($stmt->fetchAll() as $row) 
  {
      //zet de gegevens in een array.
  array_push($UniqueLoginNameAdmin, $row["UniekeLoginNaam"]);
  array_push($RightsAdmin, $row["Rechten"]);
  array_push($PasswordAdmin, $row["Wachtwoord"]);
  array_push($UserIDAdmin, $row["GebruikerID"]);
  }

  //hier worden de html code weergegeven om de table te maken. 
  //hij controleert de lengte en  maakt voor elke rij in de tabel een rij. 
  echo "
  <h2>Administrators</h2>
  <table style=\"margin: auto;\">
  ";
  for ($i = 0; $i < count($UniqueLoginNameAdmin); $i++)
  {
    //maakt hier de rijen aan met de gegevens er in. 
    echo "
      <form action=\"UserAdministration.php\" method=\"post\">
        <input type=\"hidden\" name=\"UserIDForm\" value=\"".$UserIDAdmin[$i]."\">
        <input type=\"hidden\" name=\"RightsForm\" value=\"1\">
        <input type=\"hidden\" name=\"UniqueLoginNameForm\" value=\"".$UniqueLoginNameAdmin[$i]."\">
        <br> 
        <tr id=\"formdesign\">
          <td>
            ".$UniqueLoginNameAdmin[$i]."
          </td>
          <td>
              <input type=\"submit\" id=\"buttondesigning\" class=\"btn btn-primary\" name=\"EditUser\"value=\"Aanpassen\">
          </td> 
          <td>
              <input type=\"submit\" id=\"buttondesigning\" class=\"btn btn-primary\" name=\"DeleteUser\"value=\"Verwijderen\">
          </td>
        </tr>
      </form>
    "; 
  } 
  echo "</table>";
}

function LoadReaders()
{
  $conn = ConnectDB();
  $sql = "SELECT * FROM gebruiker WHERE Rechten = 0 ORDER BY UniekeLoginNaam ASC";
  $stmt = $conn->prepare($sql);
  $stmt->execute();

  //hier worder de arrays aangemaakt, anders komen er foutmeldingen als de array niet bestaat. 
  $UniqueLoginNameReader = [];
  $RightsReader = [];
  $PasswordReader = [];
  $UserIDReader = [];

  foreach ($stmt->fetchAll() as $row) 
  {
    //zet de gegevens in een array.
    array_push($UniqueLoginNameReader, $row["UniekeLoginNaam"]);
    array_push($RightsReader, $row["Rechten"]);
    array_push($PasswordReader, $row["Wachtwoord"]);
    array_push($UserIDReader, $row["GebruikerID"]);
  }

  //hier worden de html code weergegeven om de table te maken. 
  //hij controleert de lengte en  maakt voor elke rij in de tabel een rij. 
  echo "
    <h2>Lezers</h2>
    <table style=\"margin: auto;\">
  ";
  for ($i = 0; $i < count($UniqueLoginNameReader); $i++)
  {
    //maakt hier de rijen aan met de gegevens er in. 
    echo "
      <form action=\"UserAdministration.php\" method=\"post\">
        <input type=\"hidden\" name=\"UserIDForm\" value=\"".$UserIDReader[$i]."\">
        <input type=\"hidden\" name=\"RightsForm\" value=\"0\">
        <input type=\"hidden\" name=\"UniqueLoginNameForm\" value=\"".$UniqueLoginNameReader[$i]."\">
        <tr id=\"formdesign\">
          <td>
            ".$UniqueLoginNameReader[$i]."
          </td>
          <td>
            <input type=\"submit\" class=\"btn btn-primary\" id=\"buttondesigning\" name=\"EditUser\"value=\"Aanpassen\">
          </td> 
          <td>
            <input type=\"submit\" class=\"btn btn-primary\" id=\"buttondesigning\" name=\"DeleteUser\"value=\"Verwijderen\">
          </td>
        </tr>
      </form>
    "; 
  } 
  echo "</table>";
}

if ((!empty($_POST["CreateAcc"])) && ($_POST["CreateAcc"] == "Aanmaken"))
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
    if (!(isset($Result)))
    {
      $Result = null;
    }
    if (($Result == $Username) || !($Password == $Password2) || ((strlen($Password)) < 5) || ((strlen($Password2)) < 5) || (strlen($Username) < 3))
    {
      $_SESSION["UserAdminEcho"] = null;
      if ($Result == $Username)
      {
        $_SESSION["UserAdminEcho"] = "Deze gebruikersnaam is al in gebruik door iemand anders.<br>";
      }
      if (!($Password == $Password2) || ((strlen($Password)) < 5) || ((strlen($Password2)) < 5))
      {
        $_SESSION["UserAdminEcho"] = $_SESSION["UserAdminEcho"] . "De wachtwoorden zijn niet gelijk of je hebt het wachtwoord te kort (moet minimaal 5 characters bevatten).<br>";
      }
      if (strlen($Username) < 3)
      {
        $_SESSION["UserAdminEcho"] = $_SESSION["UserAdminEcho"] . "De gebruikersnaam is te kort, hij moet minimaal 3 characters bevatten.<br>";
      }
    }
    else
    {
      $sql = "INSERT INTO `gebruiker` (`GebruikerID`, `UniekeLoginNaam`, `Wachtwoord`, `Rechten`) VALUES (NULL, :Username, :Password, :Rights);";

      $stmt = $conn->prepare($sql);
      $stmt->bindValue("Password", $Hashed, PDO::PARAM_STR);
      $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
      $stmt->bindValue("Rights", $Rights, PDO::PARAM_STR);
      if($stmt->execute()) 
      {
        $_SESSION["UserAdminEcho"] = "Het account is succesvol aangemaakt!";
      }
    }
    header("Location: UserAdministration.php");
    exit;
  }
  catch(exception $ex)
  {
    echo "Er was een fout met de connectie met het database.";
  }
}

function EditUserInformationForm($UserID, $Rights, $Username)
{
  echo "
  <h3>Gebruiker toevoegen</h3>
  <table style=\"margin: auto;\">
  ";
  echo "
    <form action=\"UserAdministrationPHP.php\" method=\"post\">
      <input type=\"hidden\" name=\"id\" value=\"".$UserID."\">
      <label>Nieuwe gebruikersnaam (minimaal 3 characters):</label><br>
      <input type=\"text\" name=\"NewUsername\" value=\"".$Username."\"><br>
      <label>Nieuwe wachtwoord(minimaal 5 characters):</label><br>
      <input type=\"password\" name=\"NewPassword\"><br>
      <label>Wachtwoord hertypen:</label><br>
      <input type=\"password\" name=\"NewPassword2\"><br>
  ";
  if ($Rights == 1)
  {
    echo "
      <input type=\"radio\" name=\"NewRights\" value=\"1\" checked>
      <label for=\"Administrator\">Administrator</label><br>
      <input type=\"radio\" name=\"NewRights\" value=\"0\">
      <label for=\"Lezer\">Lezer</label><br>
    ";
  }
  else
  {
    echo "
      <input type=\"radio\" name=\"NewRights\" value=\"1\">
      <label for=\"Administrator\">Administrator</label><br>
      <input type=\"radio\" name=\"NewRights\" value=\"0\" checked>
      <label for=\"Lezer\">Lezer</label><br>
    ";
  }
  echo "
      <input type=\"submit\" id=\"buttondesigning\" class=\"btn btn-primary\" name=\"EditUserConfirmation\" value=\"Gegevens aanpassen\">
    </form><br>
    <button type=\"submit\" id=\"buttondesigning\" onclick=\"window.location.href = 'UserAdministration.php';\" class=\"btn btn-primary\" name=\"Cancel\">Annuleren</button>
  ";
}

function CreateUserForm()
{
  echo "
    <form action=\"UserAdministrationPHP.php\" method=\"post\">
      <label>Gebruikersnaam (Minimaal 3 characters):</label><br>
      <input type=\"str\" name=\"Username\"><br>
      <label>Wachtwoord(Minimaal 5 characters):</label><br>
      <input type=\"password\" name=\"Password\"><br>
      <label>Wachtwoord hertypen:</label><br>
      <input type=\"password\" name=\"Password2\"><br>
      <input type=\"radio\" name=\"Rights\" value=\"1\">
      <label for=\"Administrator\">Administrator</label><br>
      <input type=\"radio\" name=\"Rights\" value=\"0\" checked>
      <label for=\"Lezer\">Lezer</label><br>
      <input type=\"submit\" id=\"buttondesigning\" class=\"btn btn-primary\" name=\"CreateAcc\" value=\"Aanmaken\">
    </form><br>
    <button type=\"submit\" id=\"buttondesigning\" onclick=\"window.location.href = 'MainMenu.php';\" class=\"btn btn-primary\" name=\"BackToMainMenu\">Terug</button>
  ";
}

function EditUserInformation($UserID)
{
  $conn = connectDB();
  $Username = filter_var($_POST["NewUsername"], FILTER_SANITIZE_STRING);
  $Username = validate($Username);
  $Password = filter_var($_POST["NewPassword"], FILTER_SANITIZE_STRING);
  $Password = validate($Password);
  $Password2 = filter_var($_POST["NewPassword2"], FILTER_SANITIZE_STRING);
  $Password2 = validate($Password2);
  $Hashed = password_hash($Password, PASSWORD_DEFAULT);
  $Rights = $_POST["NewRights"];
  try
  {
    //selecteerd alle gebruikersnamen uit de tabel gebruiker die hetzelfde zijn als de ingevoerde waarde. 
    $sql = "SELECT UniekeLoginNaam FROM gebruiker WHERE UniekeLoginNaam = :Username"; 
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row)
    {
      $Result = $row["UniekeLoginNaam"];
    }
    if (!(isset($Result)))
    {
      $Result = null;
    }
    $PastUsername = GetUserName($UserID);
    if (($Result == $Username && $Result != $PastUsername) || !($Password == $Password2) || ((strlen($Password)) < 5) || ((strlen($Password2)) < 5) || (strlen($Username) < 3))
    {
      $_SESSION["UserAdminEcho"] = null;
      if ($Result == $Username && $Result != $PastUsername)
      {
        $_SESSION["UserAdminEcho"] = "Deze gebruikersnaam is al in gebruik door iemand anders.<br>";
      }
      if (!($Password == $Password2) || ((strlen($Password)) < 5) || ((strlen($Password2)) < 5))
      {
        $_SESSION["UserAdminEcho"] = $_SESSION["UserAdminEcho"] . "De wachtwoorden zijn niet gelijk of je hebt het wachtwoord te kort (moet minimaal 5 characters bevatten).<br>";
      }
      if (strlen($Username) < 3)
      {
        $_SESSION["UserAdminEcho"] = $_SESSION["UserAdminEcho"] . "Deze gebruikersnaam is te kort, hij moet minimaal 3 characters bevatten.<br>";
      }
    }
    else
    {
      $sql = "UPDATE `gebruiker` SET UniekeLoginNaam=:Username, Wachtwoord=:Password, Rechten=:Rights WHERE GebruikerID = $UserID;";

      $stmt = $conn->prepare($sql);
      $stmt->bindValue("Password", $Hashed, PDO::PARAM_STR);
      $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
      $stmt->bindValue("Rights", $Rights, PDO::PARAM_STR);
      if($stmt->execute()) 
      {
        echo "De gebruikersgegevens zijn succesvol aangepast!"; 
      }
    }
    header("Location: UserAdministration.php");
    exit;
  }
  catch(exception $ex)
  {
    echo "Er was een fout met de connectie met het database.";
  }
}

function DeleteUserConfirmation($UserID)
{
  echo "
      <div class=\"col-7\">
      <form action=\"UserAdministrationPHP.php\" method=\"post\">
        <input type=\"hidden\" name=\"UserID\" value=\"".$UserID."\">
        <label>Weet u zeker dat u deze gebruiker wilt verwijderen?</label><br>
        <input type=\"submit\" class=\"btn btn-primary\" name=\"DeleteUserPerm\" value=\"Gebruiker verwijderen\">
      </form><br>
      <button type=\"submit\" onclick=\"window.location.href = 'UserAdministration.php';\" class=\"btn btn-primary\" name=\"KeepUser\">Gebruiker behouden</button>
    </div>
  "; 
}

if(isset($_POST["EditUserConfirmation"]))
{
  EditUserInformation($_POST["id"]); 
}
if(isset($_POST["DeleteUserPerm"]))
{
  try
  {
    $sql = "DELETE FROM gebruiker WHERE GebruikerID=:UserID";
    $conn = connectDB();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("UserID", $_POST["UserID"], PDO::PARAM_STR);
    if($stmt->execute())
    {
      $_SESSION["UserAdminEcho"] = "De gebruiker is succesvol verwijderd.";
      header("Location: UserAdministration.php"); 
    }
  }
  catch(exception $ex)
  {
    echo "Er was een fout met de connectie met het database.";
  }
} 
?>