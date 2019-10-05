<?php

// Указываем пространство имен
  namespace Facebook\WebDriver;

// Указываем какие классы будут использоватся
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

//Подключаем автолоадер классов
require_once('vendor/autoload.php');
include  "models/movies.php";
require_once('config/DB_conn.php');

use Movies;
use Database;

 class Selenium {
     public $genre_id; 
     public $movies;
     public $database;
     public $host = 'http://localhost:4444/wd/hub';
     public $catchState;
     

   function __construct($genre_id) {
     $this->genre_id = $genre_id;
   }
  // Get pages count for succession
   public function getPages(){
     $testUrl = "https://www.kinopoisk.ru/";
    $url = "https://www.kinopoisk.ru/s/type/film/list/1/order/rating/m_act[genre][0]/$this->genre_id/perpage/200/";
    $driver = RemoteWebDriver::create($this->host, DesiredCapabilities::chrome());
    $driver->get($url);
    $pages_count = $driver->findElement(WebDriverBy::cssSelector("span.search_results_topText"));
    $category_pages =  $pages_count->getText();
    $category_pages =  substr($category_pages, 38);  
    $driver->close();
    return $category_pages;
   }

  // Main parsing part
   public function Parse($pages,$Parentgenre){
    // Start with counter
      $starttime = microtime(true); 
      // Database connection
      $this->database = new Database();
      $db_conn = $this->database->connect();

      $pages = $pages/200;
      $pages = ceil($pages);
        echo "Total pages in $Parentgenre is " . $pages . '<br>';
        echo '<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#target">Show/hide logs</button>';
        echo '<div class="collapse" id="target">';
         // Getting through all pages

         if (strpos($Parentgenre, '-') !== false) {
          $genre = preg_replace('/-/','_',$Parentgenre);
          } else if (strpos($Parentgenre, ' ') !== false) {
           $genre = preg_replace('/ /','_',$Parentgenre);
          } else {
            $genre = $Parentgenre;
          }
          
          $this->movies = new Movies($db_conn);
          $page = $this->movies->saver($this->genre_id);
           if(!empty($page)){
             $startPage = $page;
           } else {
            $startPage = 1;
           }
        for ($i = $startPage; $i <= $pages; $i++) {    
          $multyTime = microtime(true);
          $total = array();
          $ids = array();
          $names = array();
          $years = array();
          $ratings = array();
          $types = array();
          // Calculate the percentation
          $percent = intval($i/$pages * 100)."%";
          // Selenium set up
          try {
            $url = "https://www.kinopoisk.ru/s/type/film/list/1/order/rating/m_act[genre][0]/$this->genre_id/perpage/200/page/$i/";
            $driver = RemoteWebDriver::create($this->host, DesiredCapabilities::chrome());
            $driver->get($url);
            // Note all needed data
            $names_in = $driver->findElements(WebDriverBy::cssSelector("img.flap_img"));
            $years_in = $driver->findElements(WebDriverBy::cssSelector("span.year"));
            $ratings_in = $driver->findElements(WebDriverBy::className("rating"));
            $types_in = $driver->findElements(WebDriverBy::cssSelector("p.name"));
             // Format data
                foreach ($names_in as $names_single) {
                  $name = $names_single->getAttribute('alt');
                  $id = $names_single->getAttribute('title');
                  $id = substr($id, 16);
                  $id = substr($id, 0, -4);
                  if (strlen($id) <=1) {
                    $id = 0;
                  }
                  array_push($ids,$id);
                  array_push($names,$name);
                }
                    foreach ($years_in as $years_single) {
                      $year = $years_single->getText();
                      array_push($years,$year);              
                    }
                    foreach ($ratings_in as $ratings_single) {
                      $rating = $ratings_single->getText();
                      array_push($ratings,$rating);              
                    }
                    foreach ($types_in as $types_single) {
                      $text = $types_single->getText();
                      if (strpos($text, 'сериал') !== false) {
                        array_push($types, 'Сериал');
                      } else {
                      array_push($types, 'Фильм');
                      }

                }
                // Collect all data into one array
                array_push($total,$ids);        // 0
                array_push($total,$names);      // 1
                array_push($total,$years);      // 2
                array_push($total,$ratings);    // 3
                array_push($total,$types);      // 4
 
                
                $insert = $this->movies->insert($total,$genre);
                $driver->close();
                // Logs info
                $endMultyTime = microtime(true);
                $totalTime = round($endMultyTime) - round($multyTime);
                echo '<br>' . "Page $i was parsed in $totalTime seconds!";
                // Progress bar output
                echo '<script language="javascript">
                document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#44E519;\">&nbsp;</div>";
                document.getElementById("information").innerHTML="'.$percent.' processed.";
                </script>';
                // This is for the buffer achieve the minimum size in order to flush data
                echo str_repeat(' ',1024*64);
                // Send output to browser immediately
                flush();
                // Sleep one second so we can see the delay
                sleep(1);
          } 
            catch (\Exception $e) {
              if(!empty($page)){
                $this->movies->removeSave($startPage);
              }
                echo '<br>' . 'There is an error - ' . $e->getMessage() . '<br>';
                $this->movies->saveParsing($this->genre_id,$i);
               die();
            }
          
        }
         echo '</div>';
            if(!empty($page)){
              $this->movies->removeSave($startPage);
            }
            // Change category status
            $this->movies->CompleteStatus($Parentgenre);

            $endtime = microtime(true); // Bottom of page
            $time = round($endtime) - round($starttime);
            $avarage =  $time/$pages;
            // Output load time
            echo '<br>' . "Whole genre was parsed in $time seconds!";
            echo '<br>' . "Avarage page was parsed in $avarage seconds!";
            
            return "success";
      }
    }

     // Command to run selenium server
 //  java -jar selenium-server-standalone-3.141.59.jar