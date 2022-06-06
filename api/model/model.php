<?php
include_once "model/modelDatos.php";
define('HOST',$db_host);
define('BASE',$db_base);
define('USER',$db_user);
define('PASS',$db_pass);

class model {

  protected $conectarBaseDeDatos;

  function __construct(){
    try {
      $this->conectarBaseDeDatos = new PDO('mysql:host='.HOST.';dbname='.BASE.';charset=utf8mb4',USER,PASS,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));        
    }
    catch (Exception $e) {
      echo "error";
      print "¡Error bd!: " . $e->getMessage() . "<br/>";
    }
  }

}