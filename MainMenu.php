<?php
      require("MainMenuPHP.php");
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
  }
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
    <link rel="stylesheet" href="stylesheet.css">
    <script src="script.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>Main menu</title>
</head>
<body>
  <br>
  <main>
    <div class="row justify-content-center" id="rowmargin">
      <div class="col-2" id="sidebaredit">
        <nav id="sidebar">
          <div class="sidebar-header form-group" id="sidebarheaderedit">
            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Licentie naam zoeken">
          </div>
          <div class="row">
            <div class="col">
              <div class="form-group" id="submitbtns">   
                <form method="POST">                     
                  <button type="submit" class="btn" name="csv" id="buttondesign">Exporteer naar csv</button>
                  <button type="submit" class="btn" name="Home" id="buttondesign"><i class="glyphicon glyphicon-home"></i></button>
                </form>
              </div>
            </div>
            <div class="col">
              <a href="#sortingmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Sorteer op</a>
              <ul class="collapse list-unstyled" id="sortingmenu">
                <li>
                  <a href="MainMenu.php?Sort=1">A t/m Z</a>
                </li>
                <li>
                  <a href="MainMenu.php?Sort=2">Doelgroep</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="overflow-auto">
            <!--Div die de gezochte punten laat zien-->            
            <div id="result"></div>
            <!--Lijst met alle licenties-->
            <ul class="list-unstyled components">
              <?php
                GetLicense();
                if (IsAdmin())
                {
                  echo "
                    <li style=margin-top:10px;>
                      <form action=\"\" method=\"post\">
                        <input type=\"submit\" class=\"btn\" id=\"buttondesign\" name=\"Add-submit\" style= \"margin-bottom: 10px;\" value=\"Licentie toevoegen\">
                      </form>
                    </li>
                  ";
                }
              ?>
            </ul>
          </div>
          <span class="ban alert alert-primary jummie3">Er zijn momenteel <?=$_SESSION["counter"]?> licenties</span>                    
        </nav>                
      </div>
      <div class="col-9" id="mainmenu">
        <!--Navigatie balk-->
        <nav class="navbar navbar-expand-lg navbar-light" id="navbarmain">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <a class="navbar-brand" id="logo" href="MainMenu.php"><img src="nhl.png" width="50" height="50" alt="NHLStenden logo.png"></a>
            </li>
          </ul>
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <form action="" method="post">
                <?php
                  if (IsAdmin())
                  {
                    echo "<input type=\"submit\" class=\"btn btn-primary\" id=\"buttondesign\" name=\"toggle-account-management\" value=\"Accounts beheren\">";
                  }
                ?>
                <input type="submit" class="btn btn-primary" name="logout-submit" value="Uitloggen">
              </form>
            </li>
          </ul>
        </nav>
        <div class="row" id="mainmenuroundborder">
          <div class="col-8">
            <?php 
              
              if(isset($_POST["Edit-submit"]) || isset($_SESSION["EditLicenseError"]) )
              {
                EditLicenseForm();
                if (isset($_SESSION["EditLicenseError"]))
                {
                  echo $_SESSION["EditLicenseError"];
                }   
                              
              }
              else if (isset($_POST["Add-submit"]) || isset($_SESSION["AddLicenseError"]))
              {
                AddLicenseForm();
                if (isset($_SESSION["AddLicenseError"]))
                {
                  echo $_SESSION["AddLicenseError"];
                }
              }
              else if(isset($_POST["Delete-submit"]))
              {
                DeleteLicense();
              }
              else
              {
                LoadLicense();
                
              }
              if($_SESSION["home"]){
                Expire();
              }
            ?>
          </div>
          <div class="col-4 bg-light" id="comments">
            <?php
              if (!(isset($_POST["Edit-submit"])) && !(isset($_POST["Add-submit"])) && !(isset($_SESSION["AddLicenseError"])))
              {
                LoadComments();
              }
              else if (isset($_SESSION["AddLicenseError"]))
              {
                unset($_SESSION["AddLicenseError"]);
              }
            ?>
          </div>
        </div>
      </div>
    </div>
   
      
  <div id="id01" class="modal modalstyle">
    <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">×</span>
    <form class="modal-content" method="POST">
      <div class="container">
        <h1>Licentie verwijderen</h1>
        <p>Wilt u deze licentie verwijderen?</p>
      
        <div class="clearfix">
          <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Annuleer</button>
          <button type="submit" name="Delete-submit" class="deletebtn">Verwijderen</button>
        </div>
      </div>
    </form>
  </div>
  </main>

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>