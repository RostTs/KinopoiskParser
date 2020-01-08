<?php
// Include files
include  "logs.php";
include  "config/DB_conn.php";
include  "selenium.php";
include  "models/genres.php";

 // Create objects
  $cookiename = "ss";
  $GenresArray = array();
  $database = new Database ();
  $db = $database->connect();
  $logs = new Logs($db);
  $logs->create('start');
  $genresObg = new Genres($db);
  // Set cookie with genres
    if(isset($_COOKIE[$cookiename])){
      $AllGenres = json_decode(gzuncompress($_COOKIE[$cookiename])); 
    } elseif($AllGenres == NULL) {
      header("Location: api/genres/select.php");   
      $genres = $_POST['genres'];
      setcookie($cookiename, gzcompress($genres),time() + (86400 * 30) , "/");
      $AllGenres = json_decode(gzuncompress($_COOKIE[$cookiename])); 
    }
   set_time_limit(999999);
  ?>
<!-- GUI output -->
<!DOCTYPE html>
<html>
 <head>
   <title>KP_parser</title>
   <meta charset="utf-8">
   <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> -->
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
 </head>
 <body>

   <div class="container mt-5">
     <div class="row col-12 bg-light">
       <form action="api/genres/insert.php">
        <!-- Parse genres to database -->
         <input type="submit" name="Genres"  data-toggle="tooltip" data-placement="top" title="Insert genres to database (use once)" value="Parse genres" class="btn btn-info " >
       </form>
       <!-- Select genre to parse -->
       <form  class="form-inline ml-5" >
          <select name = "genre" class="form-control mr-1"><?php
            foreach ($AllGenres as $genre) {
              $checkStatus = $genresObg->checkStatus($genre->kp_id);
              if($checkStatus->is_parsed == 1) {
                echo "<option class='bg-warning' value='$genre->kp_id'>$genre->genre</option>";
              } else {
                echo "<option  value='$genre->kp_id'>$genre->genre</option>";
              }
            }
         ?></select>
         <input type="submit" name="Movies"  data-toggle="tooltip" data-placement="top" title="Select genre to parse" value="Parse movies" class="btn btn-success " >
       </form>
       <!-- Create all genres tables-->
       <form class="form-inline ml-5"  action="api/genres/show.php">
         <input type="submit" name="Genres"  data-toggle="tooltip" data-placement="top" title="Create all genres tables (use once)" value="Create tables" class="btn btn-info " >
       </form>

     </div> 
      <div class="d-flex justify-content-center mt-5"> 
      <div id="progress" class="" style="width:500px;border:1px solid #ccc;"></div>
          <!-- Progress information -->
          <div id="information" style="width"></div>
      </div>

     <?php
     // Buffer 
      ob_end_flush();
      ob_implicit_flush();
       // Parsing part
         if(isset($_GET['Movies'])){
          $logs->create('info'); 
          // Output logs
          foreach (array_reverse($logs->show()) as $show) {
            echo $show . '<br>';
          }
           $selectedGenre = $_GET['genre'];
           $genreName = null;
            foreach($AllGenres as $genre) {
             if($selectedGenre == $genre->kp_id) {
                $genreName = $genre;
                break;
             }
            }    
           $selenium = new Facebook\WebDriver\Selenium($selectedGenre);
           $pages = $selenium->getPages();
           $parse = $selenium->Parse($pages,$genreName->genre);
           if($parse == "success"){ 
            $logs->create('success'); 
            echo $logs->showLast();
           } else {
            $logs->create('error'); 
            echo $logs->showLast();
           }

         } else {
          foreach (array_reverse($logs->show()) as $show) {
            echo $show . '<br>';
      
          }
         }


       ?>


   </div>


 </body> 
 
</html>


