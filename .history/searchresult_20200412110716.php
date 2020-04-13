<?php
      require("MainMenuPHP.php");
    if (!class_exists('connectDB'))
    {
        include_once("Connect.php");
    }
    $conn = connectDB();
    $output = '';
    $searchresult = $_POST["search"];
    
    //alle data uit LicentieID die lijken op "tekst in zoekvenster"
    $sql = $conn->prepare("SELECT * FROM `licentie` WHERE `LicentieNaam` LIKE '%$searchresult%'");
    $sql->execute();
    //Als er regels gelijk zijn aan wat er gezocht wordt
    if ($sql->rowCount() != 0) 
    {
        //Lijst met styling
        $output .= '<ul class="list-unstyled components">';

        //Oproepen van de gezochte lijst punten
        while ($row = $sql->fetch()) 
        {
            $output .= '
                <li>
                    <form action="MainMenuPHP.php" method="post">
                        <input type="hidden" name="LicenseID" value="'.$row["LicentieID"].'" >
                        <input type="submit" name="LicenseNameLoad" value="'.$row["LicentieNaam"].'" >
                    </form>
                </li>
            ';
        }
        $output .= '<hr>';
        echo $output;
    } 
    else 
    {
        echo 'Data Not Found';
    }
    if (isset($_GET['Audience'])){
    //alle data uit LicentieID die lijken op "tekst in zoekvenster"
    $sql = $conn->prepare("SELECT * FROM `licentie` WHERE `Doelgroep` LIKE '%$searchresult%'");
    $sql->execute();
    //Als er regels gelijk zijn aan wat er gezocht wordt
    if ($sql->rowCount() != 0) 
    {
        //Lijst met styling
        $output .= '<ul class="list-unstyled components">';

        //Oproepen van de gezochte lijst punten
        while ($row = $sql->fetch()) 
        {
            $output .= '
                <li>
                    <form action="MainMenuPHP.php" method="post">
                        <input type="hidden" name="LicenseID" value="'.$row["LicentieID"].'" >
                        <input type="submit" name="LicenseNameLoad" value="'.$row["LicentieNaam"].'" >
                    </form>
                </li>
            ';
        }
        $output .= '<hr>';
        echo $output;
    } 
    else 
    {
        echo 'Data Not Found';
    }
    } 
?>