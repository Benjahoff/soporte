<?php

require_once 'authController.php';

class crudController extends authController
{

    private $table;
    private $ID;

    public function __construct()
    {
        parent::__construct();
        //$this->isAuthenticated();
        $this->table = null;
        if (isset($this->data->table)) {
            $this->table = $this->data->table;
            // $this->verifyTable($this->table);
        }
        $this->ID = -1;
        if (isset($this->data->id)) {
            $this->ID = $this->data->id;
        }
    }

    /**
     * Devuelve un json con formato especial para
     * armar formulario en front-end.
     */
    public function buildTableForm()
    {
        $data = $this->dataSchema($this->table);
        if ($data !== false) {
            //remove first element "id"
            array_shift($data);
            $this->getForm($data);
        } else {
            $this->response->data = "Error";
            $this->returnData($this->response, 400);
        }
    }

    public function getDataTable()
    {
        $table = $this->table;
        if ($this->table == "salidas") {
            $fields_table = [];
            $losCampos = ['id', 'titulo', 'fecha_salida', 'fecha_regreso', 'micros_id'];
            $losTipos = ['INT', 'VAR_STRING', 'DATE', 'DATE', 'INT'];
            for ($i = 0; $i < COUNT($losCampos); $i++) {
                $field = new stdClass();
                $field->COLUMN_NAME = $losCampos[$i];
                $field->TYPE = $losTipos[$i];
                array_push($fields_table, $field);
            }
        } else if ($this->table == "reservas") {
            $fields_table = [];
            $losCampos = ['id', 'salida', 'agencia', 'fecha_realizada', 'pasajero', 'dni'];
            $losTipos = ['INT', 'VAR_STRING', 'VAR_STRING', 'DATE', 'VAR_STRING', 'VAR_STRING'];
            for ($i = 0; $i < COUNT($losCampos); $i++) {
                $field = new stdClass();
                $field->COLUMN_NAME = $losCampos[$i];
                $field->DATA_TYPE = $losTipos[$i];
                array_push($fields_table, $field);
            }
        } else if (($this->table == "venta") || ($this->table == "compra")) {
            $table = 'operacion';
            $fields_table = $this->dataSchema($table);
        } else {
            $fields_table = $this->dataSchema($table);
        }
        if ($fields_table !== false) {
            //remove first element "id"
            $columns = array();
            // botones dataTable
            $botones = $this->armar_botones($this->table, 0);
            $botones_cabecera = $this->armar_botones($this->table, 1);

            foreach ($fields_table as $value) {
                //$dataTemp = new stdClass();
                $dataTemp = $value->COLUMN_NAME;
                array_push($columns, $dataTemp);
            }
            //
            $sql = "SELECT COUNT(*) as cantidad FROM $table";
            $this->response->total = $this->modularModel->sqlVariosReturn($sql, 'O')[0]->cantidad;
            $this->response->columns = $columns;
            $this->response->buttons = $botones;
            $this->response->buttons_header = $botones_cabecera;
            $this->returnData($this->response, 200);
        } else {
            $this->response->data = "Error";
            $this->returnData($this->response, 400);
        }
    }

    private function getForm($data)
    {
        $this->response->form = array();

        if ($this->ID > 0) {
            $valores =  $this->modularModel->getRegistroID($this->table, $this->ID, "A");
        }
        foreach ($data as $value) {
            $validations = new stdClass();
            //$value->COLUMN_NAME = ucwords($value->COLUMN_NAME);
            $elTipo = $this->dataType($value->DATA_TYPE);
            if ($elTipo != "hide") {

                $form = new stdClass();
                $form->label = str_replace("_", " ", ucwords($value->COLUMN_NAME));
                $form->inputType = $elTipo;
                $form->name =  $value->COLUMN_NAME;
                $form->length =  $value->CHARACTER_MAXIMUM_LENGTH;
                $form->type = "input";
                if ($this->ID > 0) {
                    $form->value = $valores[$value->COLUMN_NAME];
                }
                $form->validations = array();
                if ($form->inputType == 'checkbox') {
                    $form->type = "checkbox";
                }
                if (strpos($form->name, "cuit") !== false) {
                    $form->length = 11;
                    /* $validations->name = "cuit";
                    $validations->validator= "";
                    $validations->message= "Cuit inválido"; */
                }
                if (strpos($form->name, "mail") !== false) {
                    $form->inputType = "email";
                    $validations->name = "mail";
                    $validations->validator = "";
                    $validations->message = "Email inválido";
                }
                if (strpos($form->name, "password") !== false) {
                    $form->inputType = "password";
                }
                if ($form->inputType == "date") {
                    $form->inputType = "date";
                }

                if ($form->inputType == "datetime") {
                    $form->inputType = "datetime-local";
                }
                if ($value->IS_NULLABLE == "NO") {
                    $validations->name = "required";
                    $validations->validator = "";
                    $validations->message = $form->label . " requerido";
                }
                $form->options = array();
                // tabla secundaria
                if (strpos($value->COLUMN_NAME, "_id") !== false) {
                    $table_temp = strtolower(str_replace("_id", "", $value->COLUMN_NAME));
                    $data_temp = $this->dataSelect($table_temp, -1);
                    if (COUNT($data_temp) > 0) {
                        $form->type = "select";
                        $form->label = ucwords($table_temp);
                        $form->name = strtolower($value->COLUMN_NAME);
                        $form->value = null;
                        $form->addButton = $this->verifyTableButton($table_temp);
                        $valorSeleccionadoID = -1;
                        if ($this->ID > 0) {
                            $valorSeleccionadoID = $valores[$value->COLUMN_NAME];
                            if ($valorSeleccionadoID !== null) {
                                $form->value = $valorSeleccionadoID;
                                $valorSeleccionado = $this->dataSelect($table_temp, $valorSeleccionadoID);
                                $options = new stdClass();
                                $options->name = $valorSeleccionado[0]->name;
                                $options->value = $valorSeleccionadoID;
                                $options->selected = false;
                                array_push($form->options, $options);
                            }
                        }
                        $validations->name = "required";
                        $validations->validator = "";
                        $validations->message = $form->label . " requerido";
                        foreach ($data_temp as $valueSec) {
                            if ($valorSeleccionadoID != $valueSec->id) {
                                $options = new stdClass();
                                $options->name = $valueSec->name;
                                $options->value = $valueSec->id;
                                $options->selected = false;
                                array_push($form->options, $options);
                            }
                        }
                    }
                }
                // tabla secundaria
                if (strpos($value->COLUMN_NAME, "SN_") !== false) {
                    $table_temp = strtolower(str_replace("SN_", "", $value->COLUMN_NAME));
                    $data_temp = $this->dataSelect('opcion', -1);
                    if (COUNT($data_temp) > 0) {
                        $form->type = "select";
                        $form->label = ucwords($table_temp);
                        $form->name = strtolower($value->COLUMN_NAME);
                        $form->value = null;
                        $form->addButton = false;
                        $valorSeleccionadoID = -1;
                        if ($this->ID > 0) {
                            $valorSeleccionadoID = $valores[$value->COLUMN_NAME];
                            if ($valorSeleccionadoID !== null) {
                                $form->value = $valorSeleccionadoID;
                                $valorSeleccionado = $this->dataSelect($table_temp, $valorSeleccionadoID);
                                $options = new stdClass();
                                $options->name = $valorSeleccionado[0]->opcion;
                                $options->value = $valorSeleccionadoID;
                                $options->selected = false;
                                array_push($form->options, $options);
                            }
                        }
                        $validations->name = "required";
                        $validations->validator = "";
                        $validations->message = $form->label . " requerido";
                        foreach ($data_temp as $value) {
                            if ($valorSeleccionadoID != $value->id) {
                                $options = new stdClass();
                                $options->name = $value->name;
                                $options->value = $value->id;
                                $options->selected = false;
                                array_push($form->options, $options);
                            }
                        }
                    }
                }
                //
                array_push($form->validations, $validations);
                array_push($this->response->form, $form);
            }
        }
        $button = new stdClass();
        $button->type = "button";
        if ($this->ID > 0) {
            $button->label = "Modificar";
        } else {
            $button->label = "Confirmar";
        }
        $button->validations = array();
        array_push($this->response->form, $button);
        $id = new stdClass();
        $id->id = $this->ID;
        $this->response->data = $id;
        $this->returnData($this->response, 200);
    }

    protected function dataSchema($table)
    {
        $base = BASE;
        $sql = "SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,IS_NULLABLE 
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = '" . $base . "' AND TABLE_NAME = '$table'";
        return $this->modularModel->sqlVariosReturn($sql, 'O');
    }

    private function dataSelect($table, $idSelected)
    {
        if ($table == "nivelesboton") {
            $sql = "SELECT id, nombre as name FROM $table WHERE seccion_id <= 1 AND nivelesboton_id = 1 AND titulo = 0";
            return $this->modularModel->sqlVariosReturn($sql, 'O');
        } else {
            $fields_table = $this->dataSchema($table);
            //busco primer campo varchar
            $field_name = "";
            $where = "";
            if ($idSelected != -1) {
                $where = "WHERE id = $idSelected";
            }
            foreach ($fields_table as $value) {
                if ($value->DATA_TYPE == "varchar") {
                    $field_name = $value->COLUMN_NAME;
                    $sql = "SELECT id, $field_name as name FROM $table $where ORDER BY $field_name";
                    return $this->modularModel->sqlVariosReturn($sql, 'O');
                }
            }
        }
        return array();
    }

    private function dataType($type): string
    {
        $formType = '';
        switch ($type) {
            case 'bigint':
            case 'decimal':
            case 'int':
            case 'smallint':
                $formType = 'number';
                break;
            case 'tinyint':
                $formType = 'checkbox';
                break;
            case 'varchar':
            case 'longtext':
            case 'text':
            case 'char':
            case 'mediumtext':
                $formType = 'text';
                break;
            case 'datetime':
                $formType = 'hide';
                break;
            case 'date':
                $formType = 'date';
                break;
            default:
                $formType = 'UNDEFINED';
                break;
        }

        return $formType;
    }

    private function getInsertSql()
    {
        $table = $this->table;
        $fields_table = $this->dataSchema($table);
        if ($fields_table !== false) {
            $field_name = "";
            $sqlInsert = "Insert into $table (";
            $sqlValores = " VALUES (";
            foreach ($this->data->form as $key => $value) {
                $sqlInsert .= $key . ",";
                if ($key == "password") {
                    if (strlen($value) < 15) {
                        $value = password_hash($value, PASSWORD_DEFAULT);
                    }
                }
                if ($key == "nivel") {
                    $userData = $this->getData($this->getToken());
                    $levelUser = $userData->level;
                    if ($value > $levelUser) {
                        $value = $levelUser;
                    }
                }
                if (strpos($key, "_multiple") !== false) {
                    $value = implode(",",$value);
                }
                $sqlValores .= "'$value',";
            }
            $sqlTotal = $sqlInsert .= ")" . $sqlValores .= ")";
            $sqlTotal = str_replace(",)", ")", $sqlTotal);
            return $sqlTotal;
        } else {
            $this->response->data = "Error";
            $this->returnData($this->response, 400);
        }
    }

    public function deleteRegister($param = [])
    {

        $table = $param[":table"];
        $this->verifyTable($table);
        if ($table == "salidas") {
            $this->deleteSalida($param);
        } else {

            $id = $param[":id"];
            if ($id > 0) {
                $resp = $this->modularModel->deleteRegistroID($table, $id);
                if ($resp !== false) {
                    $this->response->data = $id;
                    $this->returnData($this->response, 200);
                } else {
                    $this->response->data = "Error";
                    $this->returnData($this->response, 400);
                }
            } else {
                $this->response->data = "Error delete";
                $this->returnData($this->response, 400);
            }
        }
    }

    public function updateRegister()
    {
        $id = $this->data->id;
        if ($id > 0) {
            $sql = $this->getUpdateSql($id);
            $resp = $this->modularModel->sqlVarios($sql);
            if ($resp !== false) {
                $this->response->data = $id;
                $this->returnData($this->response, 200);
            } else {
                $this->response->data = "Error update";
                $this->returnData($this->response, 400);
            }
        } else {
            $this->response->data = "Error update";
            $this->returnData($this->response, 400);
        }
        //print_r($data);
    }

    private function getUpdateSql($id)
    {
        $table = $this->table;
        $fields_table = $this->dataSchema($table);
        if ($fields_table !== false) {
            $field_name = "";
            $sqlUpdate = "UPDATE $table SET ";
            foreach ($this->data->form as $key => $value) {
                if ($key != "id") {
                    if ($key == "password") {
                        if (strlen($value) < 15) {
                            $value = password_hash($value, PASSWORD_DEFAULT);
                        }
                    }
                    if ($key == "nivel") {
                        $userData = $this->getData($this->getToken());
                        $levelUser = $userData->level;
                        if ($value > $levelUser) {
                            $value = $levelUser;
                        }
                    }
                    $sqlUpdate .= "$key = '$value',";
                }
            }
            $sqlWHERE = " WHERE id = $id";
            $sqlTotal = $sqlUpdate .= ")" . $sqlWHERE;
            $sqlTotal = str_replace(",)", "", $sqlTotal);
            return $sqlTotal;
        } else {
            return false;
        }
    }

    public function armarConsulta($la_tabla, $el_filtro, $order, $limit, $columns)
    {
        $tabla = $la_tabla;
        if ($columns == null) {
            $strConsulta_tabla = "Select * from " . $tabla . " LIMIT 0,1";
            $result =  $this->modularModel->sqlVariosReturn($strConsulta_tabla, "B");
            $n = $result->columnCount();
        } else {
            $n = Count($columns);
        }
        $seleccion = "";
        $externas = "";
        $subselec = "";
        for ($i = 0; $i < $n; $i++) {
            if ($columns == null) {
                $tmp = $result->getColumnMeta($i);
                $campo = $tmp["name"];
                $tipo = $tmp["native_type"];
            } else {
                $campo = $columns[$i]['db'];
                $tipo = $columns[$i]['type'];
            }
            $union = "=";
            if ($tipo == "VAR_STRING") {
                $tipo = "STRING_TYPE";
                $union = "%";
            } else if ($tipo == "BLOB") {
                $tipo = "STRING_TYPE";
                $union = "%";
            } else if ($tipo == "INT") {
                $tipo = "NUMERIC_TYPE";
                $union = "=";
            } else if ($tipo == "LONG") {
                $tipo = "NUMERIC_TYPE";
                $union = "=";
            } else if ($tipo == "DOUBLE") {
                $tipo = "DOUBLE_TYPE";
                $union = "=";
            } else if ($tipo == "DATETIME") {
                $tipo = "DATE_TYPE";
                $union = "=";
            } else if ($tipo == "DATE") {
                $campo = "DATE_FORMAT($campo, '%d-%m-%Y') as $campo";
                $tipo = "DATE_TYPE";
                $union = "=";
            }
            if (strpos($campo, "_id") !== false) {
                $campo2 = "id";
                $tabla2 = str_replace("_id", "", $campo);
                if ($tabla2 == "origenes") {
                    $tabla2 = "destinos";
                }
                if (strpos($tabla2, "_") > 0) {
                    $tabla2 = strtolower(substr($tabla2, 0, strpos($tabla2, "_")));
                    // $campo2= "id".substr($campo,7,strlen($tabla2));
                    $campo2 = "id";
                }
                $fields_table = $this->dataSchema($tabla2);
                $campo3 = "descripcion";
                foreach ($fields_table as $value) {
                    if ($value->DATA_TYPE == "varchar") {
                        $campo3 = $value->COLUMN_NAME;
                        break;
                    }
                }
                $tabla2as = $tabla2 . "as";
                if (strpos($campo, "origenes") !== false) {
                    $tabla2as = "origenesas";
                }
                $seleccion = $seleccion . $tabla2as . "." . $campo3 . " AS " . $campo;
                $externas = $externas . " LEFT JOIN " . $tabla2 . " " . $tabla2as . " ON " . $tabla . "." . $campo . " = " . $tabla2as . "." . $campo2;
            } else if (strtoupper(substr($campo, 0, 3)) == "SN_") {
                $campo2 = "id";
                $campo3 = substr($campo, 3);
                $tabla2 = "subalias" . $i;
                $subselec = $subselec . ", (SELECT * FROM opcion) as " . $tabla2;
                $seleccion = $seleccion . $tabla2 . ".Opcion AS " . $campo;
                $externas = $externas . " LEFT JOIN (Select * from opcion) as " . $tabla2 . " ON " . $tabla . "." . $campo . " = " . $tabla2 . "." . $campo2;
            } else {
                $seleccion = $seleccion . $tabla . "." . $campo;
            }
            if ($i < $n - 1) {
                $seleccion = $seleccion . ", ";
            }
        }
        $query_rs_tabla1 = sprintf("SELECT %s FROM $tabla %s %s %s %s", $seleccion, $externas, $el_filtro, $order, $limit);
        $query_rs_tabla1 = str_replace($tabla . ".DATE_", "DATE_", $query_rs_tabla1);
        return $query_rs_tabla1;
    }

    public function armarConsultaCount($la_tabla, $el_filtro, $primaryKey, $columns)
    {
        $tabla = $la_tabla;
        $strConsulta_tabla = "SELECT * FROM " . $tabla . " LIMIT 0,1";
        //$result =  $this->modularModel->sqlVariosReturn($strConsulta_tabla, "B");

        //$n = $result->columnCount();
        $n = Count($columns);
        $seleccion = "";
        $externas = "";
        $subselec = "";
        for ($i = 0; $i < $n; $i++) {
            //$tmp = $result->getColumnMeta($i);      
            //$campo = $tmp["name"];
            //$tipo = $tmp["native_type"];
            $campo = $columns[$i]['db'];
            $tipo = $columns[$i]['type'];
            if (strpos($campo, "_id") !== false) {
                $campo2 = "id";
                $tabla2 = str_replace("_id", "", $campo);
                if ($tabla2 == "origenes") {
                    $tabla2 = "destinos";
                }
                if (strpos($tabla2, "_") > 0) {
                    $tabla2 = strtolower(substr($tabla2, 0, strpos($tabla2, "_")));
                    $campo2 = "id";
                }
                $fields_table = $this->dataSchema($tabla2);
                foreach ($fields_table as $value) {
                    if ($value->DATA_TYPE == "varchar") {
                        $campo3 = $value->COLUMN_NAME;
                        break;
                    }
                }
                $tabla2as = $tabla2 . "as";
                if (strpos($campo, "origenes") !== false) {
                    $tabla2as = "origenesas";
                }
                $externas = $externas . " LEFT JOIN " . $tabla2 . " " . $tabla2as . " ON " . $tabla . "." . $campo . " = " . $tabla2as . "." . $campo2;
            } else {
                $seleccion = $seleccion . $tabla . "." . $campo;
            }
            if ($i < $n - 1) {
                $seleccion = $seleccion . ", ";
            }
        }
        $query_rs_tabla1 = "SELECT COUNT($tabla.$primaryKey) as cant FROM $tabla $externas $el_filtro";
        //echo $query_rs_tabla1;
        return $query_rs_tabla1;
    }

    public function reportBug()
    {
        $seccion = $this->data->report->section;
        $detalle = $this->data->report->detail;

        if (($seccion == '') || ($detalle == '')) {
            $this->response->status = 'error';
            $this->returnData($this->response, 400);
        }
        $user = $this->getData($this->getToken())->id;
        $sql = "INSERT INTO bugs (seccion,descripcion,user_id,estado) VALUES('$seccion','$detalle',$user,1)";
        $status = $this->modularModel->sqlVarios($sql);
        if ($status === false) {
            $this->response->status = 'error';
            $this->returnData($this->response, 400);
        }
        $this->response->status = 'success';
        $this->returnData($this->response, 200);
    }


    protected function formOptions($valor, $opciones, $validations = false)
    {
        $form = new stdClass();
        $form->type = "select";
        $form->label = ucwords($valor);
        $form->name = strtolower($valor);
        $form->value = null;
        $form->options = array();
        $valorSeleccionadoID = -1;
        $selOp = false;
        if (COUNT($opciones) == 1) {
            $form->value = $opciones[0]->id;
        }
        foreach ($opciones as $value) {
            if ($valorSeleccionadoID != $value->id) {
                $options = new stdClass();
                $options->name = $value->detalle;
                $options->value = $value->id;
                $options->selected = false;
                array_push($form->options, $options);
            }
        }
        $form->validations = array();
        /* if($validations){
            $validations = new stdClass();
            $validations->name = "required";
            $validations->validator= "";
            $validations->message= $form->label." requerido";
            array_push($form->validations,$validations);
        } */
        return $form;
    }

    protected function formInput($valor, $tipo)
    {

        $form = new stdClass();
        //$valor = "Fecha salida";
        $form->type = "input";
        $form->inputType = $tipo;
        $form->label = str_replace("_", " ", ucwords($valor));
        $form->name =  $valor;
        $form->validations = array();
        return $form;
    }

    public function management()
    {

        $this->response->form = array();
        //select tablas
        $userData = $this->getData($this->getToken());

        $sql = "SELECT id, nombre as detalle FROM tablaaux  WHERE nivel <= {$userData->level} ORDER BY nombre";


        $tablas = $this->modularModel->sqlVariosReturn($sql, 'O');
        $options = $this->formOptions('tablas', $tablas, true);

        array_push($this->response->form, $options);
        // buttons    
        $button = new stdClass();
        $button->type = "button";
        $button->label = "Confirmar";
        $button->validations = array();
        array_push($this->response->form, $button);

        $this->returnData($this->response, 200);
    }

    public function addRegister()
    {
        if (!empty($this->data)) {
            $this->modularModel->beginTransaction();
            try {
                $sql = $this->getInsertSql();
                $this->modularModel->sqlVarios($sql);
                $this->modularModel->commitTransaction();
                $this->returnData($this->response, 200);
            } catch (\Throwable $th) {
                $this->modularModel->rollBackTransaction();
                $this->response->data = "Error add";
                $this->returnData($this->response, 400);
            }
        } else {
            $this->response->data = "Error add";
            $this->returnData($this->response, 400);
        }
    }
    
}
