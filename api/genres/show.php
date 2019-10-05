<?php

include  "../../config/DB_conn.php";
include  "../../models/genres.php";

 $database = new Database;
 $db = $database->connect();
 
  $genresObj = new Genres($db);

 $genres = array();

  $all_genres = $genresObj->select();
  foreach ($all_genres as $genre) {
    $genre = $genre->genre;
    array_push($genres,$genre);
  }
  $genres = array_unique($genres);
    $create = $genresObj->create_tables($genres);

  