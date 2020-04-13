<?php 
if (!(class_exists('connectDB'))) 
{
  include_once("Connect.php");
}

if (session_status() == PHP_SESSION_NONE) 
{
  session_start();
}

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
  unset($_SESSION["LicenseNameShow"]);
  unset($_SESSION["DescriptionShow"]);
  unset($_SESSION["InstallDescShow"]);
  unset($_SESSION["LastChangedShow"]);
  unset($_SESSION["ExpirationDateShow"]);
  unset($_SESSION["UserIDShow"]);
  unset($_SESSION["LicenseIDShow"]);
  unset($_SESSION["AudienceShow"]);
  header("Location: UserAdministration.php");
  exit;
}

function GetLicense()
{
  if (isset($_GET["Sort"])){
    $Sort = $_GET["Sort"]; 
    $_SESSION['sort'] = $_GET["Sort"]; 
  }
  else {
    $Sort = 0; 
  }
  if (!(isset($_SESSION["sort"]))){
    $_SESSION['sort'] = 1; 
  }
  $counter = 0;
  $conn = connectDB();
  try
  {
    if ($Sort == 2 || $_SESSION['sort'] == '2'){
    $sql = "SELECT LicentieID, LicentieNaam, Doelgroep FROM licentie ORDER BY Doelgroep asc";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $counter += 1;
      echo "
        <li>
          <form action=\"MainMenuPHP.php\" method=\"post\">
            <input type=\"hidden\" name=\"LicenseID\" value=\"".$row["LicentieID"]."\">
            <input type=\"submit\"name=\"LicenseNameLoad\"value=\"".$row["LicentieNaam"]."\">
          </form>
        </li>
      ";
    }
    }

    if ($Sort == 1 || $_SESSION['sort'] == '1'){
    $sql = "SELECT LicentieID, LicentieNaam FROM licentie ORDER BY LicentieNaam asc";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) 
    {
      $counter += 1;
      echo "
        <li>
          <form action=\"MainMenuPHP.php\" method=\"post\">
            <input type=\"hidden\" name=\"LicenseID\" value=\"".$row["LicentieID"]."\">
            <input type=\"submit\"name=\"LicenseNameLoad\"value=\"".$row["LicentieNaam"]."\">
          </form>
        </li>
      ";
    }
  }
}

  catch (PDOException $ex) 
  {
    echo "$ex";
  } 

  if (session_status() == PHP_SESSION_NONE) 
  {
  session_start();
  }
  $_SESSION["counter"] = $counter; 
}

function LoadLicense()
{
  if (isset($_SESSION["LicenseNameShow"]))
  {
    $Licenseview = '';
    $Licenseview .= "
    <div class=\"col-7\">
      <h2><b>Licentie naam</b></h2>
      <p><td>".$_SESSION["LicenseNameShow"]."</td></p>
      <h2><b>Doelgroep</b></h2>
      <p><td>".$_SESSION["AudienceShow"]."</td></p>
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
      <form action=\"MainMenuPHP.php\" method=\"post\">
        <label>Opmerking:</label><br>
        <textarea name = \"Comment\" rows = \"3\" cols = \"80\"></textarea><br>
        <input type=\"submit\" class=\"btn btn-primary\" name=\"AddComment\" value=\"Opmerking plaatsen\">
      </form><br>
    </div>
    ";
    if(IsAdmin())
    {
    $Licenseview .= "
    <form action=\"MainMenu.php\" method=\"post\">
    <input type=\"submit\" class=\"btn btn-success\" name=\"Edit-submit\" style= \"margin-bottom: 10px;\" value=\"Licentie bewerken\">
    <input type=\"button\"class=\"btn btn-danger\" name=\"Delete-submit\" onclick=\"document.getElementById('id01').style.display='block'\"  style= \"margin-bottom: 10px;\" value=\"Licentie Verwijderen\" >
    </form>
    ";
    }

    echo $Licenseview;

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
      echo "<h2><b>Opmerkingen</b></h2>";
      foreach ($stmt->fetchAll() as $row)
      { 
        echo "
          \"".$row["Opmerking"]."\"<br>
          -".GetUserName($row["GebruikerID"])." ".$row["GeplaatstOp"]."<br><hr>
        ";
      }
    }
  }
}

function AddLicenseForm()
{
  echo "
    <div class=\"col-7\">
      <form action=\"MainMenuPHP.php\" method=\"post\">
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
    <input type=\"text\" name = \"LicenseName\" rows = \"3\" cols = \"80\" value=".$result["LicentieNaam"]."><br>
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

function EditLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate, $UserID)
{
  date_default_timezone_set('Europe/Amsterdam');
  $CurrentDate = date('Y/m/d');
  $Audience = $_POST["Audience"];

  $sql ="UPDATE licentie SET LicentieNaam=:LicenseName, Beschrijving=:Description, InstallatieOmschrijving=:InstallDesc, VerloopDatum=:ExpirationDate, GebruikerID=:UserID, LaatstAangepast=:CurrentDate, Doelgroep=:Audience WHERE LicentieID=:LicenseID";
  $conn = connectDB();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":LicenseID", $_SESSION["LicenseID"], PDO::PARAM_STR);
  $stmt->bindParam(":LicenseName", $LicenseName, PDO::PARAM_STR);
  $stmt->bindParam(":Description", $Description, PDO::PARAM_STR);
  $stmt->bindParam(":InstallDesc", $InstallDesc, PDO::PARAM_STR);
  $stmt->bindParam(":ExpirationDate", $ExpirationDate);
  $stmt->bindParam(":CurrentDate", $CurrentDate);
  $stmt->bindParam(":Audience", $Audience);
  $stmt->bindParam(":UserID", $UserID);
  $res = $stmt->fetch(PDO::FETCH_ASSOC);
  if($stmt->execute()){
    unset( $_SESSION["tempLicenseName"]);
    $_SESSION["LicenseID"] = $_SESSION["LicenseID"];
    $_SESSION["LicenseNameShow"] = $LicenseName;
    $_SESSION["DescriptionShow"] = $Description;
    $_SESSION["InstallDescShow"] = $InstallDesc;
    $_SESSION["LastChangedShow"] = $CurrentDate;
    $_SESSION["UserIDShow"] = $UserID;
    $_SESSION["ExpirationDateShow"] = $ExpirationDate;
    $_SESSION["AudienceShow"] = $Audience;
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

if(isset($_POST["csv"]))
{
  $filename = "licenties.csv";

  header("Content-type: text/csv; charset=utf-8");
    
  header("Content-Disposition: attachment; filename=$filename");
  $fp = fopen('php://output', 'w');
  fputcsv($fp, array('Licentienaam', 'Beschrijving', 'Installatie omschrijving'), ";");
  $sql = "SELECT LicentieNaam, Beschrijving, InstallatieOmschrijving FROM licentie"; 
  $conn = connectDB();
  $stmt = $conn->prepare($sql);
  $stmt->execute(); 
  
  while ($res = $stmt->fetch(PDO::FETCH_ASSOC))
  {
    fputcsv($fp, $res, ';');
  }
  fclose($fp);
  exit();
}

if (isset($_POST["Home"]))
{
  unset($_SESSION["LicenseNameShow"]);
  unset($_SESSION["DescriptionShow"]);
  unset($_SESSION["InstallDescShow"]);
  unset($_SESSION["LastChangedShow"]);
  unset($_SESSION["ExpirationDateShow"]);
  unset($_SESSION["UserIDShow"]);
  unset($_SESSION["LicenseIDShow"]);
  unset($_SESSION["AudienceShow"]);
  header("Location: MainMenu.php");
  exit;
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
      $ExpirationDate = $_POST["ExpirationDate"];
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
      $ExpirationDate = $_POST["ExpirationDate"];
      EditLicense($LicenseName, $Description, $InstallDesc, $ExpirationDate, $_SESSION["UserID"]);
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

?>