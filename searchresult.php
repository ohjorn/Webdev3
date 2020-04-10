<?php
    include 'Connect.php';
    $conn = connectDB();
    $output = '';
    $searchresult = $_POST["search"];
    
    //alle data uit LicentieID die lijken op "tekst in zoekvenster"
    $sql = $conn->prepare("SELECT * FROM `licentie` WHERE `LicentieNaam` LIKE '%$searchresult%'");
    $sql->execute();
    if ($sql->rowCount() != 0) 
    {
        //Lijst met styling
        $output .= '<ul class="list-unstyled components">';

        //Oproepen van de gezochte lijst punten
        while ($row = $sql->fetch()) 
        {
            $output .= '
                <li>
                    <form action="Function.php" method="post">
                        <input type="hidden" name="LicenseID" value="'.$row["LicentieID"].'" >
                        <input type="submit" name="LicenseNameLoad" value="'.$row["LicentieNaam"].'" >
                    </form>
                </li>
            ';
        }
        //Geef weer op scherm
        echo $output;
    } 
    else 
    {
        //Anders foutmelding geven als er niks gevonden is
        echo 'Data Not Found';
    }
?>