<?php
  class Movies {
    private $table; 
    private $conn;

    function __construct ($db) {
        $this->conn = $db;
    }
     // Insert movies data into DB
    public function insert($total,$genre) {
      $this->table = $genre;
      $count = count($total['0']);
      // Check if movie dont have a poster
     for ($i=0; $i < $count ; $i++) { 
       if ($total['0'][$i] == 0) {
         // If there is no poster then movie will be missed
            continue;
       } else {
           // PDO executing

         $sql = 'INSERT INTO ' . $this->table . ' (kp_id,name,year,rating,type) VALUES (:kp_id,:name,:year,:rating,:type)';
         $stmt = $this->conn->prepare($sql);

         $stmt->bindParam(':kp_id',$total['0'][$i]);
         $stmt->bindParam(':name',$total['1'][$i]);
         $stmt->bindParam(':year',$total['2'][$i]);
         $stmt->bindParam(':rating',$total['3'][$i]); 
         $stmt->bindParam(':type',$total['4'][$i]);

         if(!$stmt->execute()) {                
             echo "ERROR: ";
             print_r($this->conn->errorInfo());    
             echo '<br>';
           }
       }
     } 
     return "success";
      
        }
         // Change genre status to parsed
        public function CompleteStatus($genre) {

          $this->table = 'genres';
          $sql = "UPDATE $this->table SET is_parsed = 1 WHERE genre=?";
      
          $stmt = $this->conn->prepare($sql);
          if($stmt->execute([$genre])){
            return "success";
          } else {
            echo "ERROR: ";
            print_r($this->conn->errorInfo());    
            echo '<br>';
          }

        } 

        public function saveParsing($genre,$page){
           $this->table = 'save';
           $sql = 'INSERT INTO ' . $this->table . ' (genre_id,page) VALUES (:genre_id,:page)';
          //  echo $sql . '<br>';
          //  echo $genre . '<br>';
          //  echo $page . '<br>';
           $stmt = $this->conn->prepare($sql);
           $stmt->bindParam(':genre_id', $genre);
           $stmt->bindParam(':page', $page);

           if($stmt->execute()){
            return "success";
          } else {
            echo "ERROR: ";
            print_r($this->conn->errorInfo());    
            echo '<br>';
          }
        }

        public function saver($genre){
          $this->table = 'save';
          $sql = "SELECT DISTINCT * FROM $this->table WHERE genre_id = $genre";

          $state = array();
          $stmt = $this->conn->query($sql);
          $result = $stmt->fetch();
          return $result['page'];
       }

       public function removeSave($page){
        $this->table = 'save';

        $sql = "DELETE FROM $this->table WHERE page = :page";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':page', $page);
        $stmt->execute();

        return "success";
     }

    }

  