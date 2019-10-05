<?php

 class Database {
	 // DB Params
	 private $host_name = 'localhost';
	 private $db_name = 'kinopoisk';
	 private $user_name = 'root';
	 private $password = 'root';
     private $conn;
     
     public function connect() {
         // Set connection to null
         $this->conn = null;
         // DB PDO connection
         try{
          $dbh = new PDO('mysql:host=' . $this->host_name . ';dbname=' . $this->db_name . ';charset=utf8', 
                          $this->user_name, 
                          $this->password);
                         
         } catch(PDOExeption $e) {
           echo 'Connection failed:' . $e->getMessage();
           die();
         }
         return $dbh;
     }
 }



