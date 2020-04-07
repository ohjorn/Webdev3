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

<?php
  $conn = ConnectDB(); 
  $UniekeID = 1;
  $sql = "SELECT * FROM licentie WHERE LicentieID = :UniekeID ORDER BY LicentieNaam ASC";
  $stmt = $conn->prepare($sql);
  $stmt->bindValue("UniekeID", $UniekeID, PDO::PARAM_STR);
  $stmt->execute();

  // voor elke rij in de table gaat hij erlangs om de array te vullen
  foreach ($stmt->fetchAll() as $row) 
  {
      //zet de gegevens in een array.
    $LicentieID = $row["LicentieID"]; 
    $LicentieNaam = $row["LicentieNaam"];
    $Beschrijving = $row["Beschrijving"];
    $Opmerking = $row["Opmerking"];
    $InstallatieOmschrijving = $row["InstallatieOmschrijving"];
    $LaatstAangepast = $row["LaatstAangepast"];
    $Verlopen = $row["Verlopen"];
    $GebruikerID = $row["GebruikerID"];
    $VerloopDatum = $row["VerloopDatum"];
  }
?>
    <style>
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #sidebar {
            padding-left: 10px;
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
                        <!--Sorteer menu-->
                            <a href="#sortingmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Sorteer op</a>
                            <ul class="collapse list-unstyled" id="sortingmenu">
                                <li>
                                    <a href="#">A t/m Z</a>
                                </li>
                                <li>
                                    <a href="#">Verloop datum</a>
                                </li>
                                <li>
                                    <a href="#">Start datum</a>
                                </li>
                            </ul>
                        </div>
                        <!--Sorteer knop-->
                        <div class="col">
                            <div class="form-group" id="submitbtns">                        
                                <button type="submit" class="btn btn-primary" name="search-submit">Zoeken</button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-auto">
                    <!--Licentie lijst-->
                        <!--Zoek resultaat komt in deze div-->
                        <div id="result"></div>
                        <ul class="list-unstyled components">
                            <li>
                                <a href=""><?php echo "<td>".$LicentieNaam."</td>"; ?></a>
                            </li>
                            <li>
                                <a href="">Licentie 2</a>
                            </li>
                            <li>
                                <a href="">Licentie 3</a>
                            </li>
                        </ul>
                    </div>
                    <div class="fixed-bottom">
                        <div class="row">
                            <div class="col-2 text-center" id="submitbtns">                        
                                <button type="submit" class="btn btn-primary" name="search-submit" style="margin-bottom: 10px;">Licentie toevoegen</button>
                            </div>
                        </div>
                    </div>                    
                </nav>                
            </div>
            <div class="col-10">
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
                <!--Informatie van gekozen licentie-->
                <div class="row bg-light">
                    <div class="col-4">
                        <h2><b>Beschrijving</b></h2>
                        <p><?php echo "<td>".$Beschrijving."</td>";?></p>
                        <br>
                        <h2><b>Opmerking</b></h2>
                        <p><?php echo "<td>".$Opmerking."</td>";?></p>
                        <br>
                        <h2><b>Installatie omschrijving</b></h2>
                        <p><?php echo "<td>".$InstallatieOmschrijving."</td>";?></p>
                        <br> 
                        <h2><b>Verloop datum</b></h2>
                        <p><?php echo "<td>".$VerloopDatum."</td>";?></p>
                        <br> 
                        <h2><b>Laatst aangepast</b></h2>
                        <p><?php echo "<td>".$LaatstAangepast."</td>";?></p>
                        <br> 
                        
                    </div>
                    <div class="col-6">
                        <br>
                        <div class="bg-secondary">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime at vitae veniam. Labore esse deleniti accusantium officiis velit vero, repudiandae obcaecati iusto rem harum ullam soluta voluptas fuga? Libero, sunt?</p>
                        </div>
                        <br>
                        <div class="bg-secondary">
                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Id excepturi maxime in assumenda eaque repellendus dolorum esse doloremque. Esse, quae hic quisquam corrupti delectus impedit ipsa voluptates voluptatum temporibus cum!</p>
                        </div>
                        <br>
                        <div class="bg-secondary">
                            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Autem laudantium ut ratione culpa obcaecati. Aperiam suscipit nam delectus earum consequuntur beatae, amet aut corporis minima, quae velit provident itaque inventore?</p>
                        </div>
                    </div>
                    <br>
                    <div class="col-8">
                        <div class="bg-secondary">
                            <h3><b>Informatie</b></h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit ex delectus quod pariatur culpa soluta nostrum fugit quibusdam a asperiores saepe nesciunt mollitia dolor, necessitatibus et odio incidunt eum. Doloremque. Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sequi, modi numquam in, harum fugiat culpa veniam asperiores aspernatur placeat quidem voluptate eveniet laudantium! Eius aliquid excepturi aperiam labore sit velit.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    </body>
</html>