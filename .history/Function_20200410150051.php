<?php 
require("Connect.php");

if (session_status() == PHP_SESSION_NONE) 
{
  session_start();
}
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

function GetLicense()
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
  if (isset($_SESSION["LicenseNameShow"]))
  {
    echo "
    <div class=\"col-7\">
      <h2><b>Licentie naam</b></h2>
      <p><td>".$_SESSION["LicenseNameShow"]."</td></p>
      <h2><b>Doelgroep</b></h2>
      <p><td>".$_SESSION["AudienceShow"]."</td></p>
      <br>
      <h2><b>Beschrijving</b></h2>
      <p><td>".$_SESSION["DescriptionShow"]."</td></p>
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
      <br>
      <form action=\"Function.php\" method=\"post\">
        <label>Opmerking:</label><br>
        <textarea name = \"Comment\" rows = \"3\" cols = \"80\"></textarea><br>
        <input type=\"submit\" class=\"btn btn-primary\" name=\"AddComment\" value=\"Opmerking plaatsen\">
      </form><br>
      <form action=\"MainMenu.php\" method=\"post\">
        <input type=\"submit\" class=\"btn btn-success\" name=\"Edit-submit\" style= \"margin-bottom: 10px;\" value=\"Licentie bewerken\">
        <input type=\"button\"class=\"btn btn-danger\" name=\"Delete-submit\" onclick=\"document.getElementById('id01').style.display='block'\"  style= \"margin-bottom: 10px;\" value=\"Licentie Verwijderen\" >
      </form>
    </div>
    ";

    $_SESSION["tempLicenseName"] = $_SESSION["LicenseNameShow"];  
  }
  else
  {
    exit;
  }
}

if (isset($_POST["AddComment"]))
{
  if ($_POST["Comment"] != null)
  {
    AddComment($_POST["Comment"], $_SESSION["UserID"], $_SESSION["LicenseID"]);
  }
  else
  {
    header("Location: MainMenu.php");
    exit;
  }
}

function AddComment($Comment, $UserID, $LicenseID)
{

  $sql = "INSERT INTO `opmerking` (`LicentieID`, `OpmerkingID`, `GebruikerID`, `Opmerking`) VALUES (:LicenseID, NULL, :UserID, :Comment);";
  $conn = connectDB();
  $stmt = $conn->prepare($sql);
  $stmt->bindValue("LicenseID", $LicenseID, PDO::PARAM_STR);
  $stmt->bindValue("UserID", $UserID, PDO::PARAM_STR);
  $stmt->bindValue("Comment", $Comment, PDO::PARAM_STR);
   
  if($stmt->execute())
  {
    header("Location: MainMenu.php");
  }
}

Function LoadComments()
{
  if (isset($_SESSION["LicenseNameShow"]))
  {
    $LicenseID = $_SESSION["LicenseID"];
    $conn = connectDB();
    $sql = "SELECT Opmerking, GebruikerID, GeplaatstOp FROM opmerking WHERE LicentieID = :LicenseID ORDER BY OpmerkingID ASC;";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("LicenseID", $LicenseID, PDO::PARAM_STR);
    if ($stmt->execute())
    {
      echo "<h2><b>Comments</b></h2>";
      foreach ($stmt->fetchAll() as $row)
      { 
        echo "
          \"".$row["Opmerking"]."\"<br>
          -".GetUserName($row["GebruikerID"])." ".$row["GeplaatstOp"]."<br>
        ";
      }
    }
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

Function GetUserID($Username)
{
  try 
  {
    $conn = connectDB();
    $sql = "SELECT GebruikerID FROM gebruiker WHERE UniekeLoginNaam = :Username;";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("Username", $Username, PDO::PARAM_STR);
    if ($stmt->execute())
    {
      foreach ($stmt->fetchAll() as $row)
      { 
        return $row["GebruikerID"];
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
        <label>Doelgroep:</label><br>
        <input type=\"text\" name=\"Audience\"><br>
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
  $sql = "SELECT LicentieID, LicentieNaam, Beschrijving, InstallatieOmschrijving, VerloopDatum ,LaatstAangepast, Doelgroep FROM licentie WHERE LicentieID = :LicenseID"; 
 $conn = connectDB();
 $stmt = $conn->prepare($sql);
 $stmt->bindParam("LicenseID", $_SESSION["LicenseID"], PDO::PARAM_STR);
 
 $stmt->execute();
 $result = $stmt->fetch(PDO::FETCH_ASSOC);
 $_SESSION["tempID"] = $result["LicentieID"];
  echo "
  <div class=\"col-4\">
  <form method=\"post\">
    <label>Licentie naam:</label><br>
    <textarea name = \"Description\" rows = \"3\" cols = \"80\">".$result["LicentieNaam"]."</textarea><br>
    <label>Doelgroep:</label><br>
    <textarea name = \"Audience\" rows = \"3\" cols = \"80\">".$result["Doelgroep"]."</textarea><br>
    <label>Omschrijving van de licentie:</label><br>
    <textarea name = \"Description\" rows = \"3\" cols = \"80\">".$result["Beschrijving"]."</textarea><br>
    <label>Omschrijving van de installatie:</label><br>
    <textarea name = \"InstallDesc\" rows = \"3\" cols = \"80\">".$result["InstallatieOmschrijving"]."</textarea><br>
    <label>Licentie verloop datum:</label><br>
    <input type=\"date\" name=\"ExpirationDate\" value=" .$result["VerloopDatum"]. "><br><br>
    <input type=\"submit\" class=\"btn btn-success\" name=\"EditLicense\" value=\"Licentie bewerken\">
    <br>
  </form>
</div>
  ";
}

function DeleteLicense()
{
  $sql = "DELETE FROM licentie WHERE LicentieID=:LicenseID";
  $conn = connectDB();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":LicenseID",  $_SESSION["LicenseID"], PDO::PARAM_STR);
  if($stmt->execute())
  {
   
  ?>  <script type="text/javascript">
  window.location.href = 'MainMenu.php';
  </script>
  <?php
  }
  unset($_SESSION["LicenseNameShow"]);
  unset($_SESSION["DescriptionShow"]);
  unset($_SESSION["InstallDescShow"]);
  unset($_SESSION["LastChangedShow"]);
  unset($_SESSION["ExpirationDateShow"]);
  unset($_SESSION["UserIDShow"]);
  unset($_SESSION["LicenseIDShow"]);
  unset($_SESSION["AudienceShow"]);
}

function EditLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate)
{
  date_default_timezone_set('Europe/Amsterdam');
  $CurrentDate = date('Y/m/d');
  $Audience = $_POST["Audience"];

  $sql ="UPDATE licentie SET LicentieNaam=:LicenseName, Beschrijving=:Description, InstallatieOmschrijving=:InstallDesc, VerloopDatum=:ExpirationDate, LaatstAangepast=:CurrentDate, Doelgroep=:Audience WHERE LicentieID=:LicenseID";
  $conn = connectDB();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":LicenseID", $_SESSION["LicenseID"], PDO::PARAM_STR);
  $stmt->bindParam(":LicenseName", $LicenseName, PDO::PARAM_STR);
  $stmt->bindParam(":Description", $Description, PDO::PARAM_STR);
  $stmt->bindParam(":InstallDesc", $InstallDesc, PDO::PARAM_STR);
  $stmt->bindParam(":ExpirationDate", $ExpirationDate);
  $stmt->bindParam(":CurrentDate", $CurrentDate);
  $stmt->bindParam(":Audience", $Audience);
  $res = $stmt->fetch(PDO::FETCH_ASSOC);
  if($stmt->execute()){
    unset( $_SESSION["tempLicenseName"]);
    header("Location: MainMenu.php");    
  }
}


function AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate, $UserID)
{
  date_default_timezone_set('Europe/Amsterdam');
  $CurrentDate = date('Y/m/d');
  $Audience = $_POST["Audience"]; 

  $sql = "INSERT INTO `licentie` (`LicentieID`, `LicentieNaam`, `Beschrijving`, `InstallatieOmschrijving`, `VerloopDatum`, `GebruikerID`, `LaatstAangepast`, `Doelgroep` ) VALUES (NULL, :LicenseName, :Description, :InstallDesc, :ExpirationDate, :UserID, :CurrentDate, :Audience);";
  $conn = connectDB();
  $stmt = $conn->prepare($sql);
  $stmt->bindValue("LicenseName", $LicenseName, PDO::PARAM_STR);
  $stmt->bindValue("Description", $Description, PDO::PARAM_STR);
  $stmt->bindValue("InstallDesc", $InstallDesc, PDO::PARAM_STR);
  $stmt->bindValue("ExpirationDate", $ExpirationDate, PDO::PARAM_STR);
  $stmt->bindValue("CurrentDate", $CurrentDate, PDO::PARAM_STR);
  $stmt->bindValue("UserID", $UserID, PDO::PARAM_STR);
  $stmt->bindValue("Audience", $Audience, PDO::PARAM_STR);
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
      $DateDay = substr("$temp", 0, 2);
      $DateMonth = substr("$temp", 3, 2);
      $DateYear = substr("$temp", 6, 4);
      $ExpirationDate = $DateYear . "-" . $DateMonth . "-" . $DateDay;
      AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate, $_SESSION["UserID"]);
    }
    else
    {
      $ExpirationDate = null;
      AddLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate, $_SESSION["UserID"]);
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
    try 
    {
      $conn = connectDB();
      $sql = "SELECT LicentieNaam, Beschrijving, InstallatieOmschrijving, VerloopDatum, GebruikerID, LaatstAangepast , LicentieID, Doelgroep FROM licentie WHERE LicentieID = :LicenseID;";
      $stmt = $conn->prepare($sql);
      $stmt->bindValue("LicenseID", $_POST["LicenseID"], PDO::PARAM_STR);
      if ($stmt->execute())
      {
        foreach ($stmt->fetchAll() as $row)
        { 
          $_SESSION["LicenseID"] = $row["LicentieID"];
          $_SESSION["LicenseNameShow"] = $row["LicentieNaam"];
          $_SESSION["DescriptionShow"] = $row["Beschrijving"];
          $_SESSION["InstallDescShow"] = $row["InstallatieOmschrijving"];
          $_SESSION["LastChangedShow"] = $row["LaatstAangepast"];
          $_SESSION["UserIDShow"] = $row["GebruikerID"];
          $_SESSION["ExpirationDateShow"] = $row["VerloopDatum"];
          $_SESSION["AudienceShow"] = $row["Doelgroep"];
        }
      }
      header("Location: MainMenu.php");
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

function EditUserInformationForm($ID)
{
  echo "
    <div class=\"col-7\">
      <form action=\"UserAdministration.php\" method=\"post\">
        <input type=\"hidden\" name=\"id\" value=\"".$ID."\">
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