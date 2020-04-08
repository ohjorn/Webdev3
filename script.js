//als alles geladen is
$(document).ready(function(){
    //start zoek functie als iets wordt ingetypt
    $('#search_text').keyup(function(){
        //variabele voor de ingevoerde tekst
        var txt = $(this).val();
        //Als er niks is ingevoerd
        if (txt != '') 
        {
            //Ajax oproep aan server
            $.ajax({
                //Locatie van php oproep bestand
                url:"searchresult.php",
                //Methode
                method:"POST",
                //data die verzonden wordt naar server
                data:{search:txt},
                //datatype dat opgevraagd wordt van server
                dataType:"text",
                //Als oproep is gelukt wordt er terug gestuurd
                success:function(data)
                {
                    //de zoek data die opgevraagd is
                    $('#result').html(data);
                }
            });
        }
        else
        {
            //voer dit uit in html bestand
            $('#result').html('');
        }
    });
});