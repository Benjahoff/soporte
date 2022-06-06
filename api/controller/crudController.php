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

    public function addRegister()
    {
        if (!empty($this->data)) {
            $this->modularModel->beginTransaction();
            try {
                $sql = $this->getInsertSql();
                $lastID = $this->modularModel->sqlVarios($sql);

                if ($lastID !== false) {
                    // controles especiales
                    // verifico si es salidas agregar rutas_terminales
                    if ($this->table == "salidas") {
                        $laSalida = $this->modularModel->getRegistroID("salidas", $lastID, "O");
                        if ($laSalida) {
                            $lasRutas = $this->modularModel->getRegistrosTabla("rutas_terminales", "rutas_id = {$laSalida->rutas_id}", "id", "O");
                            if (COUNT($lasRutas) > 0) {
                                foreach ($lasRutas as $key => $rt) {
                                    $sql = "INSERT INTO salidas_terminales (salidas_id, terminales_id) VALUES ('$lastID','{$rt->terminales_id}')";
                                    $this->modularModel->sqlVarios($sql);
                                }
                            }
                        }
                    }
                    // verifico si es caja generar tabla nueva caja
                    if ($this->table == "caja") {
                        $laCaja = $this->modularModel->getRegistroID("caja", $lastID, "O");
                        $nombreCaja = "caja" . $laCaja->descripcion;
                        $nombreCaja = str_replace(" ", "_", $nombreCaja);
                        $sqlCaja = "CREATE TABLE $nombreCaja (
                            id int(11) NOT NULL,
                            descripcion varchar(100) DEFAULT NULL,
                            entrada double(13,2) DEFAULT NULL,
                            salida double(13,2) DEFAULT NULL,
                            forma varchar(50) DEFAULT NULL,
                            detalle varchar(100) NOT NULL,
                            operador varchar(50) DEFAULT NULL,
                            caja int(11) NOT NULL DEFAULT 0,
                            cuenta_id int(11) NOT NULL DEFAULT 1,
                            fecha timestamp NULL DEFAULT current_timestamp(),
                            cotizacion double(10,2) NOT NULL DEFAULT 1.00
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                        ALTER TABLE $nombreCaja ADD PRIMARY KEY (id);
                        ALTER TABLE $nombreCaja MODIFY id int(11) NOT NULL AUTO_INCREMENT;";
                        $laRespuesta = $this->modularModel->sqlVarios($sqlCaja);
                        // genero forma de pago para la nueva caja
                        $sql = "INSERT INTO formapago (descripcion, sn_fe, sn_apertura, sn_interna, monedas_id, caja_id, comision, dias_acreditacion) VALUES ('Efectivo {$laCaja->descripcion}', '1', '1', '1', '{$laCaja->monedas_id}', '{$laCaja->id}','0','0');";
                        $laRespuesta = $this->modularModel->sqlVarios($sql);
                    }
                }
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

    private function pasajeroObservacion()
    {
    }
    /**
     * Obtener datos de un pasajero gracias a su dni,
     * y ademas validar que el pasajero no se encuentre en otra reserva 
     * de la misma salida
     */

    public function validarDNI()
    {
        if (!empty($this->data->dni)) {
            $dni = $this->data->dni;
            $pasajero = $this->modularModel->getRegistro("pasajeros", "dni LIKE '%$dni%'", "O");

            if ($pasajero) {
                $salidaId = $this->data->salida;

                $sql = "SELECT * FROM reservas_pasajeros rp, reservas r WHERE r.id = rp.reservas_id AND r.salidas_id = $salidaId AND rp.pasajeros_id = " . $pasajero->id;
                $resp = $this->modularModel->sqlVariosReturn($sql, 'O');
                if ($resp) {
                    $this->response->status = "Error. El pasajero esta en otra reserva";
                    $this->returnData($this->response, 400);
                }
                // obtengo atributos pasajero
                $sql = "SELECT atributos_id FROM atributos_pasajeros WHERE pasajeros_id = " . $pasajero->id;
                $atributos = $this->modularModel->sqlVariosReturn($sql, 'A');
                if ($atributos) {
                    $atributosArr = [];
                    foreach ($atributos as $key) {
                        array_push($atributosArr, $key['atributos_id']);
                    }

                    $pasajero->observations = $atributosArr;
                } else {
                    $pasajero->observations = [];
                }
                $edad = $this->edadFecha($pasajero->fecha_nacimiento);
                $pasajero->edad = $edad;
            }
            //
            $this->response->pasajero = $pasajero;
            $this->returnData($this->response, 200);
        } else {
            $this->response->data = "Error dni";
            $this->returnData($this->response, 400);
        }
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

    public function cargarAgenciaCliente()
    {

        $agencias = $this->modularModel->getRegistrosTabla("e_agencias", "", "", "O");
        foreach ($agencias as $key => $agencia) {
            $sql = "UPDATE clientes SET usuario = '" . $agencia->usuario . "', password = '" . $agencia->password . "' WHERE id = " . $agencia->clientes_id;
            $this->modularModel->sqlVarios($sql);
        }
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

    public function borrarSalidas()
    {
        $lasSalidas = $this->modularModel->getRegistrosTabla("salidas", "fecha_salida < '2021-01-01'", "id LIMIT 1000", "O");
        foreach ($lasSalidas as $key => $salida) {
            try {
                $sql = "SELECT id FROM reservas WHERE salidas_id = {$salida->id}";
                $lasReservas = $this->modularModel->sqlVariosReturn($sql, "A");
                $texto = implode(",", $lasReservas);
                if ($lasReservas) {
                    $this->modularModel->beginTransaction();
                    try {
                        $sql = "DELETE FROM reservas_pasajeros WHERE reservas_id IN (SELECT id FROM reservas WHERE salidas_id = {$salida->id})";
                        $this->modularModel->sqlVarios($sql);
                        // borro reservas_detalles
                        $sql = "DELETE FROM reservas_detalles WHERE reservas_id IN (SELECT id FROM reservas WHERE salidas_id = {$salida->id})";
                        $this->modularModel->sqlVarios($sql);
                        // borro las reservas
                        $sql = "DELETE FROM reservas WHERE salidas_id = {$salida->id})";
                        $this->modularModel->sqlVarios($sql);
                        // borro las butacas_salidas
                        $sql = "DELETE FROM butacas_salidas WHERE salidas_id = {$salida->id}";
                        $this->modularModel->sqlVarios($sql);
                        $this->modularModel->commitTransaction();
                    } catch (\Throwable $th) {
                        $this->modularModel->rollBackTransaction();
                    }
                }
            } catch (\Throwable $e) {
                $this->modularModel->rollBackTransaction();
            }
            $param[":id"] = $salida->id;
            $this->deleteSalida2($param);
        }
        print_r("Fin Proceso");
    }

    protected function eliminarReserva($reservaId)
    {

        $this->modularModel->beginTransaction();
        try {
            $this->liberoStockReservas($reservaId);
            $sql = "DELETE FROM reservas_pasajeros WHERE reservas_id = {$reservaId}";
            $this->modularModel->sqlVarios($sql);
            $sql = "DELETE FROM reservas_detalles WHERE reservas_id = {$reservaId}";
            $this->modularModel->sqlVarios($sql);
            $sql = "DELETE FROM butacas_salidas WHERE reservas_id = {$reservaId}";
            $this->modularModel->sqlVarios($sql);
            $sql = "DELETE FROM reservas_notas WHERE reservas_id = {$reservaId}";
            $this->modularModel->sqlVarios($sql);
            $sql = "DELETE FROM movimientos_pagos WHERE reservas_id = {$reservaId}";
            $this->modularModel->sqlVarios($sql);
            $sql = "DELETE FROM reservas WHERE id = {$reservaId}";
            $this->modularModel->sqlVarios($sql);
            $this->modularModel->commitTransaction();
            return true;
        } catch (Exception $e) {
            $this->modularModel->rollBackTransaction();
            return false;
        }
    }

    public function liberoStockReservas($reservaId){

        $sql = "SELECT * FROM reservas_detalles WHERE reservas_id = $reservaId AND serviciosId > 0 AND tipostock_id > 0 GROUP BY detalle";                      
        $habitaciones = $this->modularModel->sqlVariosReturn($sql,"O");
        if($habitaciones){
            foreach ($habitaciones as $habitacion) {
                $sql = "UPDATE precios SET ocupado = ocupado - 1 WHERE servicios_id = {$habitacion->serviciosId} AND tipostock_id = {$habitacion->tipostock_id}";
                $this->modularModel->sqlVarios($sql);                                                                
            }                               
        }
        return;
    }

    // eliminar salida y registros relacionados
    function deleteSalida2($param = [])
    {
        $salidaId = $param[":id"];
        //
        $laSalida = $this->modularModel->getRegistroID("salidas", $salidaId, "O");
        if ($laSalida) {
            $this->modularModel->beginTransaction();
            try {

                // obtengo las salidas_servicios
                $salidasServicios = $this->modularModel->getRegistrosTabla("salidas_servicios", "salidas_id = $salidaId", "", "O");
                foreach ($salidasServicios as $key => $ss) {
                    $sql = "SELECT * FROM precios_salidas_servicios WHERE salidas_Servicios_id = $ss->id";
                    $precio_salidas_servicios = $this->modularModel->sqlVariosReturn($sql, "O");
                    foreach ($precio_salidas_servicios as $key => $pss) {
                        // borro los precios
                        $sql = "DELETE FROM precios WHERE id = {$pss->precios_id}";
                        $this->modularModel->sqlVarios($sql);
                    }
                    // borro las precios_salidas_servicios            
                    $sql = "DELETE FROM precios_salidas_servicios WHERE salidas_Servicios_id = $ss->id";
                    $this->modularModel->sqlVarios($sql);
                }
                // borro las salidas_servicios            
                $sql = "DELETE FROM salidas_servicios WHERE salidas_id = $salidaId";
                $this->modularModel->sqlVarios($sql);
                // borro los destinos                    
                $sql = "DELETE FROM salidas_destinos WHERE salidas_id = $salidaId";
                $this->modularModel->sqlVarios($sql);
                // borro los deudores                    
                $sql = "DELETE FROM deudores WHERE salidas_id = $salidaId";
                $this->modularModel->sqlVarios($sql);
                // borro las terminales                    
                $sql = "DELETE FROM salidas_terminales WHERE salidas_id = $salidaId";
                $this->modularModel->sqlVarios($sql);
                // borro los grupales                    
                $sql = "DELETE FROM salidas_grupales WHERE salidas_id = $salidaId";
                $this->modularModel->sqlVarios($sql);
                // borro precios
                $lasSalidaPrecios = $this->modularModel->getRegistrosTabla("salidas_precios", "salidas_id = $salidaId", "", "O");
                foreach ($lasSalidaPrecios as $key => $sp) {
                    // borro servicios_salidas_precios
                    $sql = "DELETE FROM servicios_salidas_precios WHERE salidas_precios_id = {$sp->id}";
                    $this->modularModel->sqlVarios($sql);
                    // borro descuentos_salidas_precios
                    $sql = "DELETE FROM descuentos_salidasprecios WHERE salidasprecios_id = {$sp->id}";
                    $this->modularModel->sqlVarios($sql);
                }
                //borro salidas_precios
                $sql = "DELETE FROM salidas_precios WHERE salidas_id = $salidaId";
                $this->modularModel->sqlVarios($sql);
                // borro la salida                     
                $this->modularModel->deleteRegistroID("salidas", $salidaId);
                //                                         
                $this->modularModel->commitTransaction();
            } catch (\Throwable $e) {
                $this->modularModel->rollBackTransaction();
            }
        }
        return;
    }

    // eliminar salida y registros relacionados
    function deleteSalida($param = [])
    {
        $salidaId = $param[":id"];
        //
        $laSalida = $this->modularModel->getRegistroID("salidas", $salidaId, "O");
        if ($laSalida) {
            $lasReservas = $this->modularModel->getRegistrosTabla("reservas", "salidas_id = $salidaId", "", "O");
            if (!$lasReservas) {
                $this->modularModel->beginTransaction();
                // obtengo las salidas_servicios
                try {
                    $salidasServicios = $this->modularModel->getRegistrosTabla("salidas_servicios", "salidas_id = $salidaId", "", "O");
                    if($salidasServicios){
                        foreach ($salidasServicios as $key => $ss) {
                            $sql = "SELECT * FROM precios_salidas_servicios WHERE salidas_Servicios_id = $ss->id";                        
                            $precio_salidas_servicios = $this->modularModel->sqlVariosReturn($sql, "O");
                            if($precio_salidas_servicios){
                                foreach ($precio_salidas_servicios as $key => $pss) {
                                    // borro los precios
                                    $sql = "DELETE FROM precios WHERE id = {$pss->precios_id}";
                                    $this->modularModel->sqlVarios($sql);
                                }
                            }
                            // borro las precios_salidas_servicios
                            $sql = "DELETE FROM precios_salidas_servicios WHERE salidas_Servicios_id = $ss->id";
                            $this->modularModel->sqlVarios($sql);
                        }
                    }
                    // borro las salidas_servicios
                    $sql = "DELETE FROM salidas_servicios WHERE salidas_id = $salidaId";
                    $this->modularModel->sqlVarios($sql);
                    // borro los destinos
                    $sql = "DELETE FROM salidas_destinos WHERE salidas_id = $salidaId";
                    $this->modularModel->sqlVarios($sql);
                    // borro las terminales
                    $sql = "DELETE FROM salidas_terminales WHERE salidas_id = $salidaId";
                    $this->modularModel->sqlVarios($sql);
                    // borro los grupales
                    $sql = "DELETE FROM salidas_grupales WHERE salidas_id = $salidaId";
                    $this->modularModel->sqlVarios($sql);
                    // borro plan viaje renglon
                    $sql = "DELETE FROM planviajedia WHERE planviaje_id IN (Select id FROM planviaje WHERE salidas_id = $salidaId)";
                    $this->modularModel->sqlVarios($sql);
                    // borro plan viaje
                    $sql = "DELETE FROM planviaje WHERE salidas_id = $salidaId";
                    $this->modularModel->sqlVarios($sql);
                    // borro comentarios
                    $sql = "DELETE FROM comentario WHERE salidas_id = $salidaId";
                    $this->modularModel->sqlVarios($sql);                    
                    //borro salidas_precios
                    $sql = "DELETE FROM salidas_precios WHERE salidas_id = $salidaId";
                    $this->modularModel->sqlVarios($sql);
                    // borro la salida
                    $this->modularModel->deleteRegistroID("salidas", $salidaId);
                    $this->modularModel->commitTransaction();
                    $this->returnData('Salida eliminada correctamente.', 200);
                } catch (\Throwable $th) {
                    $this->modularModel->rollBackTransaction();
                    $this->returnData('Error al borrar salida'.$th, 500);
                    //throw $th;
                }
            } else {
                $this->response->status = 'Error salida con reservas';
                $this->returnData($this->response, 400);
            }
        }
        $this->response->status = 'error salida inexistente';
        $this->returnData($this->response, 400);
    }

    protected function validarBloqueoButaca($salidaId)
    {
        $date = date('Y-m-d H:i:s', time());
        $sql = "UPDATE butacas_salidas SET fecha_bloqueo = null, detalle = 'LIBRE' WHERE fecha_bloqueo < '$date' AND detalle LIKE '%bloqueada%'";
        $this->modularModel->sqlVarios($sql);
        $minutosButaca = $this->modularModel->getRegistroId("parametros", 1, 'O')->minutos_butaca;
        $sql = "UPDATE butacas_salidas SET timer = null WHERE DATE_ADD(timer, INTERVAL $minutosButaca MINUTE) < '$date'";
        $this->modularModel->sqlVarios($sql);
        $sql = "DELETE FROM butacas_salidas WHERE (reservas_id IS NULL OR pasajeros_id IS NULL)  AND fecha_bloqueo IS NULL AND timer IS NULL";
        $this->modularModel->sqlVarios($sql);
        $sql = "DELETE FROM reservas WHERE estado = 10 AND fecha_realizada < current_date()";
        $this->modularModel->sqlVarios($sql);
        //$this->validarPromos();
    }

    protected function validarButacasDisponibilidad($tiempo, $backoffice = false)
    {
        if ($backoffice) {
            $seatsArr = "";
            foreach ($this->data->seats as $seat) {
                if ($seatsArr != "") {
                    $seatsArr .= ",";
                }
                if (isset($seat->id)) {
                    $seatsArr .= $seat->id;
                } else {
                    $seatsArr .= $seat;
                }
            }
        } else {
            $seatsArr = "";
            foreach ($this->data->seats as $key => $seat) {
                $seatsArr .= $seat->id . ",";
            }
            $seatsArr .= "/";
            $seatsArr = str_replace(",/", "", $seatsArr);
        }
        $sql = "SELECT * FROM butacas_salidas WHERE salidas_id = {$this->data->salida} AND butacas_id IN ({$seatsArr}) ";
        $dbSeats = $this->modularModel->sqlVariosReturn($sql, 'O');

        if (count($dbSeats) > 0) {
            $this->modularModel->beginTransaction();
            try {
                $date = date('Y-m-d H:i:s', time());
                $seatDate = new DateTime($dbSeats[0]->timer);
                $desde = $seatDate->diff(new DateTime($date));

                $minutes = $desde->days * 24 * 60;
                $minutes += $desde->h * 60;
                $minutes += $desde->i;

                foreach ($dbSeats as $seat) {

                    if (($dbSeats[0]->reservas_id != null) || ($dbSeats[0]->pasajeros_id != null)) {
                        throw new Exception("Butaca ocupada", 1);
                        $this->modularModel->rollBackTransaction();
                    }

                    if ($dbSeats[0]->timer != null) {
                        if ($minutes <  (int) $tiempo) {
                            $this->modularModel->rollBackTransaction();
                            throw new Exception("Error procesando la peticion butacas", 1);
                        }
                    }

                    //Si bloqueo o ocupo desde backoffice no hace falta poner timer
                    if ($backoffice) {
                        $sql = "UPDATE butacas_salidas SET timer = '{$date}'  WHERE id = '{$seat->id}'";

                        if ($this->modularModel->sqlVarios($sql) === false) {
                            $this->modularModel->rollBackTransaction();
                            throw new Exception("Error procesando la peticion butacas", 1);
                        }
                    }
                }
                $this->modularModel->commitTransaction();
            } catch (Exception $e) {
                $this->response = 'Butaca1';
                $this->returnData($this->response, 400);
            }
        }
    }

    public function desocuparRoomGrabar($reservaId, $passId, $servId)
    {
        $pasajero = $this->modularModel->getRegistroID("pasajeros", $passId, "O");
        // desocupar habitacion
        try {
            /* $sql = "DELETE FROM reservas_detalles WHERE pasajeros_id = $passId AND reservas_id = $reservaId AND serviciosId = $servId";
            $this->modularModel->sqlVarios($sql);
            $sql = "UPDATE reservas_detalles SET detalle = Replace(detalle,'{$pasajero->dni}','') WHERE reservas_id = $reservaId AND serviciosId = $servId";
            $this->modularModel->sqlVarios($sql);
            $sql = "UPDATE reservas_detalles SET detalle = Replace(detalle,'//','/') WHERE reservas_id = $reservaId AND serviciosId = $servId";
            $this->modularModel->sqlVarios($sql); */
            $sql = "DELETE FROM reservas_detalles WHERE pasajeros_id = $passId AND serviciosId = $servId";
            $this->modularModel->sqlVarios($sql);
            $sql = "UPDATE reservas_detalles SET detalle = Replace(detalle,'{$pasajero->dni}','') WHERE serviciosId = $servId";
            $this->modularModel->sqlVarios($sql);
            $sql = "UPDATE reservas_detalles SET detalle = Replace(detalle,'//','/') WHERE serviciosId = $servId";
            $this->modularModel->sqlVarios($sql);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getParameters()
    {
        $parametros = $this->modularModel->getRegistroID("parametros", 1, 'O');
        $this->response->parameters = $parametros;
        $this->returnData($this->response, 200);
    }

    public function vaciarBasePrueba()
    {
        $this->modularModel->beginTransaction();
        try {
            $sql =
                "SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE arba;
            TRUNCATE operacion_forma;
            TRUNCATE movimientos_pagos;
            TRUNCATE detalle;
            TRUNCATE operacion;
            TRUNCATE reservas_detalles;
            TRUNCATE butacas_historial;
            TRUNCATE butacas_salidas;
            TRUNCATE reservas_pasajeros;
            TRUNCATE reservas;
            TRUNCATE cajainterna;
            SET FOREIGN_KEY_CHECKS = 1;";
            $this->modularModel->sqlVarios($sql);
            $this->response = 'Tablas vaciadas correctamente.';
            $this->returnData($this->response, 200);
            $this->modularModel->commitTransaction();
        } catch (Exception $e) {
            $this->response = 'Error';
            $this->modularModel->rollBackTransaction();
            $this->returnData($this->response, 400);
        }
    }
}
