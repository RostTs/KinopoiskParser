<?php

include  "../../config/DB_conn.php";
include  "../../models/movies.php";
include  "../../models/genres.php";

 $database = new Database;
 $db = $database->connect();

  $genresObj = new Genres($db);

 // $sql = "SELECT * FROM genres";

  $all_genres = $genresObj->select();
  print_r($all_genres);
