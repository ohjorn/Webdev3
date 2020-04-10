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
    $sql = "SELECT Wachtwoord, GebruikerID FROM gebruiker WHERE `UniekeLoginNaam` = :Username";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $result = $row["Wachtwoord"];
      if (session_status() == PHP_SESSION_NONE) 
      {
        session_start();
      }
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
            <input type=\"submit\"name=\"LicenseNameLoad\"value=\"".$row["LicentieNaam"]."\">
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
  if (isset($_SESSION["LicenseNameShow"]))
  {
    echo "
    <div class=\"col-4\">
      <h2><b>Licentie naam</b></h2>
      <p><td>".$_SESSION["LicenseNameShow"]."</td></p>
      <h2><b>Beschrijving</b></h2>
      <p><td>".$_SESSION["DescriptionShow"]."</td></p>
      <br>
      <h2><b>Opmerking</b></h2>
      <p><td>".$_SESSION["CommentShow"]."</td></p>
      <br>
      <h2><b>Installatie omschrijving</b></h2>
      <p><td>".$_SESSION["InstallDescShow"]."</td></p>
      <br> 
      <h2><b>Verloop datum</b></h2>
      <p><td>".$_SESSION["ExpirationDateShow"]."</td></p>
      <br> 
      <h2><b>Laatst aangepast</b></h2>
      <p><td>".$_SESSION["LastChangedShow"].", Door: ".GetUserName($_SESSION["UserIDShow"])."</td></p>
      <br> 
    </div>
    <div class='col-2 text-center' id='submitbtns'>    
    <form action='' method='post'>
      <input type='submit' class='btn btn-success' name='Edit-submit' style= 'margin-bottom: 10px;' value='Licentie bewerken'>
      <input type=\"submit\" class=\"btn btn-danger\" name=\"Delete-submit\"  style= 'margin-bottom: 10px;' value=\"Licentie Verwijderen\">
    </form>             
    </div>
    ";

    $_SESSION["tempLicenseName"] = $_SESSION["LicenseNameShow"];  
    unset($_SESSION["LicenseNameShow"]);
    unset($_SESSION["DescriptionShow"]);
    unset($_SESSION["CommentShow"]);
    unset($_SESSION["InstallDescShow"]);
    unset($_SESSION["LastChangedShow"]);
    unset($_SESSION["ExpirationDateShow"]);
    unset($_SESSION["UserIDShow"]);
    exit;
  }
  else
  {
    exit;
  }
}

Function GetUserName($UserID)
{
  try 
  {
    $conn = connectDB();
    $sql = "SELECT UniekeLoginNaam FROM gebruiker WHERE GebruikerID = :UserID;";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("UserID", $UserID, PDO::PARAM_STR);
    if ($stmt->execute())
    {
      foreach ($stmt->fetchAll() as $row)
      { 
        return $row["UniekeLoginNaam"];
        exit;
      }
    }
  }
  catch (PDOException $ex) 
  {
    echo "$ex";
  }
}

function AddLicenseForm()
{
  echo "
    <div class=\"col-7\">
      <form action=\"Function.php\" method=\"post\">
        <label>Licentie naam:</label><br>
        <input type=\"text\" name=\"LicenseName\"><br>
        <label>Omschrijving van de licentie:</label><br>
        <textarea name = \"Description\" rows = \"3\" cols = \"80\"></textarea><br>
        <label>Omschrijving van de installatie:</label><br>
        <textarea name = \"InstallDesc\" rows = \"3\" cols = \"80\"></textarea><br>
        <label>Licentie verloop datum:</label><br>
        <input type=\"date\" name=\"ExpirationDate\"><br><br>
        <input type=\"submit\" class=\"btn btn-primary\" name=\"AddLicense\" value=\"Licentie toevoegen\">
      </form>
    </div>
  ";
}

function EditLicenseForm()
{
  $sql = "SELECT LicentieID, LicentieNaam, Beschrijving, Opmerking, InstallatieOmschrijving, LaatstAangepast FROM licentie WHERE LicentieNaam = :LicenseName"; 
 $conn = connectDB();
 $stmt = $conn->prepare($sql);
 $stmt->bindParam("LicenseName", $_SESSION["tempLicenseName"], PDO::PARAM_STR);
 
 $stmt->execute();
 $result = $stmt->fetch(PDO::FETCH_ASSOC);
 if (session_status() == PHP_SESSION_NONE) 
    {
      session_start();
    }
 $_SESSION["tempID"] = $result["LicentieID"];
  echo "
  <div class=\"col-4\">
  bewerken
  <form method=\"post\">
    <label>Licentie naam:</label><br>
    <input type=\"text\" name=\"LicenseName\" value\"".$result["LicentieNaam"]."\"><br>
    <label>Omschrijving van de licentie:</label><br>
    <textarea name = \"Description\" rows = \"3\" cols = \"80\">".$result["Beschrijving"]."</textarea><br>
    <label>Omschrijving van de installatie:</label><br>
    <textarea name = \"InstallDesc\" rows = \"3\" cols = \"80\">".$result["InstallatieOmschrijving"]."</textarea><br>
    <label>Licentie verloop datum:</label><br>
    <input type=\"date\" name=\"ExpirationDate\"><br><br>
    <input type=\"submit\" class=\"btn btn-success\" name=\"EditLicense\" value=\"Licentie bewerken\">
    <br>
  </form>
</div>
  ";
}

function DeleteLicense()
{
$sql = "DELETE FROM licentie WHERE LicentieNaam=:LicenseName";
$conn = connectDB();
$stmt = $conn->prepare($sql);
$stmt->bindParam(":LicenseName",  $_SESSION["tempLicenseName"], PDO::PARAM_STR);
if($stmt->execute())
{
  unset($_SESSION["tempLicenseName"]);
?>  <script type="text/javascript">
window.location.href = 'MainMenu.php';
</script>
<?php
}
 
}

function EditLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate)
{
  date_default_timezone_set('Europe/Amsterdam');
  $CurrentDate = date('Y/m/d');
  if (session_status() == PHP_SESSION_NONE) 
    {
      session_start();
    }

  $sql ="UPDATE licentie SET LicentieNaam=:LicenseName, Beschrijving=:Description, InstallatieOmschrijving=:InstallDesc,LaatstAangepast=:CurrentDate WHERE LicentieID=:LicenseID";
  $conn = connectDB();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":LicenseID", $_SESSION["tempID"], PDO::PARAM_STR);
  $stmt->bindParam(":LicenseName", $LicenseName, PDO::PARAM_STR);
  $stmt->bindParam(":Description", $Description, PDO::PARAM_STR);
  $stmt->bindParam(":InstallDesc", $InstallDesc, PDO::PARAM_STR);
  $stmt->bindParam(":CurrentDate", $CurrentDate);
  $res = $stmt->fetch(PDO::FETCH_ASSOC);
  if($stmt->execute()){
    unset($_SESSION["tempID"]);
    unset( $_SESSION["tempLicenseName"]);
    header("Location: MainMenu.php");    
  }
}


function AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate, $UserID)
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
  $stmt->bindValue("CurrentDate", $CurrentDate, PDO::PARAM_STR);
  $stmt->bindValue("UserID", $UserID, PDO::PARAM_STR);
  if($stmt->execute())
  {
    header("Location: MainMenu.php");
  }
}

if(isset($_POST["DeleteLicense"])){
  DeleteLicense();
}

if (isset($_POST["AddLicense"]))
{
  if (!(empty($_POST["LicenseName"])))
  {
    if (session_status() == PHP_SESSION_NONE) 
    {
      session_start();
    }
    $LicenseName = $_POST["LicenseName"];
    $UserID = $_SESSION["UserID"];
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
      $DateDay = substr("$temp", 0, 2);
      $DateMonth = substr("$temp", 3, 2);
      $DateYear = substr("$temp", 6, 4);
      $ExpirationDate = $DateYear . "-" . $DateMonth . "-" . $DateDay;
      AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate, $UserID);
    }
    else
    {
      $ExpirationDate = null;
      AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate, $UserID);
    }
  }
  else
  {
    echo "Fout";
  }
}

if (isset($_POST["EditLicense"]))
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
          $ExpirationDate = $temp;
          EditLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate);
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
      EditLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate);
    }
  }
  else 
  {
    echo "fout";
  }
}

if (isset($_POST["LicenseNameLoad"]))
{
  if ($_POST["LicenseNameLoad"] && $_POST["LicenseID"])
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
          $_SESSION["LicenseNameShow"] = $row["LicentieNaam"];
          $_SESSION["DescriptionShow"] = $row["Beschrijving"];
          $_SESSION["CommentShow"] = $row["Opmerking"];
          $_SESSION["InstallDescShow"] = $row["InstallatieOmschrijving"];
          $_SESSION["LastChangedShow"] = $row["LaatstAangepast"];
          if ($row["GebruikerID"] != null)
          {
            $_SESSION["UserIDShow"] = $row["GebruikerID"];
          }
          else
          {
            $_SESSION["UserIDShow"] = "-";
          }
          $_SESSION["ExpirationDateShow"] = $row["VerloopDatum"];
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

function EditUserInformationForm()
{
  echo "
    <div class=\"col-7\">
      <form action=\"UserAdministration.php\" method=\"post\">
        <label>Gebruikersnaam:</label><br>
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
    </div>
  ";
}

function EditUserInformation($NewUsername, $NewPassword, $NewPassword2, $NewRights, $UserID)
{
  $conn = connectDB();

  $Password = filter_var($_POST["NewPassword"], FILTER_SANITIZE_STRING);
  $Password = validate($Password);
  $Password2 = filter_var($_POST["NewPassword2"], FILTER_SANITIZE_STRING);
  $Password2 = validate($Password2);
  $Hashed = password_hash($Password, PASSWORD_DEFAULT);
  $Rights = $_POST["NewRights"];
  $Username = $_POST["NewUsername"];

  //aangepaste gegevens updaten in de tabel
  //Als de gegevens leeg zijn worden de oude gegevens ingevuld
  //de juiste rij aanpassen door middel van het gebruikersID
  try
  {
    $Result = NULL; 
    $Checked = $_POST["id"];
    $sql = "SELECT * FROM gebruiker WHERE UniekeLoginNaam = :Username";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $Result = $row["UniekeLoginNaam"];
    }
    if ((!($Result == $Username)) && ($Password == $Password2)) 
    {
      if (empty($Username))
      {
        $PastUsername = "SELECT UniekeLoginNaam FROM gebruiker WHERE GebruikerID = $Checked";
        $Username = $PastUsername; 
      }
      if (empty($Password))
      {
        $PastPassword = "SELECT Wachtwoord FROM gebruiker WHERE GebruikerID = $Checked";
        $Password = $PastPassword; 
      }
      if (empty($Password2))
      {
        $PastPassword2 = "SELECT Wachtwoord FROM gebruiker WHERE GebruikerID = $Checked";
        $Password2 = $PastPassword2; 
      }
      if (empty($Rights))
      {
        $PastRights = "SELECT Rechten FROM gebruiker WHERE GebruikerID = $Checked";
        $Rights = $PastRights; 
      }
      $sql = "UPDATE `gebruiker` SET UniekeLoginNaam=:Username, Wachtwoord=:Password, Rechten=:Rights WHERE GebruikerID = $Checked;";

      $stmt = $conn->prepare($sql);
      $stmt->bindValue("Password", $Hashed, PDO::PARAM_STR);
      $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
      $stmt->bindValue("Rights", $Rights, PDO::PARAM_STR);
      if($stmt->execute()) 
      {
        echo "De gebruikersgegevens zijn succesvol aangepast!";
        EditUserInformation($NewUsername, $NewPassword, $NewPassword2, $NewRights, $UserID);
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


//UserAdministration.php ^^^^
?>