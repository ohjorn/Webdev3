<?php 
//ConnectToDB
function connectDB()
{
  $host = "localhost";
  $databaseName = "licentiedb";
  $dns = "mysql:host=$host;dbname=$databaseName";
  $username = "root";
  $password = "";

  $conn = new PDO($dns, $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $conn;
}
//ConnectToDB ^^^

////Checks if the user logged in, if this is not the case they will be transported to the login page.
function IsLogged()
{
  if (!(isset($_SESSION["Username"])))
  {
    header("Location: LoginScreen.php");
  }
}
// ^^^

//Trims the string.
function validate($str) 
{
  return trim(htmlspecialchars($str));
}
// ^^^

//Checks if the user is an admin, returns true if this is the case.
function IsAdmin()
{
  $UserID = $_SESSION['UserID'];
  $conn = connectDB();
  try
  {
    $sql = "SELECT Rechten FROM gebruiker WHERE `GebruikerID` = :UserID";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("UserID", $UserID, PDO::PARAM_STR);
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
// ^^^

//Checks the Username of the user and returns the UserID that is paired with this name.
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
// ^^^

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
        if (!empty($row["UniekeLoginNaam"]))
        {
          return $row["UniekeLoginNaam"];
          exit;
        }
      }
      return "Verwijderde gebruiker";
    }
  }
  catch (PDOException $ex) 
  {
    echo "$ex";
  }
}
?>