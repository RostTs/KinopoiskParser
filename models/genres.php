<?php

 class Genres {
      private $table = 'genres'; 
      private $conn;

    // Table values
      public $kp_id;
      public $genre;
      public $is_parsed;

      function __construct ($db) {
          $this->conn = $db;
      }

      // Insert genres
      public function insert(){
        // PDO executing
        $sql = 'INSERT INTO ' . $this->table . ' (kp_id,genre,is_parsed) VALUES (:kp_id,:genre,:is_parsed)';
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':kp_id',$this->kp_id);
        $stmt->bindParam(':genre',$this->genre); 
        $stmt->bindParam(':is_parsed',$this->is_parsed);

        if($stmt->execute()) {
          return true;
          } else {                 
            echo "ERROR: ";
            print_r($this->conn->errorInfo());    
            return false;
          }
          return $stmt;
      }

      // Parse genre
      public function select() {
        $sql = 'SELECT DISTINCT * FROM ' . $this->table;
       $genres = array();
        $stmt = $this->conn->query($sql);
        while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
          array_push($genres,$row );
        }
       return $genres;
      }

      public function create_tables($genres) {
         foreach ($genres as $genre) {

          if (strpos($genre, '-') !== false) {
           $genre = preg_replace('/-/','_',$genre);
           } else if (strpos($genre, ' ') !== false) {
            $genre = preg_replace('/ /','_',$genre);

           }
      

          $sql = "CREATE TABLE $genre (
            id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            kp_id int(255),
            name varchar(255),
             year varchar(255),
              rating float(11),
              type varchar(255)
          );";
          $stmt = $this->conn->prepare($sql);
        if($stmt->execute()) {
          echo "success " . '<br>';
          } else {                 
           echo "error" . $genre;
          }

         }

      }

      public function CompleteStatus($genre) {
        $sql = "UPDATE $this->table SET is_parsed = 1 WHERE genre = $genre";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return "success";
        
      }

      public function checkStatus($kp_id){
         $sql = "SELECT DISTINCT is_parsed FROM $this->table WHERE kp_id = $kp_id";
          $stmt = $this->conn->query($sql);
          $row = $stmt->fetch(PDO::FETCH_OBJ);
           return $row;
      }

 }
