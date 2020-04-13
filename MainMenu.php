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
    <script src="script.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>Main menu</title>
  <style>

    .wrapper {
      display: flex;
      width: 100%;
      align-items: stretch;
    }
    .sidebar {

        width: 0px;
        position: fixed;
        top: 80px;
        left: 0;
        height: 100vh;
        z-index: 999;
        background: ghostwhite;
        color: ghostwhite;
        transition: all 0.3s;
        bottom: 0px;
        overflow-y: scroll;
    }



    /* Style the submit button */
    .button1 {
        float: left;
        width: 100%;
        height: 40px;
        background: #2196F3;
        color: white;
        font-size: 14px;
        border: 1px solid grey;
        border-left: none; /* Prevent double borders */
        cursor: pointer;
    }

    .button1:hover {
        background: #0ca6da;
    }

    .searchbar{
        float:left;
        width: 100%;
        height: 40px;
        font-size: 14px;
        border: 1px solid grey;
        background: white;
    }

    .license{
        width: 100%;
        height: 40px;
        background: white;
        color: gray;
        font-size: 14px;
        border: 1px solid lightgray;
        border-left: none; /* Prevent double borders */
        cursor: pointer;

    }

    #main{
        margin-top: -40px;
        transition: all 0.3s;
    }



    .btn{
         color: white;

     }

    .btn1{
        color: black;

    }
    .modalstyle {
		  text-align: center;		
		  border-radius: 5px;
		  font-size: 13px;
		  padding: 10px 5px 4px;
      margin: 250px 10px 5px;
    }

  </style>
</head>
<body>
<!--navbar -->
<nav style="height: 80px; background: #008487; border-radius: 0px;" class="navbar fixed-top navbar-expand-lg">
    <a class="navbar-brand" href="/"> <img style= "margin-top: -25px;" src="nhl.png" width="70px" height="70px" alt=""></a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto navbar-right">
            <li class="nav-item">

                <form action="" method="post">
                    <button type="button"  class="btn" onclick="openNav()">
                        <i class="fas fa-align-left"></i>
                        <span>☰</span>
                    </button>

                    <?php
                    if (IsAdmin())
                    {
                        echo "
                      
                        <input type=\"submit\" class=\"btn\" name=\"Add-submit\"  value=\"Licentie toevoegen\">
                      
                    ";
                    }
                    ?>

                    <?php
                    if (IsAdmin())
                    {
                        echo "<input type=\"submit\" class=\"btn\" name=\"toggle-account-management\" value=\"Accounts beheren\">";
                    }
                    ?>
                    <button type="submit" class="btn" name="csv">Exporteer naar csv</button>
                    <input type="submit" class="btn " name="logout-submit" value="Uitloggen">

                </form>
            </li>


        </ul>
    </div>
</nav>

<!--sidebar -->

<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <input type="text" class="form-control" id="search_text" name="search_text" class="searchbar" placeholder="Licentie naam zoeken">
    </div>

    <?php if (IsAdmin())
    {
        echo "
          <li style=margin-top:10px;>
            <form action=\"\" method=\"post\">
              <input type=\"submit\" class=\"button1\" name=\"Add-submit\" style= \"margin-bottom: 10px;\" value=\"Licentie toevoegen\">
            </form>
          </li>
        ";
    }
    ?>
    <br><br><br>
    <ul class="list-unstyled components">
        <a href="#sortingmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Sorteer op</a>
        <ul class="collapse list-unstyled" id="sortingmenu">
          <li>
            <a href="MainMenu.php?Sort=1">A t/m Z</a>
          </li>
          <li>
            <a href="MainMenu.php?Sort=2">Doelgroep</a>
          </li>
        </ul>
    </ul>
    </br>

    <div class="overflow-auto">
        <!--Div die de gezochte punten laat zien-->
        <div id="result"></div>
        <!--Lijst met alle licenties-->
        <ul class="list-unstyled components">
            <?php
            GetLicense();?>

        </ul>
        <br>
    </div>
    <span class="ban alert alert-primary jummie3">Er zijn momenteel <?=$_SESSION["counter"]?> licenties</span>

    <hr>
    <br><br><br><br>
</nav>

<script>
    function openNav() {
        if (document.getElementById("sidebar").style.width == "0px"){
            document.getElementById("sidebar").style.width = "250px";
            document.getElementById("main").style.marginLeft = "250px";
        }
        else{
            document.getElementById("sidebar").style.width = "0px";
            document.getElementById("main").style.marginLeft = "0px";
        }
    }


</script>
  <br>
  <main id="main">



      <div class="col-12" >


        <div class="row bg-light" style="height: 100vh;" >
          <div class="col-7">
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
          <div class="col-3">
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


  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </main>
</body>
</html>