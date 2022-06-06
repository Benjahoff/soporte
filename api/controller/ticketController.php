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


    public function getTicketsTabla()
    {
        $sql = "SELECT t.id, t.titulo, t.dispositivo, t.fecha, u.username, e.descripcion AS estado, e.color FROM ticket t, estado e, user u WHERE t.user_id = u.id AND t.estado_id = e.id;";
        $tickets = $this->modularModel->sqlVariosReturn($sql, "O");
        $this->response->tickets = $tickets;
        $this->returnData($this->response, 200);
    }

    public function getTicketDetalle($params = [])
    {
        $id = $params[':id'];
        $sql = "SELECT t.id, t.titulo, t.dispositivo, t.fecha, u.username, e.descripcion FROM ticket t, estado e, user u WHERE t.id = $id AND t.user_id = u.id AND t.estado_id = e.id;";
        $ticket = new stdClass();
        $ticket = $this->modularModel->sqlVariosReturn($sql, "O");
        $sql = "SELECT tr.detalle, tr.fecha, u.username, u.id AS userId FROM ticketrenglon tr, user u WHERE tr.ticket_id = $id AND u.id = tr.user_id ORDER BY tr.id;";
        $renglones = $this->modularModel->sqlVariosReturn($sql, "O");
        $this->response->ticket = $ticket;
        $this->response->renglones = $renglones;
        $this->returnData($this->response, 200);
    }
}
