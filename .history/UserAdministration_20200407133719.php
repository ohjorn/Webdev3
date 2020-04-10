<?php 
  require("Function.php");
  if (session_status() == PHP_SESSION_NONE) 
  {
    session_start();
  }
  try
  {
    IsLogged();
  }
  catch(exception $ex)
  {
    header("Location: LoginScreen.php");
    exit;
  }
  if (!(IsAdmin()))
  {
    header("Location: MainMenu.php");
    exit;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap 4 Website Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="UserAdministration.css">
<style>

</style>
</head>
<body>
<div class="row text-center">
  <h1 class="col-sm-6">Gebruiker toevoegen</h1>
  <h1 class="col-sm-3">Administrators</h1>
  <h1 class="col-sm-3">Lezers</h1>
</div>

<div class="row text-center">
<div class="col-sm-6" > 
<form action="UserAdministration.php" method="post">
  Gebruikersnaam:<br><input type="str" name="Username"><br>
  Wachtwoord:<br><input type="password" name="Password"><br>
  Wachtwoord hertypen:<br><input type="password" name="Password2"><br><br>
  <input type="radio" name="Rights" value="0" checked>
  <label for="Lezer">Lezer</label><br>
  <input type="radio" name="Rights" value="1">
  <label for="Administrator">Administrator</label><br>
  <input type="submit" class="btn btn-primary" name="CreateAcc" value="Aanmaken">
</form><br>
<button type="submit" onclick="window.location.href = 'MainMenu.php';" class="btn btn-primary" name="BackToMainMenu">Terug</button>
</div> 
<p class="col-sm-3">
<?php
$conn = ConnectDB(); 
  $UniekeID = 1;
  $sql = "SELECT * FROM gebruiker WHERE Rechten = 1 ORDER BY UniekeLoginNaam ASC";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue("UniekeID", $UniekeID, PDO::PARAM_STR);
  $stmt->execute();

  //hier worder de arrays aangemaakt, anders komen er foutmeldingen als de array niet bestaat. 
  $UniqueLoginName = [];
  $Rights = [];
  $Password = [];
  $UserID = [];

  // voor elke rij in de table gaat hij erlangs om de array te vullen
  foreach ($stmt->fetchAll() as $row) 
  {
      //zet de gegevens in een array.
    array_push($UniqueLoginName, $row["UniekeLoginNaam"]);
    array_push($Rights, $row["Rechten"]);
    array_push($Password, $row["Wachtwoord"]);
    array_push($UserID, $row["GebruikerID"]);
  }

  //hier worden de html code weergegeven om de table te maken. 
  //hij controleert de lengte en  maakt voor elke rij in de tabel een rij. 
  echo "<table>";
  for ($i = 0; $i < count($UniqueLoginName); $i++)
  {
    //maakt hier de rijen aan met de gegevens er in. 
    echo "<tr>";
    echo "<td>".$UniqueLoginName[$i]."</td>";
    echo "<td>";
    echo "<input type=\"submit\"name=\"ChangeBtn\"value=\"Change\">";
    echo "</td>";
    echo "</tr>";
  } 
  echo "</table>";
?>
</p> 
<p class="col-sm-3">
<?php 
  $sql2 = "SELECT * FROM gebruiker WHERE Rechten = 0 ORDER BY UniekeLoginNaam ASC";
  $stmt2 = $conn->prepare($sql2);
  $stmt2->bindValue("UniekeID", $UniekeID, PDO::PARAM_STR);
  $stmt2->execute();

  //hier worder de arrays aangemaakt, anders komen er foutmeldingen als de array niet bestaat. 
  $UniqueLoginName2 = [];
  $Rights2 = [];
  $Password2 = [];
  $UserID2 = [];

  foreach ($stmt2->fetchAll() as $row) 
  {
    //zet de gegevens in een array.
    array_push($UniqueLoginName2, $row["UniekeLoginNaam"]);
    array_push($Rights2, $row["Rechten"]);
    array_push($Password2, $row["Wachtwoord"]);
    array_push($UserID2, $row["GebruikerID"]);
  }

  //hier worden de html code weergegeven om de table te maken. 
  //hij controleert de lengte en  maakt voor elke rij in de tabel een rij. 
  echo "<table>";
  for ($i = 0; $i < count($UniqueLoginName2); $i++)
  {
    //maakt hier de rijen aan met de gegevens er in. 
    echo "<tr>";
    echo "<td>".$UniqueLoginName2[$i]."</td>";
    echo "<td>";
    echo "<input type=\"submit\"name=\"ChangeBtn\"value=\"Change\">";
    echo "</td>";
    echo "</tr>";
  } 
  echo "</table>";
?>
</p>
</div> 
</body>
</html>