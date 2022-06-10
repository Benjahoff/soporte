<?php

require_once 'crudController.php';

require 'vendor/autoload.php';

class ticketController extends crudController
{

    public function __construct()
    {
        parent::__construct();
        $this->response = new stdClass();
    }

    public function addTicket()
    {
        $userId= $this->data->userId;
        $titulo= $this->data->titulo;
        $equipo= $this->data->equipo;
        $detalle= $this->data->detalle;
        $sql = "INSERT INTO ticket(titulo, dispositivo, user_id, estado_id) VALUES ('$titulo', '$equipo', $userId, 1)";
        $this->modularModel->sqlVarios($sql);
        $sql = "SELECT MAX( id ) AS id FROM ticket;";
        $ticketId = $this->modularModel->sqlVariosReturn($sql,"O");
        $sql = "INSERT INTO ticketrenglon(ticket_id, user_id, detalle) VALUES ({$ticketId[0]->id}, $userId, '$detalle');";
        $ticketId = $this->modularModel->sqlVarios($sql);
        $this->response->status = "OK";
        $this->returnData($this->response, 200);
    }

    public function getTicketsTabla()
    {
        $token = $this->data->token;
        $userData = $this->getData($token);
        $levelUser = $userData->level;
        $idUser = $userData->id;
        if ($levelUser > 6) {
            $sql = "SELECT t.id, t.titulo, t.dispositivo, t.fecha, u.username, e.descripcion AS estado, e.color FROM ticket t, estado e, user u WHERE t.user_id = u.id AND t.estado_id = e.id;";
        } else {
            $sql = "SELECT t.id, t.titulo, t.dispositivo, t.fecha, u.username, e.descripcion AS estado, e.color FROM ticket t, estado e, user u WHERE t.user_id = $idUser AND t.user_id = u.id AND t.estado_id = e.id;";
        }
        $tickets = $this->modularModel->sqlVariosReturn($sql, "O");
        $this->response->tickets = $tickets;
        $this->returnData($this->response, 200);
    }

    public function getTicketDetalle($params = [])
    {
        $id = $params[':id'];
        $sql = "SELECT t.id, t.titulo, t.dispositivo, t.fecha, u.username, e.descripcion AS estado, e.id AS estadoId FROM ticket t, estado e, user u WHERE t.id = $id AND t.user_id = u.id AND t.estado_id = e.id;";
        $ticket = new stdClass();
        $ticket = $this->modularModel->sqlVariosReturn($sql, "O");
        $sql = "SELECT tr.detalle, tr.fecha, u.username, u.id AS userId FROM ticketrenglon tr, user u WHERE tr.ticket_id = $id AND u.id = tr.user_id ORDER BY tr.id;";
        $renglones = $this->modularModel->sqlVariosReturn($sql, "O");
        $estados = $this->modularModel->getRegistrosTabla('estado','','','O');
        $this->response->estados = $estados;
        $this->response->ticket = $ticket;
        $this->response->renglones = $renglones;
        $this->returnData($this->response, 200);
    }

    public function sendMensaje(){
        $ticket_id = $this->data->ticket_id;
        $user_id = $this->data->user_id;
        $detalle = $this->data->detalle;
        $sql = "INSERT INTO ticketrenglon(ticket_id, user_id, detalle) VALUES ($ticket_id, $user_id, '$detalle');";
        $this->modularModel->sqlVarios($sql);
        $this->response->status = "OK";
        $this->returnData($this->response, 200);
    }

    public function changeTicketStatus(){
        $ticketId = $this->data->ticketId;
        $estadoId = $this->data->estadoId;
        $user_id = $this->data->user_id;
        $sql= "UPDATE ticket SET estado_id = $estadoId WHERE id = $ticketId";
        $this->modularModel->sqlVarios($sql);
        $estado = $this->modularModel->getRegistroID('estado',$estadoId,"O");
        $texto = "El estado del ticket fue cambiado a : ". $estado->descripcion;
        $sql = "INSERT INTO ticketrenglon(ticket_id, user_id, detalle) VALUES ($ticketId, $user_id, '$texto');";
        $this->modularModel->sqlVarios($sql);
        $this->response->status = "OK";
        $this->returnData($this->response, 200);
    }
}
