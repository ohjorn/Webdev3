<?php 
//ConnectToDB
function connectDB()
{
  $host = "127.0.0.1";
  $databaseName = "licentiedb";
  $dns = "mysql:host=$host;dbname=$databaseName";
  $username = "root";
  $password = "";

  $conn = new PDO($dns, $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $conn;
}
//ConnectToDB ^^^
?>