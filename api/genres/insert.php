<?php

include  "../../config/DB_conn.php";
include  "../../models/genres.php";

 $database = new Database;
 $db = $database->connect();

  $genresObj = new Genres($db);

   $url = 'https://www.kinopoisk.ru/s/';
    // Curl section
    $curl = curl_init();

    curl_setopt($curl,CURLOPT_URL, $url);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);
     preg_match_all('!<select class="text el_6 __genreSB__" name="m_act\[genre]\[]" id="m_act\[genre]" multiple="multiple" size="6"><option value="">-<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><option value=\'(\d+)\' >(.*?)<\/option><\/select>!',$result,$genres);
     if(empty($genres)){
       echo "Captcha";
     } else{
       // Format results genres
        $kp_ids = array();
        $genreses = array();     
       for ($i=1; $i < 65 ; $i++) { 
         if($i % 2 == 0) {
             array_push($genreses,$genres[$i]);
         } else {
             array_push($kp_ids,$genres[$i]);
         }
       }
        // Work with database
       for ($i=0; $i < count($kp_ids); $i++) { 
           $genresObj->kp_id = $kp_ids[$i]['0'];
           $genresObj->genre = $genreses[$i]['0'];
           $genresObj->is_parsed = 0;

           $create = $genresObj->insert();
              if($create == true) {
                echo "Done!" . "<br>";
            } else {
                echo "Cannot insert new genres!" . "<br>";
            }
       }

     }
     $create = $genresObj->insert();

