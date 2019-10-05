<?php 
class Logs {
    public $type;
    public $conn;
    public $table = 'logs';
    public $message;
    public $logs_history = array();
    public $output = array();
    
    function __construct ($db) {
        $this->conn = $db;
    }

    public function create($type){

        switch($type) {
           case 'start':
           $this->message = "You just started parsing!";
           break;   
            case 'info':
            $this->message = "Parsing is going. Please wait ...";
            break;   
            case 'success':
            $this->message = "Category was succesfuly parsed!";
            break;   
            case 'error':
            $this->message = "There was an error!";
            break;   
        }
        $this->type = $type;

        $sql = 'INSERT INTO ' . $this->table . '(type,message) VALUES (:type, :message)';
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':type',$this->type);
        $stmt->bindParam(':message',$this->message);
        array_push($this->logs_history,$type);

        if($stmt->execute()) { 
            return true;
            } else {                 
              echo "ERROR: ";
              print_r($this->conn->errorInfo());    
              return false;
            }
         //   return $stmt;
      }
    
   public function show(){
     foreach($this->logs_history as $type) {
      $sql = 'SELECT * FROM ' . $this->table . ' WHERE type = ? ORDER BY id DESC LIMIT 1';

      switch($type) {
       case 'start':
       $colour = "primary";
       break;   
       case 'info':
       $colour = "warning";
       break; 
       case 'success':
       $colour = "success";
       break; 
       case 'error':
       $colour = "danger";
       break; 
    }
 
   $stmt = $this->conn->prepare($sql);  
     $stmt->execute([$type]);
     
     $answear = $stmt->fetch(PDO::FETCH_OBJ);
     $date = substr($answear->date, -8);
     $output = "  
     <div class='row col-12 p-2 border rounded bg-$colour mt-4 d-flex'>
     <h5 class='text-white d-inline p-2'>$answear->id</h5>
     <h5 class='text-white d-inline p-2'>$answear->message</h5>
     <h5 class='text-white ml-auto p-2'>$date</h5>
 
    </div> <!-- .row -->";
     array_push($this->output,$output);
     }

    return $this->output;
   }

   public function showLast(){
     $sql = 'SELECT * FROM ' . $this->table . ' ORDER BY id DESC LIMIT 1';

  $stmt = $this->conn->prepare($sql);  
    $stmt->execute();
    
    $answear = $stmt->fetch(PDO::FETCH_OBJ);

    switch($answear->type) {
      case 'start':
      $colour = "primary";
      break;   
      case 'info':
      $colour = "warning";
      break; 
      case 'success':
      $colour = "success";
      break; 
      case 'error':
      $colour = "danger";
      break; 
   }
    $date = substr($answear->date, -8);
    $output = "
    <div class='row col-12 p-2 border rounded bg-$colour mt-4 d-flex'>
    <h5 class='text-white d-inline p-2'>$answear->id</h5>
    <h5 class='text-white d-inline p-2'>$answear->message</h5>
    <h5 class='text-white ml-auto p-2'>$date</h5>

   </div> <!-- .row -->";
    array_push($this->output,$output);

   return $output;
  }

}
