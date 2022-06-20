<?php

require_once './model/modelBase.php';

define('HOME', 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/');

class controller
{

    protected $modularModel;
    protected $uri;
    protected $uriApi;

    public function __construct()
    {
        Sentry\init(['dsn' => 'https://cb9931b44a07497eaf334c0e85717f43@o938498.ingest.sentry.io/5888284']);
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $this->modularModel = new modelBase();
<<<<<<< HEAD
        //$this->uri = 'http://localhost/soporte-worksi/';
        //$this->uriApi = 'http://localhost/soporte-worksi/api';
        $this->uri = 'https://wks.ar/soporte/';
        $this->uriApi = 'https://wks.ar/soporte/api/';
=======
        $this->uri = 'http://localhost/worksi-soporte/';
        $this->uriApi = 'http://localhost/worksi-soporte/api';

        // endpoint prod
        //$this->uri = 'https://wks.ar/soporte/';
        //$this->uriApi = 'https://wks.ar/soporte/api/';
>>>>>>> main
    }

    public function returnData($data, $httpCode)
    {
        http_response_code($httpCode);
        print_r(json_encode($data));
        die();
    }

    public function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'WksSoporte123';
        $secret_iv = 'WksSoporte';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    public function formatoFecha($fecha)
    {
        $fechaSeparada = explode("-", $fecha);
        if (checkdate($fechaSeparada[1], $fechaSeparada[2], $fechaSeparada[0])) {
            $date = date_create($fecha);
            return date_format($date, "d/m/Y");
        }
        return "N/A";
    }

    public function edadFecha($fecha)
    {
        $fechaSeparada = explode("-", $fecha);
        if (checkdate($fechaSeparada[1], $fechaSeparada[2], $fechaSeparada[0])) {
            $fecha_nacimiento = new DateTime($fecha);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento);
            return $edad->y;
        }
        return "N/A";
    }

    public function getRoomsReserva($reservaId, $salidaId)
    {
        if ($reservaId) {
            $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, "O");
            $reservas_detalles = $this->modularModel->getRegistrosTabla('reservas_detalles', 'reservas_id = ' . $reservaId, '', "O");
            $sql = "SELECT COUNT(*) as cantidad FROM reservas_pasajeros WHERE reservas_id = $reservaId AND infoa = 0";
            $pasajeros_adultos = $this->modularModel->sqlVariosreturn($sql, "O");
            $sql = "SELECT COUNT(*) as cantidad FROM reservas_pasajeros WHERE reservas_id = $reservaId AND infoa = 1";
            $pasajeros_chicos = $this->modularModel->sqlVariosreturn($sql, "O");
            $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, "O");
        } else {
            $sql = "SELECT * FROM reservas_detalles WHERE reservas_id IN (SELECT id FROM reservas WHERE salidas_id = $salidaId)";
            $reservas_detalles =  $this->modularModel->sqlVariosreturn($sql, "O");
            $sql = "SELECT COUNT(*) as cantidad FROM reservas_pasajeros WHERE reservas_id IN (SELECT id FROM reservas WHERE salidas_id = $salidaId) AND infoa = 0";
            $pasajeros_adultos = $this->modularModel->sqlVariosreturn($sql, "O");
            $sql = "SELECT COUNT(*) as cantidad FROM reservas_pasajeros WHERE reservas_id IN (SELECT id FROM reservas WHERE salidas_id = $salidaId) AND infoa = 1";
            $pasajeros_chicos = $this->modularModel->sqlVariosreturn($sql, "O");
            $tarifa = $this->modularModel->getRegistro("salidas_precios", "salidas_id = $salidaId", "O");
        }
        $salida = $this->modularModel->getRegistroID("salidas", $salidaId, "O");
        $serviciosTarifa = $this->serviciosTarifa($tarifa->id);
        $sql = "SELECT ss.*, s.detalle_servicio FROM salidas_servicios ss, servicios s WHERE s.id = ss.servicios_id AND ss.salidas_id = {$salidaId} AND s.tiposervicios_id = 5 AND ss.id IN ({$serviciosTarifa})";
        $salidaServicios = $this->modularModel->sqlVariosReturn($sql, 'O');
        //$salidaServicios = $this->modularModel->getRegistrosTabla("salidas_servicios", "salidas_id = {$reserva->salidas_id}","fecha_ingreso","O");
        $tiposStock = $this->modularModel->getRegistrosTabla("tipostock", "cantidad_total > 0 AND cantidad_total <= {$pasajeros_adultos[0]->cantidad}", "", "O");
        $servicios = [];
        foreach ($salidaServicios as $key => $servicio) {
            $servicioInd = new stdClass();
            $elServicio = $this->modularModel->getRegistroID("servicios", $servicio->servicios_id, "O");
            $elOperador = $this->modularModel->getRegistroID("operadores", $elServicio->operadores_id, "O");
            $parametros = $this->modularModel->getRegistroID("parametros", 1, "O");
            //$elDestino = $this->modularModel->getRegistroID("destinos", $elServicio->destinos_id,"O");
            if ($elServicio->tiposervicios_id == 5) {
                if ($reservaId == null) {
                    $sql = "SELECT t.detalle FROM reservas_detalles rd, tipostock t WHERE rd.tipostock_id = t.id AND rd.reservas_id IN (SELECT id FROM reservas WHERE salidas_id = $salidaId) AND rd.serviciosId = {$servicio->id} GROUP BY rd.detalle";
                    $tiposHabitacion = $this->modularModel->sqlVariosReturn($sql, "O");
                }
                if ($reservaId) {
                    $sql = "SELECT t.detalle FROM reservas_detalles rd, tipostock t WHERE rd.tipostock_id = t.id AND rd.reservas_id IN (SELECT id FROM reservas WHERE salidas_id = $salidaId) AND rd.serviciosId = {$servicio->id} GROUP BY rd.detalle";
                    $tiposHabitacion = $this->modularModel->sqlVariosReturn($sql, "O");
                    $sql = "SELECT * FROM reservas_detalles WHERE reservas_id = $reservaId AND serviciosId = {$servicio->id} GROUP BY detalle";
                } else {
                    $sql = "SELECT * FROM reservas_detalles WHERE reservas_id IN (SELECT id FROM reservas WHERE salidas_id = $salidaId) AND serviciosId = {$servicio->id} GROUP BY detalle";
                }
                $reservaDetalle = $this->modularModel->sqlVariosReturn($sql, "O");
                $servicioInd->id = $servicio->id;
                $servicioInd->detalle_servicio = $elServicio->detalle_servicio;
                $servicioInd->tipos = $this->serviciosDisponibles($servicio);
                $servicioInd->tiposHabitacion = $tiposHabitacion;
                $stringDNI = "(";
                //$servicio->destino = $elDestino->nombre;
                $rooms = [];
                $totalAdultos = 0;
                $totalInfoa = 0;
                foreach ($reservaDetalle as $key => $habitacion) {
                    $tipoStock = $this->modularModel->getRegistroID("tipostock", $habitacion->tipostock_id, "O");
                    $room = new stdClass();
                    $room->id = $habitacion->tipostock_id;
                    $room->tipo = $habitacion->detalle;
                    $room->regimen = $elServicio->regimen;
                    $room->ingreso = $servicio->fecha_ingreso;
                    $room->egreso = $servicio->fecha_egreso;
                    $room->total = $tipoStock->cantidad_total;
                    $room->descripcion = $tipoStock->detalle;
                    $room->reservaId = $habitacion->reservas_id;

                    // armo pasajeros habitacion
                    $pasajeros = [];
                    $tipoSeparado = explode("##", $habitacion->detalle);
                    $pasDNI = explode("/", $tipoSeparado[1]);

                    foreach ($pasDNI as $key => $dni) {
                        $pas = new stdClass();
                        if (trim($dni) != "") {
                            $pasajero = $this->modularModel->getRegistro("pasajeros", "dni = '" . trim($dni) . "'", "O");
                            if ($pasajero) {
                                // busco reservas_detalle de la habitacion
                                $sql = "SELECT reservas_id FROM reservas_detalles WHERE serviciosId = {$servicio->id} AND pasajeros_id = $pasajero->id";
                                $reservaDelPasajero = $this->modularModel->sqlVariosReturn($sql, "O");
                                //
                                $stringDNI .= $pasajero->id . ",";
                                $pas->id = $pasajero->id;
                                $pas->nombre = $pasajero->apellido . " " . $pasajero->nombre;
                                $pas->dni = $pasajero->apellido . " " . $pasajero->dni;
                                $pas->doc = $pasajero->dni;
                                $edad = $this->edadFecha($pasajero->fecha_nacimiento);
                                $pas->edad = $edad;
                                if ($edad < (int)$parametros->edad_infoa) {
                                    $totalInfoa++;
                                } else {
                                    $totalAdultos++;
                                }
                                $pas->reservaId = $reservaDelPasajero[0]->reservas_id;
                                array_push($pasajeros, $pas);
                            }
                        }
                    }

                    $room->pasajeros = $pasajeros;
                    array_push($rooms, $room);
                }

                $stringDNI .= ")";
                $stringDNI = str_replace(",)", ")", $stringDNI);
                $stringDNI = str_replace("()", "(-1)", $stringDNI);

                // pasajeros sin room
                if ($reservaId) {
                    $sql = "SELECT * FROM reservas_pasajeros WHERE reservas_id = $reservaId AND pasajeros_id NOT IN $stringDNI";
                } else {
                    $sql = "SELECT * FROM reservas_pasajeros WHERE reservas_id IN (SELECT id FROM reservas WHERE salidas_id = $salidaId) AND pasajeros_id NOT IN $stringDNI";
                }
                $reservas_pasajeros_sin_room = $this->modularModel->sqlVariosReturn($sql, "O");
                if ($reservas_pasajeros_sin_room) {
                    foreach ($reservas_pasajeros_sin_room as $key => $sinRoom) {
                        $pasajero = $this->modularModel->getRegistroID("pasajeros", $sinRoom->pasajeros_id, "O");
                        $sinRoom->apellido = $pasajero->apellido;
                        $sinRoom->nombre = $pasajero->nombre;
                    }
                    $servicioInd->pasajerosSinRoom = $reservas_pasajeros_sin_room;
                } else {
                    $servicioInd->pasajerosSinRoom = null;
                }
                //
                $servicioInd->rooms = $rooms;
                $servicioInd->adultos = $totalAdultos;
                $servicioInd->chicos = $totalInfoa;
                array_push($servicios, $servicioInd);
            }
        }
        $servicios[0]->tipoStock = $tiposStock;
        //$servicios[0]->adultos = $pasajeros_adultos[0]->cantidad;
        //$servicios[0]->chicos = $pasajeros_chicos[0]->cantidad;
        return $servicios;
    }

    public function serviciosDisponibles($servicio)
    {
        $servicioId = $servicio->id;
        //traigo los precios de los servicios de la salida
        $desde = date("Y-m-d", strtotime($servicio->fecha_ingreso));
        $hasta = date("Y-m-d", strtotime($servicio->fecha_egreso));
        if($servicio->agrupar_stock == 1){
            $sql = "SELECT IFNULL(SUM(p.stock-p.ocupado),0) as cant FROM precios p WHERE p.servicios_id = $servicioId AND p.fecha_desde = '$desde' AND p.fecha_hasta = '$hasta'";
            $agrupado = $this->modularModel->sqlVariosReturn($sql, 'O');
            $laCantidad = 0;
            if($agrupado){
                $laCantidad = $agrupado[0]->cant;
            }
            //
            $sql = "SELECT p.*, $laCantidad as cant, p.stock as total, t.* FROM precios p, tipostock t WHERE p.tipostock_id = t.id AND servicios_id = $servicioId AND fecha_desde = '$desde' AND fecha_hasta = '$hasta' GROUP BY p.tipostock_id";
        }
        else{
            $sql = "SELECT p.*, IFNULL(SUM(p.stock-p.ocupado),0) as cant, p.stock as total, t.* FROM precios p, tipostock t WHERE p.tipostock_id = t.id AND servicios_id = $servicioId AND fecha_desde = '$desde' AND fecha_hasta = '$hasta' GROUP BY p.tipostock_id";
        }
        $serviciosDisponibles = $this->modularModel->sqlVariosReturn($sql, 'O');
        if(!$serviciosDisponibles){
            $serviciosDisponibles = [];
        }        
        return $serviciosDisponibles;
    }

    public function traerCotizacion($moneda)
    {
        // traigo cotizacion
        $hoy = date("Y-m-d");
        $sql = "SELECT c.*, m.codigo FROM cotizaciones c, monedas m WHERE c.monedas_id = m.id AND c.monedas_id = $moneda AND fecha <= '$hoy' ORDER BY fecha DESC LIMIT 1";
        $cotizacion = $this->modularModel->sqlVariosReturn($sql, 'O');
        if ($cotizacion) {
            $laCotizacion = $cotizacion[0]->importe;
        } else {
            $laCotizacion = 1;
        }
        return $laCotizacion;
    }

    protected function verificarInternacional($salidaId)
    {
        $impuestos = false;
        $sql = "SELECT ss.*, s.*, d.paises_id FROM salidas_servicios ss, servicios s, destinos d WHERE ss.servicios_id = s.id AND s.destinos_id = d.id AND d.paises_id > 1 AND ss.salidas_id = $salidaId";
        $salidasServicios = $this->modularModel->sqlVariosReturn($sql, 'O');
        if ($salidasServicios) {
            $impuestos = true;
        }
        return $impuestos;
    }

    protected function crearReservaSalida()
    {
        $salida = $this->modularModel->getRegistroID("salidas", $this->data->salida, 'O');
        $salidaId = $salida->id;
        $desde = $salida->fecha_salida;
        $hasta = $salida->fecha_regreso;
        $estado = 10;
        //parametro clientes_id
        $clienteID = $this->data->cliente;
        $tarifa = $this->data->tarifa;
        $laTarifa = $tarifa->id;
        $this->modularModel->beginTransaction();
        try {
            $sql = "INSERT INTO reservas (salidas_id, clientes_id, detalle, desde, hasta, estado, fecha_realizada, tarifaId) VALUES ('" . $salidaId . "','$clienteID','RESERVA EFECTUADA EN LA WEB.','$desde','$hasta','$estado',CURRENT_DATE(), $laTarifa)";
            $reservaId = $this->modularModel->sqlVarios($sql, 'O');
            $this->modularModel->commitTransaction();
            return $reservaId;
        } catch (\Throwable $th) {
            $this->modularModel->rollBackTransaction();
            $this->response->status = 'error crear reserva';
            $this->returnData($this->response, 400);
        }
    }

    public function graboPasajerosReserva($pasajeros, $reservaId)
    {
        //
        $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, "O");
        $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, "O");
        //
        foreach ($pasajeros as $key => $pasajero) {
            try {
                $pasajeroId = $pasajero->id;
                $terminalId = $pasajero->shipment;
                $dni =  $pasajero->dni;
                $monedaId = 1;
                $titular = 0;
                if (isset($pasajero->headline) && $pasajero->headline) {
                    $titular = 1;
                }
                if (isset($pasajero->room) && COUNT($pasajero->room) > 0) {
                    $room = trim($pasajero->room[0]->descripcion) . " - " . trim($pasajero->room[0]->detalle_servicio);
                } else {
                    $room = "SIN HAB";
                }
                $sql = "INSERT INTO reservas_pasajeros (terminales_id, reservas_id, pasajeros_id, titular, room, infoa) VALUES ('$terminalId','$reservaId','$pasajeroId','$titular','$room','$pasajero->infoa')";
                $this->modularModel->sqlVarios($sql);
                // verifico dni pasajero si es vacio grabo id
                $elPasajero = $this->modularModel->getRegistroID("pasajeros", $pasajeroId, "O");
                if (($elPasajero->dni == null) || ($elPasajero->dni == '') || ($elPasajero->dni == '0')) {
                    $sql = "UPDATE pasajeros SET dni = 'id$pasajeroId' WHERE id = $pasajeroId";
                    $this->modularModel->sqlVarios($sql);
                }
                // grabo reserva_detalle
                if (!$this->graboReservaDetalle($pasajero, $reservaId)) {
                    return false;
                }
                // recorro las habitaciones del pasajero
                if($room !== 'SIN HAB'){
                foreach ($pasajero->room as $key => $room) {
                    // costos
                    //grabo costos habitaciones
                    $tipoStockID = $room->tipoID;
                    $tipoId = $room->tipoID;
                    $precioServicio = $this->modularModel->getRegistroID("precios", $room->id, 'O');
                    //$precioServicio = $tarifa->costo_individual;
                    $servId = $precioServicio->servicios_id;
                    $ocupantes = [];
                    // calculo ocupantes
                    if ($room->passengers[0] != $pasajeroId) {
                        $ocu = new stdClass();
                        $ocu->id = $room->passengers[0];
                        $ocupantes[0] = $ocu;
                    }
                    if (!$this->ocuparRoomGrabar($reservaId, $pasajeroId, $tipoId, $servId, $ocupantes)) {
                        return false;
                    }
                }
                }
            } catch (\Throwable $th) {
                return false;
            }
        }
        return true;
    }

    public function graboReservaDetalle($pasajero, $reservaId)
    {
        try {
            $cantidad = 1;
            $impVenta = 0;
            $impCosto = 0;
            $impGastos = 0;
            $impSistema = 0;
            $impComision = 0;
            $impSena = 0;
            $pasajeroId = $pasajero->id;
            $butacaId = 0;
            //->seat->seat ?
            if ($pasajero->seat != null && $pasajero->seat->id != -1) {
                if(isset($pasajero->seat->id)){
                    $butacaId = $pasajero->seat->id;
                }else{
                    $butacaId = $pasajero->seat;
                }
            }
            $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, 'O');
            $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, "O");
            $monedaId = $tarifa->monedas_id;
            $salidaId = $reserva->salidas_id;
            $salida = $this->modularModel->getRegistroID("salidas", $salidaId, 'O');
            $cliente = $this->modularModel->getRegistroID("clientes", $reserva->clientes_id, 'O');
            $categoriaCliente = $this->modularModel->getRegistroID("categoriaclientes", $cliente->categoriaclientes_id, 'O');
            $gruposTarifasID = $tarifa->grupostarifas_id;
            $grupoTarifa = $this->modularModel->getRegistroID("grupostarifas", $gruposTarifasID, 'O');
            $edad = $this->edadFecha($pasajero->birthday);
            $cantidadTotal = 1;
            $politicaEdad = 4;
            if (isset($pasajero->room) && COUNT($pasajero->room) > 0) {
                foreach ($pasajero->room as $room) {
                    $cantidadTotal = max($cantidadTotal, $pasajero->room[0]->cantidad_total);
                    $politicaEdad = max($politicaEdad, $this->politicaEdadHotel($room->id));
                }
            }
            if ($edad < $politicaEdad) {
                $impVenta = $tarifa->precio_menor;
            } else {
                switch ($cantidadTotal) {
                    case 1:
                        $impVenta = $tarifa->precio_single;
                        break;
                    case 2:
                        $impVenta = $tarifa->precio_doble;
                        break;
                    case 3:
                        $impVenta = $tarifa->precio_triple;
                        break;
                    case 4:
                        $impVenta = $tarifa->precio_cuadruple;
                        break;
                }
            }
            if (isset($pasajero->infoa) && ($pasajero->infoa) && (!$pasajero->infoaOcupa)) {
                $impVenta = 0;
            }
            if ($tarifa->micro_cama == 2) {
                $detalle = "SEMICAMA";
            } else {
                $detalle = "CAMA";
            }
            // calculo comision y sena
            $cliente = $this->modularModel->getRegistroID("clientes", $reserva->clientes_id, 'O');
            $categoriaCliente = $this->modularModel->getRegistroID("categoriaclientes", $cliente->categoriaclientes_id, 'O');
            $impComision = $impVenta * $grupoTarifa->comision / 100;
            if (strpos(strtoupper($grupoTarifa->nombre), "NETO") === false) {
                $impComision += $impVenta * $categoriaCliente->comision / 100;
            }
            if (strpos(strtoupper($categoriaCliente->nombre), "SIN COMISION") !== false) {
                $impComision = 0;
            }
            $impSena = $impVenta * $categoriaCliente->sena / 100;
            $impGastos = $impVenta * $grupoTarifa->gastos / 100;
            $impSistema = $impVenta * $grupoTarifa->sistema / 100;
            $fechaIn = $salida->fecha_salida;
            if(isset($pasajero->infoa)){
                $infoa = $pasajero->infoa;
            }

            if (isset($pasajero->typeofUser) ) {
                if ($pasajero->typeofUser == 'MenorSB') {
                    $infoa = 1;
                } else {
                    $infoa = 0;
                }
            }

            $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema, sena, pasajeros_id, infoa) VALUES ('$reservaId',0,0,'$monedaId','$impVenta','$impCosto','$fechaIn','$cantidad','$impComision','$detalle',current_date(),'$impGastos','$impSistema','$impSena','$pasajeroId','$infoa')";
            $this->modularModel->sqlVarios($sql);
            // verifico impuestos internacionales
            if ($pasajero->impuestos->rg4815 > 0) {
                $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema, sena, pasajeros_id, infoa) VALUES ('$reservaId',0,31,'$monedaId','{$pasajero->impuestos->rg4815}','0','$fechaIn','1','0','RG 4815',current_date(),'0','0','0','$pasajeroId','0')";
                $this->modularModel->sqlVarios($sql);
            }
            if ($pasajero->impuestos->pais > 0) {
                $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema, sena, pasajeros_id, infoa) VALUES ('$reservaId',0,32,'$monedaId','{$pasajero->impuestos->pais}','0','$fechaIn','1','0','Impuesto pais',current_date(),'0','0','0','$pasajeroId','0')";
                $this->modularModel->sqlVarios($sql);
            }
            //asigno butaca pasajeros
            if ($butacaId > 0) {
                $sql = "UPDATE butacas_salidas SET pasajeros_id = $pasajeroId, detalle = 'OCUPADA' WHERE butacas_id = $butacaId AND salidas_id = $salidaId";
                $this->modularModel->sqlVarios($sql);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function ocuparRoomGrabar($reseId, $passId, $tipoId, $servId, $ocupantes)
    {
        $reserva = $this->modularModel->getRegistroID("reservas", $reseId, "O");
        $tipoStock = $this->modularModel->getRegistroID("tipostock", $tipoId, "O");
        $pasajero = $this->modularModel->getRegistroID("pasajeros", $passId, "O");
        if (COUNT($ocupantes) > 0) {
            $ocupante = $this->modularModel->getRegistroID("pasajeros", $ocupantes[0]->id, "O");
        } else {
            $ocupante = false;
        }
        // verifico que este libre
        // ocupar habitacion
        try {
            //            
            if ($ocupante) {
                //$lareservaDetalle = $this->modularModel->getRegistro("reservas_detalles","reservas_id = $reseId AND pasajeros_id = {$ocupante->id} AND serviciosId = $servId","O");
                $lareservaDetalle = $this->modularModel->getRegistro("reservas_detalles", "pasajeros_id = {$ocupante->id} AND serviciosId = $servId", "O");
                $detalle = "{$lareservaDetalle->detalle}/{$pasajero->dni}";
                $detalleOld = $lareservaDetalle->detalle;
            } else {
                $detalleOld = "";
                $detalle = "HABITACION {$tipoStock->detalle} ## {$pasajero->dni}";
            }
            $salida_servicio = $this->modularModel->getRegistroID("salidas_servicios", $servId, "O");
            $servicio = $this->modularModel->getRegistroID("servicios", $salida_servicio->servicios_id, "O");
            $roomDetalle = trim($tipoStock->descripcion) . " - " . trim($servicio->detalle_servicio);
            $desde = date("Y-m-d", strtotime($salida_servicio->fecha_ingreso));
            $hasta = date("Y-m-d", strtotime($salida_servicio->fecha_egreso));
            $sql = "SELECT * FROM precios p where p.servicios_id = {$salida_servicio->id} AND tipostock_id = $tipoId AND fecha_desde = '$desde' AND fecha_hasta = '$hasta'";
            $reservaDetalle = $this->modularModel->sqlVariosReturn($sql, "O");
            $fechaIn = '';
            $cantidad = 1;
            $impVenta = 0;
            $impCosto = 0;
            $date1 = date_create($desde);
            $date2 = date_create($hasta);
            $diff = date_diff($date1, $date2, true);
            $noches = $diff->days;
            $monedaId = 1;
            if ($reservaDetalle) {
                $impCosto = $reservaDetalle[0]->costo * $noches;
                $monedaId = $reservaDetalle[0]->monedas_id;
            }
            $impComision = 0;
            $detalle = str_replace("/##", "", $detalle);
            //
            for ($i = 0; $i < 1; $i++) {
                $fecha = date_format($date1, "Y-m-d");
                $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, pasajeros_id) VALUES ('$reseId','$servId','$tipoId','$monedaId','$impVenta','$impCosto','$fecha','$cantidad','$impComision','$detalle',current_date(),'$passId')";
                $this->modularModel->sqlVarios($sql);
                date_add($date1, date_interval_create_from_date_string("1 days"));
            }
            if ($ocupante) {
                //$sql = "UPDATE reservas_detalles SET detalle = '$detalle' WHERE detalle = '$detalleOld' AND serviciosId = '$servId' AND reservas_id = '$reseId'";
                $sql = "UPDATE reservas_detalles SET detalle = '$detalle' WHERE detalle = '$detalleOld' AND serviciosId = '$servId'";
                $this->modularModel->sqlVarios($sql);
            }
            //
            return true;
        } catch (\Throwable $th) {
            return false;
        }
        //
    }

    public function graboHotelesReserva($hoteles, $reservaId)
    {

        $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, 'O');
        $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, 'O');
        $salidaId = $reserva->salidas_id;
        try {
            // utilizo el stock de los hoteles de las habitacion
            foreach ($hoteles as $key => $seccion) {
                foreach ($seccion->hoteles as $key => $hotel) {
                    $habitaciones = $hotel->habitaciones;
                    foreach ($habitaciones as $key => $habitacion) {
                        $precio = $this->modularModel->getRegistroID("precios", $habitacion->id, 'O');
                        $date1 = date_create($precio->fecha_desde);
                        $date2 = date_create($precio->fecha_hasta);
                        $diff = date_diff($date1, $date2, true);
                        $noches = $diff->days;
                        //
                        $sql = "UPDATE precios SET ocupado = ocupado + 1 WHERE id = $habitacion->id";
                        $stock = $this->modularModel->sqlVarios($sql);
                        if ($stock === false) {
                            return false;
                        }
                        /* for ($i=0; $i < $noches; $i++) { 
                            $fecha = date_format($date1,"Y-m-d");
                            $elStock = $this->modularModel->getRegistrosTabla("stock","disponible = 1 AND fecha = '$fecha' AND precios_id = ".$habitacion->id,"numero_referente","O");
                            //
                            $sql = "UPDATE stock SET reservas_id = $reservaId, disponible = 0 WHERE id = ".$elStock[0]->id;
                            $stock = $this->modularModel->sqlVarios($sql);
                            if($stock===false){
                                $this->modularModel->rollBackTransaction();
                                $this->response->status = 'error grabar reserva';
                                $this->returnData($this->response,400);
                            }  
                            //$sql = "INSERT INTO stock_pasajeros(stock_id, pasajeros_id) VALUES ('$stock','[value-2]')"; 
                            date_add($date1,date_interval_create_from_date_string("1 days"));
                        } */
                        // atributos habitaciones
                        foreach ($habitacion->atributos as $atributo) {
                            $sql = "INSERT INTO atributos_habitaciones_reservas (atributos_habitaciones_id, reservas_id, precios_id) VALUES ('" . $atributo->id . "','$reservaId','" . $habitacion->id . "')";
                            if ($this->modularModel->sqlVarios($sql) === false) {
                                return false;
                            }
                        }
                    }
                }
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function graboServicioBus($reservaId)
    {
        $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, 'O');
        $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, "O");
        $serviciosTarifa = $this->serviciosTarifa($tarifa->id);
        // bus servicios
        $sql = "SELECT ss.*, s.detalle_servicio, s.tiposervicios_id, ts.sn_noches FROM salidas_servicios ss, servicios s, tiposervicios ts WHERE s.tiposervicios_id = ts.id AND ss.servicios_id = s.id AND s.tiposervicios_id = 1 AND ss.id IN ({$serviciosTarifa})";
        $otrosServicios = $this->modularModel->sqlVariosReturn($sql, 'O');
        if (count($otrosServicios) > 0) {
            $sql = "SELECT rd.id, r.salidas_id FROM reservas_detalles rd, reservas r WHERE rd.reservas_id = r.id AND r.salidas_id = {$reserva->salidas_id} AND rd.serviciosId = {$otrosServicios[0]->id}";
            $bus_en_salida =  $this->modularModel->sqlVariosReturn($sql, "O");
            if (!$bus_en_salida) {
                foreach ($otrosServicios as $servicioAD) {
                    $desde = date("Y-m-d", strtotime($servicioAD->fecha_ingreso));
                    $hasta = date("Y-m-d", strtotime($servicioAD->fecha_egreso));
                    $servicioId = $servicioAD->id;
                    if ($servicioAD->valor_venta > 0) {
                        continue;
                    }
                    //$precioServicio = $this->modularModel->getRegistro("precios","servicios_id = $servicioId AND fecha_desde = '$desde' AND fecha_hasta = '$hasta'","O");
                    $fechaIn = '';
                    $cantidad = 1;
                    $impVenta = 0;
                    $impCosto = 0;
                    $date1 = date_create($desde);
                    $date2 = date_create($hasta);
                    $diff = date_diff($date1, $date2, true);
                    $noches = $diff->days;
                    if ($servicioAD->sn_noches == 1) {
                        $noches++;
                    }
                    if ($noches < 1) {
                        $noches = 1;
                    }
                    // costo
                    if ($servicioAD->costo_grupal > 0) {
                        $impCosto = $servicioAD->costo_grupal;
                    } else {
                        $impCosto = $servicioAD->costo_individual * $noches;
                    }
                    $impComision = 0;
                    $impGastos = 0;
                    $impSistema = 0;
                    $detalle = trim($servicioAD->detalle_servicio);
                    $tipoStockID = 0;
                    $monedaId = $servicioAD->monedas_id;
                    //
                    for ($i = 0; $i < 1; $i++) {
                        date_add($date1, date_interval_create_from_date_string("1 days"));
                        $fecha = date_format($date1, "Y-m-d");
                        $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema) VALUES ('$reservaId','$servicioId','$tipoStockID','$monedaId','$impVenta','$impCosto','$fecha','$cantidad','$impComision','$detalle',current_date(),'$impGastos','$impSistema')";
                        $reservaDetalle = $this->modularModel->sqlVarios($sql);
                        if ($reservaDetalle === false) {
                            $this->modularModel->rollBackTransaction();
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    public function graboOtrosServicios($reservaId)
    {
        $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, 'O');
        $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, "O");
        // otros servicios grupales sin costo adicional
        $sql = "SELECT ss.*, s.detalle_servicio, s.tiposervicios_id, ts.sn_noches FROM salidas_servicios ss, servicios s, tiposervicios ts WHERE s.tiposervicios_id = ts.id AND ss.servicios_id = s.id AND ss.valor_venta = 0 AND ss.costo_grupal > 0 AND s.tiposervicios_id != 5 AND s.tiposervicios_id != 1 AND ss.id IN ({$tarifa->servicios})";
        $otrosServicios = $this->modularModel->sqlVariosReturn($sql, 'O');
        if (count($otrosServicios) > 0) {
            foreach ($otrosServicios as $servicioAD) {
                $sql = "SELECT rd.id FROM reservas_detalles rd WHERE rd.reservas_id = $reservaId AND rd.serviciosId = {$servicioAD->id}";
                $servicio_en_reserva =  $this->modularModel->sqlVariosReturn($sql, "O");
                if (!$servicio_en_reserva) {
                    $desde = date("Y-m-d", strtotime($servicioAD->fecha_ingreso));
                    $hasta = date("Y-m-d", strtotime($servicioAD->fecha_egreso));
                    $servicioId = $servicioAD->id;
                    $fechaIn = '';
                    $cantidad = 1;
                    $impVenta = 0;
                    $impCosto = 0;
                    $date1 = date_create($desde);
                    $date2 = date_create($hasta);
                    $diff = date_diff($date1, $date2, true);
                    $noches = $diff->days;
                    if ($servicioAD->sn_noches == 1) {
                        $noches++;
                    }
                    if ($noches < 1) {
                        $noches = 1;
                    }
                    // costo
                    $impCosto = $servicioAD->costo_individual * $noches;
                    $impComision = 0;
                    $impGastos = 0;
                    $impSistema = 0;
                    $detalle = trim($servicioAD->detalle_servicio);
                    $tipoStockID = 0;
                    $monedaId = $servicioAD->monedas_id;
                    //
                    for ($i = 0; $i < 1; $i++) {
                        date_add($date1, date_interval_create_from_date_string("1 days"));
                        $fecha = date_format($date1, "Y-m-d");
                        $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema) VALUES ('$reservaId','$servicioId','$tipoStockID','$monedaId','$impVenta','$impCosto','$fecha','$cantidad','$impComision','$detalle',current_date(),'$impGastos','$impSistema')";
                        $reservaDetalle = $this->modularModel->sqlVarios($sql);
                        if ($reservaDetalle === false) {
                            $this->modularModel->rollBackTransaction();
                            return false;
                        }
                    }
                }
            }
        }
        // otros servicios individuales sin costo adicional
        $serviciosTarifa = $this->serviciosTarifa($tarifa->id);
        $sql = "SELECT ss.*, s.detalle_servicio, s.tiposervicios_id, ts.sn_noches FROM salidas_servicios ss, servicios s, tiposervicios ts WHERE s.tiposervicios_id = ts.id AND ss.servicios_id = s.id AND ss.valor_venta = 0 AND ss.costo_grupal = 0 AND s.tiposervicios_id != 5 AND s.tiposervicios_id != 1 AND ss.id IN ({$serviciosTarifa})";
        $otrosServicios = $this->modularModel->sqlVariosReturn($sql, 'O');
        if (count($otrosServicios) > 0) {
            $sql = "SELECT p.id, p.fecha_nacimiento FROM reservas_pasajeros rp, pasajeros p WHERE rp.pasajeros_id = p.id AND rp.reservas_id = $reservaId";
            $pasajeros = $this->modularModel->sqlVariosReturn($sql, 'O');
            foreach ($pasajeros as $pasajero) {
                $edad = $this->edadFecha($pasajero->fecha_nacimiento);
                foreach ($otrosServicios as $servicioAD) {
                    if ($servicioAD->edad_min > 0) {
                        if (($edad < $servicioAD->edad_min) || ($edad > $servicioAD->edad_max)) {
                            continue;
                        }
                    }
                    $sql = "SELECT rd.id FROM reservas_detalles rd WHERE rd.reservas_id = $reservaId AND pasajeros_id = {$pasajero->id} AND rd.serviciosId = {$servicioAD->id}";
                    $servicio_en_reserva =  $this->modularModel->sqlVariosReturn($sql, "O");
                    if (!$servicio_en_reserva) {
                        $desde = date("Y-m-d", strtotime($servicioAD->fecha_ingreso));
                        $hasta = date("Y-m-d", strtotime($servicioAD->fecha_egreso));
                        $servicioId = $servicioAD->id;
                        $fechaIn = '';
                        $cantidad = 1;
                        $impVenta = 0;
                        $impCosto = 0;
                        $date1 = date_create($desde);
                        $date2 = date_create($hasta);
                        $diff = date_diff($date1, $date2, true);
                        $noches = $diff->days;
                        if ($servicioAD->sn_noches == 1) {
                            $noches++;
                        }
                        if ($noches < 1) {
                            $noches = 1;
                        }
                        // costo
                        $impCosto = $servicioAD->costo_individual * $noches;
                        $impComision = 0;
                        $impGastos = 0;
                        $impSistema = 0;
                        $detalle = trim($servicioAD->detalle_servicio);
                        $tipoStockID = 0;
                        $monedaId = $servicioAD->monedas_id;
                        //
                        for ($i = 0; $i < 1; $i++) {
                            date_add($date1, date_interval_create_from_date_string("1 days"));
                            $fecha = date_format($date1, "Y-m-d");
                            $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema, pasajeros_id) VALUES ('$reservaId','$servicioId','$tipoStockID','$monedaId','$impVenta','$impCosto','$fecha','$cantidad','$impComision','$detalle',current_date(),'$impGastos','$impSistema','{$pasajero->id}')";
                            $reservaDetalle = $this->modularModel->sqlVarios($sql);
                            if ($reservaDetalle === false) {
                                $this->modularModel->rollBackTransaction();
                                return false;
                            }
                        }
                    }
                }
            }
        }
        //
        return true;
    }

    public function crearPasajeroReturn($pasajero, $reservaId)
    {
        //surname name sex nacionality dni areaCode phone birthday email;
        if (is_null($pasajero)) {
            $this->response->status = 'error passenger not found';
            return $this->response;
        }
        $this->modularModel->beginTransaction();
        try {
            $pasajero->dni = trim($pasajero->dni);
            //Busco pasajero por dni
            $sql = "SELECT id FROM pasajeros WHERE dni = {$pasajero->dni}";
            $passengerComplete = $this->modularModel->sqlVariosReturn($sql, 'O');
            if (count($passengerComplete) > 0) {
                $pasajero->id = $passengerComplete[0]->id;
            }

            if (!is_null($pasajero->id)) {
                $sql = "UPDATE pasajeros SET apellido = '" . $pasajero->surname . "', nombre = '" . $pasajero->name . "' , sexos_id = '" . $pasajero->sex . "' , paises_id = '" . $pasajero->nacionality . "' , dni = '" . $pasajero->dni . "' , codigo_area = '" . $pasajero->areaCode . "' , telefono = '" . $pasajero->phone . "' , fecha_nacimiento = '" . $pasajero->birthday . "' , mail = '" . $pasajero->email . "'  WHERE id = " . $pasajero->id;
            } else {
                $sql = "INSERT INTO pasajeros (apellido, nombre, sexos_id, paises_id, dni, codigo_area, telefono, fecha_nacimiento, mail) VALUES('" . $pasajero->surname . "','" . $pasajero->name . "','" . $pasajero->sex . "','" . $pasajero->nacionality . "','" . $pasajero->dni . "','" . $pasajero->areaCode . "', '" . $pasajero->phone . "','" . $pasajero->birthday . "', '" . $pasajero->email . "')";
            }
            $status = $this->modularModel->sqlVarios($sql);
            if ($status === false) {
                $this->response->status = 'error';
            }
            if (is_null($pasajero->id)) {
                $pasajero->id = $status;
            }
            // calculo infoa
            $infoa = 0;
            $date1 = date_create($pasajero->birthday);
            $date2 = date_create(date("Y-m-d"));
            $diff = date_diff($date1, $date2, false);
            $anos = $diff->y;
            if ($anos < 4) {
                $infoa = 1;
            }
            $pasajero->edad = $anos;
            // borro observaciones
            $sql = 'DELETE FROM atributos_pasajeros WHERE pasajeros_id = ' . $pasajero->id;
            $this->modularModel->sqlVarios($sql);
            //grabo observaciones pasajero
            if ($pasajero->observations) {
                foreach ($pasajero->observations as $key => $observacion) {
                    $existe = $this->modularModel->getRegistro("atributos_pasajeros", "pasajeros_id = " . $pasajero->id . " AND atributos_id = " . $observacion, "O");
                    if (!$existe) {
                        $sql = "INSERT INTO atributos_pasajeros (pasajeros_id, atributos_id) VALUES('" . $pasajero->id . "','" . $observacion . "')";
                        $this->modularModel->sqlVarios($sql);
                    }
                }
            }
            //Añadir pasajero a una reservaa
            //$reservaId = $this->crearReservaSalida();
            if (!is_null($reservaId)) {
                //
                $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, "O");
                //Reemplazar pasajero
                $pasajeroNew = $pasajero->id;
                if (isset($pasajero->oldPassenger)) {
                    $pasajeroOld = $pasajero->oldPassenger;
                    $elPasajeroNew = $this->modularModel->getRegistroID("pasajeros", $pasajeroNew, "O");
                    $elPasajeroOld = $this->modularModel->getRegistroID("pasajeros", $pasajeroOld, "O");
                    $sql = "UPDATE butacas_salidas SET pasajeros_id = $pasajeroNew WHERE pasajeros_id = $pasajeroOld AND reservas_id = $reservaId";
                    $this->modularModel->sqlVarios($sql);
                    $sql = "UPDATE reservas_pasajeros SET pasajeros_id = $pasajeroNew, terminales_id = {$pasajero->shipment}, infoa = $infoa WHERE pasajeros_id = $pasajeroOld AND reservas_id = $reservaId";
                    $this->modularModel->sqlVarios($sql);
                    $sql = "UPDATE reservas_detalles SET detalle = REPLACE(detalle,'{$elPasajeroOld->dni}','{$elPasajeroNew->dni}') WHERE reservas_id = $reservaId";
                    $this->modularModel->sqlVarios($sql);
                    $sql = "UPDATE reservas_detalles SET pasajeros_id = $pasajeroNew WHERE pasajeros_id = $pasajeroOld AND reservas_id = $reservaId";
                    $this->modularModel->sqlVarios($sql);
                } else {
                    $verificoPasajero = $this->modularModel->getRegistro("reservas_pasajeros", "reservas_id = $reservaId AND pasajeros_id = $pasajeroNew", "O");
                    if (!$verificoPasajero) {
                        $sql = "INSERT INTO reservas_pasajeros (terminales_id, reservas_id, pasajeros_id, room, titular, infoa) VALUES('{$pasajero->shipment}','$reservaId','$pasajeroNew','',0, $infoa)";
                        $this->modularModel->sqlVarios($sql);
                    }
                }
            }

            $this->modularModel->commitTransaction();
            $this->response->passenger = $pasajero->id;
            $this->response->passengerData = $pasajero;
            return $this->response;
        } catch (\Throwable $th) {
            $this->modularModel->rollBackTransaction();
            if (isset($pasajero->oldPassenger)) {
                $this->response->status = "Error. Al reemplazar pasajero";
            } else {
                $this->response->status = "Error. Al agregar pasajero";
            }
            return $this->response;
        }
    }

    public function graboAdicionalesPasajeros($reservaId, $pasajeros)
    {
        // otros servicios por pasajero con costo adicional
        $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, 'O');
        $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, "O");
        $cliente = $this->modularModel->getRegistroID("clientes", $reserva->clientes_id, 'O');
        $categoriaCliente = $this->modularModel->getRegistroID("categoriaclientes", $cliente->categoriaclientes_id, 'O');
        //
        $gruposTarifasID = $tarifa->grupostarifas_id;
        $grupoTarifa = $this->modularModel->getRegistroID("grupostarifas", $gruposTarifasID, 'O');
        $monedaId = $tarifa->monedas_id;
        foreach ($pasajeros as $key => $pasajero) {
            if ($pasajero->totalAdicionales > 0) {
                foreach ($pasajero->adicionales as $adicional) {
                    $sql = "SELECT ss.*, s.detalle_servicio, s.tiposervicios_id, ts.sn_noches FROM salidas_servicios ss, servicios s, tiposervicios ts WHERE s.tiposervicios_id = ts.id AND ss.servicios_id = s.id AND ss.id = {$adicional->id}";
                    $otrosServicios = $this->modularModel->sqlVariosReturn($sql, 'O');
                    if (count($otrosServicios) > 0) {
                        foreach ($otrosServicios as $servicioAD) {
                            $sql = "SELECT rd.id FROM reservas_detalles rd WHERE rd.reservas_id = $reservaId AND pasajeros_id = {$pasajero->id} AND rd.serviciosId = {$servicioAD->id}";
                            $servicio_en_reserva =  $this->modularModel->sqlVariosReturn($sql, "O");
                            if (!$servicio_en_reserva) {
                                $desde = date("Y-m-d", strtotime($servicioAD->fecha_ingreso));
                                $hasta = date("Y-m-d", strtotime($servicioAD->fecha_egreso));
                                $servicioId = $servicioAD->id;
                                $fechaIn = '';
                                $cantidad = 1;
                                $date1 = date_create($desde);
                                $date2 = date_create($hasta);
                                $diff = date_diff($date1, $date2, true);
                                $noches = $diff->days;
                                if ($servicioAD->sn_noches == 1) {
                                    $noches++;
                                }
                                if ($noches < 1) {
                                    $noches = 1;
                                }
                                // venta   
                                $impVenta = $servicioAD->valor_venta * $noches * $adicional->cotizacion;
                                // costo
                                $impCosto = $servicioAD->costo_individual * $noches; // * $adicional->cotizacion;
                                $impComision = $impVenta * $grupoTarifa->comision / 100;
                                if (strpos(strtoupper($grupoTarifa->nombre), "NETO") === false) {
                                    $impComision += $impVenta * $categoriaCliente->comision / 100;
                                }
                                if (strpos(strtoupper($categoriaCliente->nombre), "SIN COMISION") !== false) {
                                    $impComision = 0;
                                }
                                $impGastos = $impVenta * $grupoTarifa->gastos / 100;
                                $impSistema = $impVenta * $grupoTarifa->sistema / 100;
                                $detalle = trim($servicioAD->detalle_servicio);
                                $tipoStockID = 0;
                                //
                                for ($i = 0; $i < 1; $i++) {
                                    date_add($date1, date_interval_create_from_date_string("1 days"));
                                    $fecha = date_format($date1, "Y-m-d");
                                    $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema, pasajeros_id) VALUES ('$reservaId','$servicioId','$tipoStockID','{$adicional->monedas_id}','$impVenta','$impCosto','$fecha','$cantidad','$impComision','$detalle',current_date(),'$impGastos','$impSistema','{$pasajero->id}')";
                                    $reservaDetalle = $this->modularModel->sqlVarios($sql);
                                    if ($reservaDetalle === false) {
                                        $this->modularModel->rollBackTransaction();
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    // promociones
    public function graboPromocionesReserva($reservaId, $pasajeros)
    {
        // promociones reserva
        $reserva = $this->modularModel->getRegistroID("reservas", $reservaId, 'O');
        $cliente = $this->modularModel->getRegistroID("clientes", $reserva->clientes_id, 'O');
        $categoriaCliente = $this->modularModel->getRegistroID("categoriaclientes", $cliente->categoriaclientes_id, 'O');
        $salidaId = $reserva->salidas_id;
        $sql = "SELECT p.* FROM promociones p, salidas_promociones sp WHERE sp.salidas_id = $salidaId AND sp.promociones_id = p.id";
        $promociones_salida = $this->modularModel->sqlVariosReturn($sql, "O");
        $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, "O");

        $promos = [];
        //
        $cambioTarifa = false;
        if ($promociones_salida[0]->grupostarifas_id > 0) {
            $cambioTarifa = true;
            $gruposTarifasID = $promociones_salida[0]->grupostarifas_id;
        } else {
            $gruposTarifasID = $tarifa->grupostarifas_id;
        }
        $tarifa = $this->modularModel->getRegistroID("salidas_precios", $reserva->tarifaId, "O");
        $monedaId = $tarifa->monedas_id;
        $grupoTarifa = $this->modularModel->getRegistroID("grupostarifas", $gruposTarifasID, 'O');
        //
        if (COUNT($promociones_salida) > 0) {
            $promos = $this->calcularPromociones($pasajeros, $promociones_salida, $salidaId, $grupoTarifa);
        }

        foreach ($promos as $key => $promo) {
            // venta
            $impVenta = $promo['importe'];
            // costo
            $impCosto = 0;
            $impComision = $impVenta * $grupoTarifa->comision / 100;
            if (strpos(strtoupper($grupoTarifa->nombre), "NETO") === false) {
                $impComision += $impVenta * $categoriaCliente->comision / 100;
            }
            if (strpos(strtoupper($categoriaCliente->nombre), "SIN COMISION") !== false) {
                $impComision = 0;
            }
            $impGastos = $impVenta * $grupoTarifa->gastos / 100;
            $impSistema = $impVenta * $grupoTarifa->sistema / 100;
            $detalle = $promo['descripcion'];
            $fecha = $promo['expira'];
            $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema, pasajeros_id) VALUES ('$reservaId',0,'30','$monedaId','$impVenta','$impCosto','$fecha','1','$impComision','$detalle',current_date(),'$impGastos','$impSistema',null)";
            $reservaDetalle = $this->modularModel->sqlVarios($sql);
            if ($reservaDetalle === false) {
                $this->modularModel->rollBackTransaction();
                return false;
            }
        }
        if ($cambioTarifa) {
            if (strpos(strtoupper($categoriaCliente->nombre), "SIN COMISION") === false) {
                $sql = "UPDATE reservas_detalles SET comision = precio_venta * $grupoTarifa->comision / 100 WHERE reservas_id = $reservaId";
                $this->modularModel->sqlVarios($sql);
                $sql = "UPDATE reservas_detalles SET gastos = (precio_venta - comision) * $grupoTarifa->gastos / 100 WHERE reservas_id = $reservaId";
                $this->modularModel->sqlVarios($sql);
            } else {
                $sql = "UPDATE reservas_detalles SET comision = 0 WHERE reservas_id = $reservaId";
                $this->modularModel->sqlVarios($sql);
                $sql = "UPDATE reservas_detalles SET gastos = (precio_venta) * $grupoTarifa->gastos / 100 WHERE reservas_id = $reservaId";
                $this->modularModel->sqlVarios($sql);
            }
        }
        return true;
    }

    public function calcularPromociones($pasajeros, $promociones_salida, $salidaId, $grupoTarifa)
    {

        $mas_barato = $pasajeros[0]->importeVenta;
        foreach ($pasajeros as $pasajero) {
            $mas_barato = ($pasajero->importeVenta < $mas_barato) ? $pasajero->importeVenta : $mas_barato;
        }
        $hoy = date("Y-m-d");
        $promos = [];
        $sigo = true;
        foreach ($promociones_salida as $promo) {
            //$expira=date_create($hoy);

            //
            if ($promo->tope_salida > 0) {
                // cantidad de promos en salida
                $sql = "SELECT COUNT(*) as cantidad FROM reservas_detalles WHERE tipostock_id = 30 AND reservas_id IN (SELECT id FROM reservas WHERE salidas_id = $salidaId)";
                $cantidadPromo = $this->modularModel->sqlVariosReturn($sql, 'O');
                //
                if ((int)$cantidadPromo[0]->cantidad >= (int)$promo->tope_salida) {
                    $sigo = false;
                }
            }
            $expira = date("Y-m-d", strtotime($hoy . "+ {$promo->dias} days"));
            //date_add($expira,date_interval_create_from_date_string("{$promo->dias} days"));
            if (($promo->promocion != "") && ($sigo)) {
                if (($hoy >= $promo->fecha_desde) && ($hoy <= $promo->fecha_hasta)) {

                    if (strpos($promo->promocion, "x") > 0) {
                        $of_separada = explode("x", $promo->promocion);
                        $resto = intval(COUNT($pasajeros) / $of_separada[0]);
                        if ($resto > 0) {
                            $elImporte = $mas_barato * $resto * -1;
                            array_push($promos, ["descripcion" => $resto . " " . $promo->descripcion, "importe" => $elImporte, "expira" => $expira]);
                        }
                    }
                    if (strpos($promo->promocion, "-") !== false) {
                        if (strpos($promo->promocion, "2do") !== false) {
                            $of_separada = explode("-", $promo->promocion);
                            $resto = intval(COUNT($pasajeros) / 2);
                            if ($resto > 0) {
                                $of_separada2 = explode("%", $of_separada[1]);
                                $valor = $mas_barato * (int)$of_separada2[0] / 100 * $resto * -1;
                                $elImporte = $valor;
                                array_push($promos, ["descripcion" => "$resto -" . $promo->descripcion, "importe" => $elImporte, "expira" => $expira]);
                            }
                        } else {
                            $of_separada = explode("%", $promo->promocion);
                            $valor = $mas_barato * (int)$of_separada[0] / 100;
                            $cantidad = COUNT($pasajeros);
                            $elImporte = $valor * $cantidad;
                            array_push($promos, ["descripcion" => "1 -" . $promo->descripcion, "importe" => $elImporte, "expira" => $expira]);
                        }
                    }
                }
            }
        }
        return $promos;
    }

    public function politicaEdadHotel($room)
    {
        $sql = "SELECT p.*, p.tipostock_id, s.operadores_id FROM precios p, salidas_servicios ss, servicios s WHERE p.servicios_id = ss.id AND ss.servicios_id = s.id AND p.id = {$room}";
        $precio = $this->modularModel->sqlVariosReturn($sql, "O");
        if($precio){
            $politica = $this->modularModel->getRegistro("operadores_tipostock", "operadores_id = {$precio[0]->operadores_id} AND tipostock_id = {$precio[0]->tipostock_id}", "O");
            if ($politica) {
                return $politica->edad_menor;
            }
        }
        $parametros = $this->modularModel->getRegistroID("parametros", 1, "O");
        return $parametros->edad_infoa;
    }
    
    public function limpiarReserva(){
        $this->reservaDetalle->id = null;
        $this->reservaDetalle->reservas_id = null;
        $this->reservaDetalle->serviciosId = null;
        $this->reservaDetalle->tipostock_id = null;
        $this->reservaDetalle->monedas_id = null;
        $this->reservaDetalle->precio_venta = null;
        $this->reservaDetalle->costo = null;
        $this->reservaDetalle->fecha = null;
        $this->reservaDetalle->cantidad = null;
        $this->reservaDetalle->comision = null;
        $this->reservaDetalle->detalle = null;
        $this->reservaDetalle->fecha_modificacion = null;
        $this->reservaDetalle->gastos = null;
        $this->reservaDetalle->sistema = null;
        $this->reservaDetalle->sena = null;
        $this->reservaDetalle->pasajeros_id = null;
        $this->reservaDetalle->terminales_id = null;
        $this->reservaDetalle->infoa = null;
        $this->reservaDetalle->fecha_ingreso = null;
        $this->reservaDetalle->fecha_egreso = null;
        $this->reservaDetalle->orden = null;
        $this->reservaDetalle->pagado = null;
        $this->reservaDetalle->observacion = null;
        $this->reservaDetalle->fecha_limite = null;
    }

    public function sqlReservaDetalle(){
        //TRUE FALSE TRY CATCH
        $reservaDetalle = $this->reservaDetalle;
        $sql = "INSERT INTO reservas_detalles (reservas_id, serviciosId, tipostock_id, monedas_id, precio_venta, costo, fecha, cantidad, comision, detalle, fecha_modificacion, gastos, sistema, sena, pasajeros_id, terminales_id, infoa, fecha_ingreso, fecha_egreso, orden, pagado, observacion, fecha_limite) VALUES ('$reservaDetalle->reservas_id', '$reservaDetalle->serviciosId', '$reservaDetalle->tipostock_id', '$reservaDetalle->monedas_id', '$reservaDetalle->precio_venta', '$reservaDetalle->costo', '$reservaDetalle->fecha', '$reservaDetalle->cantidad', '$reservaDetalle->comision', '$reservaDetalle->detalle', $reservaDetalle->fecha_modificacion, '$reservaDetalle->gastos', '$reservaDetalle->sistema', '$reservaDetalle->sena', '$reservaDetalle->pasajeros_id', '$reservaDetalle->terminales_id', '$reservaDetalle->infoa', '$reservaDetalle->fecha_ingreso', '$reservaDetalle->fecha_egreso', '$reservaDetalle->orden', '$reservaDetalle->pagado', '$reservaDetalle->observacion', '$reservaDetalle->fecha_limite')";
        return $this->modularModel->sqlVarios($sql);
    }

    public function getRetencionesReturn($desde,$hasta,$clase,$tipo,$empresaId){
        $clase = strtolower($clase);
        if($clase == "compra"){
          $source = 'operadores';
          $sourceNombre = 'nombre_agencia';
        }
        else{
          $source = 'clientes';
          $sourceNombre = 'nombre';
        }
        $sql = "SELECT o.*, '$tipo' as tipo, o.$tipo as retencion, c.cuit as cuit FROM operacion o, $source c WHERE o.empresa_id = $empresaId AND o.clientes_id = c.id AND o.clase = '$clase' AND o.$tipo != 0 AND o.Fecha BETWEEN '$desde' AND '$hasta'";
        $items = $this->modularModel->sqlVariosReturn($sql,"O");
        return $items;
    }

    public function serviciosTarifa($tarifaId){
        $sql = "SELECT servicios_id FROM servicios_salidas_precios WHERE salidas_precios_id = $tarifaId";
        $ss = $this->modularModel->sqlVariosReturn($sql, 'O');
        $strServicios = "";
        foreach ($ss as $key => $item) {
            $strServicios .= $item->servicios_id.","; 
        }
        $strServicios .= "#";
        $strServicios = str_replace(",#","",$strServicios);

        return $strServicios;
    }

}
