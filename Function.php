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

function GetLicenties()
{
  $conn = connectDB();
  try
  {
    $sql = "SELECT LicentieID, LicentieNaam FROM licentie ORDER BY LicentieNaam asc";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      echo "
        <li>
          <form action=\"Function.php\" method=\"post\">
            <input type=\"hidden\" name=\"LicenseID\" value=\"".$row["LicentieID"]."\">
            <input type=\"submit\"name=\"LicenseName\"value=\"".$row["LicentieNaam"]."\">
          </form>
        </li>
      ";
    }
  }
  catch (PDOException $ex) 
  {
    echo "$ex";
  } 
}

function LoadLicense()
{
  if (session_status() == PHP_SESSION_NONE) 
  {
    session_start();
  }
  if (isset($_SESSION["LicenseName"]))
  {
    echo "
    <div class=\"col-4\">
      <h2><b>Beschrijving</b></h2>
      <p><td>".$_SESSION["Description"]."</td></p>
      <br>
      <h2><b>Opmerking</b></h2>
      <p><td>".$_SESSION["Comment"]."</td></p>
      <br>
      <h2><b>Installatie omschrijving</b></h2>
      <p><td>".$_SESSION["InstallDesc"]."</td></p>
      <br> 
      <h2><b>Verloop datum</b></h2>
      <p><td>".$_SESSION["ExpirationDate"]."</td></p>
      <br> 
      <h2><b>Laatst aangepast</b></h2>
      <p><td>".$_SESSION["LastChanged"]."</td></p>
      <br> 
    </div>
    ";
    unset($_SESSION["LicenseName"]);
    unset($_SESSION["Description"]);
    unset($_SESSION["Comment"]);
    unset($_SESSION["InstallDesc"]);
    unset($_SESSION["LastChanged"]);
    unset($_SESSION["ExpirationDate"]);
    exit;
  }
  else
  {
    exit;
  }
}

function AddLicenseForm()
{
  echo "
    <div class=\"col-4\">
      <form action=\"Function.php\" method=\"post\">
        <input type=\"text\" name=\"LicenseName\" placeholder=\"Licentie naam\">
        <br><input type=\"text\" name=\"Description\" placeholder=\"Omschrijving van de licentie\">
        <br><input type=\"text\" name=\"InstallDesc\" placeholder=\"Omschrijving van de installatie\">
        <br>Dag dat de licentie verloopt: <input type=\"text\" name=\"ExpirationDate\" placeholder=\"dd/mm/yyyy\">
        <br><input type=\"submit\" class=\"btn btn-primary\" name=\"AddLicense\" value=\"Licentie toevoegen\">
      </form>
    </div>
  ";
}

function AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate)
{
  date_default_timezone_set('Europe/Amsterdam');
  $CurrentDate = date('Y/m/d');

  $sql = "INSERT INTO `licentie` (`LicentieID`, `LicentieNaam`, `Beschrijving`, `Opmerking`, `InstallatieOmschrijving`, `VerloopDatum`, `GebruikerID`, `LaatstAangepast`) VALUES (NULL, :LicenseName, :Description, NULL, :InstallDesc, :ExpirationDate, :UserID, :CurrentDate);";
  $conn = connectDB();
  $stmt = $conn->prepare($sql);
  $stmt->bindValue("LicenseName", $LicenseName, PDO::PARAM_STR);
  $stmt->bindValue("Description", $Description, PDO::PARAM_STR);
  $stmt->bindValue("InstallDesc", $InstallDesc, PDO::PARAM_STR);
  $stmt->bindValue("ExpirationDate", $ExpirationDate, PDO::PARAM_STR);
  $stmt->bindValue("UserID", 123, PDO::PARAM_STR);
  $stmt->bindValue("CurrentDate", $CurrentDate, PDO::PARAM_STR);
  if($stmt->execute())
  {
    header("Location: MainMenu.php");
  }
}

if (isset($_POST["AddLicense"]))
{
  if (!(empty($_POST["LicenseName"])))
  {
    $LicenseName = $_POST["LicenseName"];

    if (!(empty($_POST["Description"])))
    {
      $Description = $_POST["Description"];
    }
    else 
    {
      $Description = null;
    }

    if (!(empty($_POST["InstallDesc"])))
    {
      $InstallDesc = $_POST["InstallDesc"];
    }
    else
    {
      $InstallDesc = null;
    }

    if (!(empty($_POST["ExpirationDate"])))
    {
      $temp = $_POST["ExpirationDate"];
      if (($temp[2] == "/") && ($temp[5] == "/") && (strlen($temp) == 10))
      {
        $DateDay = substr("$temp", 0, 2);
        $DateMonth = substr("$temp", 3, 2);
        $DateYear = substr("$temp", 6, 4);
        if (checkdate($DateMonth, $DateDay, $DateYear))
        {
          $ExpirationDate = $DateYear . "-" . $DateMonth . "-" . $DateDay;
          AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate);
        }
        else 
        {
          echo "fout";
        }
      }
      else
      {
        echo "fout";
      }
    }
    else
    {
      $ExpirationDate = null;
      AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate);
    }
  }
  else 
  {
    echo "fout";
  }
}

if (isset($_POST["LicenseName"]))
{
  if ($_POST["LicenseName"] && $_POST["LicenseID"])
  {
    $LicenseID = $_POST["LicenseID"];
    try 
    {
      $conn = connectDB();
      $sql = "SELECT LicentieNaam, Beschrijving, Opmerking, InstallatieOmschrijving, VerloopDatum, GebruikerID, LaatstAangepast FROM licentie WHERE LicentieID = :LicenseID;";
      $stmt = $conn->prepare($sql);
      $stmt->bindValue("LicenseID", $LicenseID, PDO::PARAM_STR);
      if ($stmt->execute())
      {
        if (session_status() == PHP_SESSION_NONE) 
        {
          session_start();
        }
        foreach ($stmt->fetchAll() as $row)
        { 
          $_SESSION["LicenseName"] = $row["LicentieNaam"];
          $_SESSION["Description"] = $row["Beschrijving"];
          $_SESSION["Comment"] = $row["Opmerking"];
          $_SESSION["InstallDesc"] = $row["InstallatieOmschrijving"];
          $_SESSION["LastChanged"] = $row["LaatstAangepast"];
          if ($row["GebruikerID"] != null)
          {
            $_SESSION["UserID"] = $row["GebruikerID"];
          }
          else
          {
            $_SESSION["UserID"] = "-";
          }
          $_SESSION["ExpirationDate"] = $row["VerloopDatum"];
          header("Location: MainMenu.php");
        }
      }
    }
    catch (PDOException $ex) 
    {
      echo "$ex";
    }
  }
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