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
        $output .= '<ul class="list-unstyled components">';

        while ($row = $sql->fetch()) 
        {
            $output .= '
                <li>
                    <a href=""><?php echo "<td>'.$row["LicentieNaam"].'</td></a>
                </li>
            ';
        }
        echo $output;
    } 
    else 
    {
        echo 'Data Not Found';
    }
?>