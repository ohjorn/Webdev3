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
    #sidebar {
      padding-left: 10px;
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
  <br>
  <main>
    <div class="row">
      <div class="col-2">
        <nav id="sidebar">
          <div class="sidebar-header form-group">
            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Licentie naam zoeken">
          </div>
          <div class="row">
            <div class="col">
              <a href="#sortingmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Sorteer op</a>
              <ul class="collapse list-unstyled" id="sortingmenu">
                <li>
                  <a href="#">A t/m Z</a>
                </li>
                <li>
                  <a href="#">Doelgroep</a>
                </li>
              </ul>
            </div>
            <div class="col">
              <div class="form-group" id="submitbtns">                        
                <button type="submit" class="btn btn-primary" name="search-submit">Zoeken</button>
              </div>
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
                        <input type=\"submit\" class=\"btn btn-primary\" name=\"Add-submit\" style= \"margin-bottom: 10px;\" value=\"Licentie toevoegen\">
                      </form>
                    </li>
                  ";
                }
              ?>
            </ul>
          </div>                     
        </nav>                
      </div>
      <div class="col-10">
        <!--Navigatie balk-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <a class="navbar-brand" href="#">"Logo?"</a>
            </li>
          </ul>
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <form action="" method="post">
                <?php
                  if (IsAdmin())
                  {
                    echo "<input type=\"submit\" class=\"btn btn-primary\" name=\"toggle-account-management\" value=\"Accounts beheren\">";
                  }
                ?>
                <input type="submit" class="btn btn-primary" name="logout-submit" value="Uitloggen">
              </form>
            </li>
          </ul>
        </nav>
        <div class="row bg-light">
          <div class="col-7">
            <?php 
              if(isset($_POST["Edit-submit"]))
              {
                EditLicenseForm();
              }
              else if (isset($_POST["Add-submit"]))
              {
                AddLicenseForm();
              }
              else if(isset($_POST["Delete-submit"])){
                DeleteLicense();
              }
              else
              {
                LoadLicense();
              }
            ?>
          </div>
          <div class="col-3">
            <?php
              if (!(isset($_POST["Edit-submit"])) && !(isset($_POST["Add-submit"])))
              {
                LoadComments();
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