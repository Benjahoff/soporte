<?php

require_once 'model/model.php';

class modelBase extends model {

  function __construct(){
    parent::__construct();
  }

  public function beginTransaction(){
    $this->conectarBaseDeDatos->beginTransaction();
  }

  public function commitTransaction(){
    $this->conectarBaseDeDatos->commit();
  }

  public function rollBackTransaction(){
    $this->conectarBaseDeDatos->rollBack();
  }

  //devuelve los registros de la tabla por filtro y orden
  public function getRegistrosTabla($la_tabla,$el_filtro,$el_orden, $tipo){
    try {
      if(!empty($el_filtro)){
          $el_filtro = "WHERE $el_filtro";
      }
      if(!empty($el_orden)){
          $el_orden = "ORDER BY $el_orden";
      }
      //echo "SELECT * from $la_tabla $el_filtro $el_orden";
      $sentencia = $this->conectarBaseDeDatos->prepare("SELECT * from $la_tabla $el_filtro $el_orden");
      $sentencia->execute();
      if($tipo=="O"){
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
      }
      elseif($tipo=="N"){
        return $sentencia->fetchAll(PDO::FETCH_NUM);
      }
      elseif($tipo=="B"){
        return $sentencia;
      }
      else{
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
      }
    } 
    catch (PDOException $e) {      
      return false;
    }  
  }

  //devuelve un registro segun ID
  public function getRegistroID($la_tabla, $el_id, $tipo){
    if(!empty($el_id)){
        $el_id = "WHERE id = $el_id";
    }
    try{
      $sentencia = $this->conectarBaseDeDatos->prepare("SELECT * from $la_tabla $el_id");
      $sentencia->execute(array($la_tabla,$el_id));
      if($tipo=="O"){
        return $sentencia->fetch(PDO::FETCH_OBJ);
      }
      elseif($tipo=="N"){
        return $sentencia->fetch(PDO::FETCH_NUM);
      }
      elseif($tipo=="B"){
        return $sentencia;
      }
      else{
        return $sentencia->fetch(PDO::FETCH_ASSOC);
      }
    } 
    catch (PDOException $e) {      
      return false;
    }  
  }

  //devuelve un registro segun ID Completo con tablas externas
  public function getRegistroIDCompleto($el_sel, $tipo){
    //echo "SELECT * from $la_tabla $el_id";
    try{
      $sentencia = $this->conectarBaseDeDatos->prepare($el_sel);
      $sentencia->execute();
      if($tipo=="O"){
        return $sentencia->fetch(PDO::FETCH_OBJ);
      }
      elseif($tipo=="N"){
        return $sentencia->fetch(PDO::FETCH_NUM);
      }
      elseif($tipo=="B"){
        return $sentencia;
      }
      else{
        return $sentencia->fetch(PDO::FETCH_ASSOC);
      }
    } 
    catch (PDOException $e) {      
      return false;
    }  
  }

  //devuelve un registro segun filtro
  public function getRegistro($la_tabla,$el_filtro, $tipo){
    if(!empty($el_filtro)){
        $el_filtro = "WHERE $el_filtro";
    }
    try{
      $sentencia = $this->conectarBaseDeDatos->prepare("SELECT * from $la_tabla $el_filtro");
      $sentencia->execute();
      if($tipo=="O"){
        return $sentencia->fetch(PDO::FETCH_OBJ);
      }
      elseif($tipo=="N"){
        return $sentencia->fetch(PDO::FETCH_NUM);
      }
      elseif($tipo=="B"){
        return $sentencia;
      }
      else{
        return $sentencia->fetch(PDO::FETCH_ASSOC);
      }
    } 
    catch (PDOException $e) {      
      return false;
    }  
  }

  //devuelve un registro según ID pero sólo la columna especificada por parámetro
  public function getRegistroColumna($la_tabla,$la_columna,$el_id,$tipo){
    if(!empty($el_id)) {
      $id = " id".$la_tabla." = ".$el_id;
    }
    try{
      $sentencia = $this->conectarBaseDeDatos->prepare("SELECT $la_columna FROM $la_tabla WHERE $id");
      $sentencia->execute();

      if($tipo=="O"){
        return $sentencia->fetch(PDO::FETCH_OBJ);
      }
      elseif($tipo=="N"){
        return $sentencia->fetch(PDO::FETCH_NUM);
      }
      elseif($tipo=="B"){
        return $sentencia;
      }
      else{
        return $sentencia->fetch(PDO::FETCH_ASSOC);
      }
    } 
    catch (PDOException $e) {      
      return false;
    }  
  }

  //devuelve un registro según ID pero por las columnas especificadas por parámetro
    public function getRegistroColumnas($la_tabla,$las_columnas,$el_id,$tipo){
      if(!empty($el_id)) {
        $id = " id".$la_tabla." = ".$el_id;
      }
      if(!empty($las_columnas)) {
        $columnas = implode(",", $las_columnas);
      }
      try{ 
        $sentencia = $this->conectarBaseDeDatos->prepare("SELECT $columnas FROM $la_tabla WHERE $id");
        $sentencia->execute();
    
        if($tipo=="O"){
          return $sentencia->fetch(PDO::FETCH_OBJ);
        }
        elseif($tipo=="N"){
          return $sentencia->fetch(PDO::FETCH_NUM);
        }
        elseif($tipo=="B"){
          return $sentencia;
        }
        else{
          return $sentencia->fetch(PDO::FETCH_ASSOC);
        }
      } 
      catch (PDOException $e) {      
        return false;
      }  
    }

//devuelve registros por las columnas especificadas por parámetro
public function getRegistrosColumnas($la_tabla,$las_columnas,$el_filtro,$tipo){
  if(!empty($las_columnas)) {
    $columnas = implode(",", $las_columnas);
  }
  if(!empty($el_filtro)){
    $el_filtro = "WHERE $el_filtro";
  }
  try{
    $sentencia = $this->conectarBaseDeDatos->prepare("SELECT $columnas FROM $la_tabla $el_filtro");
    $sentencia->execute();  
  
    if($tipo=="O"){
      return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }
    elseif($tipo=="N"){
      return $sentencia->fetchAll(PDO::FETCH_NUM);
    }
    elseif($tipo=="B"){
      return $sentencia;
    }
    else{
      return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }
  } 
  catch (PDOException $e) {      
    return false;
  }  
}


  private function armarINSERT($la_tabla,$losCampos, $losValores){
    $elSQL = "INSERT INTO ".$la_tabla;
    $campo = "(";
    $valores = "(";
    foreach ($losCampos as $key => $value) {
      $campo .= $value.",";  
      $valores .= "?,";  
    }
    $campo .= ") VALUES ";
    $campo = str_replace(",)",")",$campo);  
    $valores .= ")";
    $valores = str_replace(",)",")",$valores);  
    $elSQL .= $campo.$valores;
    return $elSQL;

  }

  public function insertRegistro($la_tabla,$losCampos, $losValores){
    try{
      $elInsertSQL = $this->armarINSERT($la_tabla,$losCampos,$losValores);
      $this->graboCambios($elInsertSQL);    
      $sentencia = $this->conectarBaseDeDatos->prepare($elInsertSQL);
      $sentencia->execute($losValores);
      return $sentencia->errorInfo();
    } catch (PDOException $e) {
      return false;      
    }
  }

  //delete by ID
  public function deleteRegistroID($la_tabla,$el_id){
    $this->graboCambios("DELETE from $la_tabla WHERE id$la_tabla = $el_id");    
    try{
      $sentencia = $this->conectarBaseDeDatos->prepare("DELETE from $la_tabla WHERE id = $el_id");
      $sentencia->execute();
    } 
    catch (PDOException $e) {      
      return false;
    }  
  }

  //delete by FILTRO
  public function deleteRegistroFiltro($la_tabla,$el_filtro){
    $this->graboCambios("DELETE from $la_tabla WHERE $el_filtro");
    try{   
      $sentencia = $this->conectarBaseDeDatos->prepare("DELETE from $la_tabla WHERE $el_filtro");
      $sentencia->execute();
    } 
    catch (PDOException $e) {      
      return false;
    }  
  }

  //sql Varios
  public function sqlVarios($el_sql){
    if(strpos(strtoupper($el_sql),"SELECT")===false){
      $this->graboCambios($el_sql);
    }
    try{ 
      $sentencia = $this->conectarBaseDeDatos->prepare($el_sql);
      $sentencia->execute();
      return $this->conectarBaseDeDatos->lastInsertId();
    } catch (PDOException $e) {
      return false;
    }

  }

  //sql Varios con return
  public function sqlVariosReturn($el_sql, $tipo){
    if(strpos(strtoupper($el_sql),"SELECT")===false){
      //$this->graboCambios($el_sql);
    }
    try{ 
      $sentencia = $this->conectarBaseDeDatos->prepare($el_sql);
      $sentencia->execute();
      if($tipo=="O"){
        $algo = $sentencia->errorInfo();
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
      }
      elseif($tipo=="N"){
        return $sentencia->fetchAll(PDO::FETCH_NUM);
      }
      elseif($tipo=="B"){
        return $sentencia;
      }
      else{
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
      }    
    } catch (PDOException $e) {      
      return false;
    }
    
  }

  public function graboCambios($el_sql){
    $levelUser = 9;
    $cambios_sincomillas =str_replace("'"," ",utf8_decode($el_sql));
	  $cambios_base = "INSERT INTO cambios (cambios, operador) values ('".$cambios_sincomillas."','$levelUser')";
    try{
      $sentencia = $this->conectarBaseDeDatos->prepare($cambios_base);
      $sentencia->execute();
    } 
    catch (PDOException $e) {      
      return false;
    }   
  }

  

}
?>