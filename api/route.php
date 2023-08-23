<?php
require_once 'routeClass.php';
require_once 'controller/authController.php';
require_once 'controller/crudController.php';
require_once 'controller/ticketController.php';
require_once 'controller/dataTableController.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');
$r = new Router();

// caja route

//AUTH
$r->addRoute("auth", "POST", "authController", "authenticate");
$r->addRoute("refresh-token", "POST", "authController", "refreshToken");
/* $r->addRoute("getform", "GET", "authController", "getForm");
  $r->addRoute("getform", "GET", "authController", "getForm"); */

//Bug
$r->addRoute("reportBug", "POST", "crudController", "reportBug");

//Crud
$r->addRoute("buttons", "POST", "authController", "getButtons");
$r->addRoute("getform", "POST", "crudController", "buildTableForm");
$r->addRoute("addRegister", "POST", "crudController", "addRegister");
$r->addRoute("updateRegister", "PUT", "crudController", "updateRegister");
$r->addRoute("deleteRegister/:table/:id", "DELETE", "crudController", "deleteRegister"); 
$r->addRoute("dataTable", "POST", "dataTableController", "dataTableData");
$r->addRoute("getDataTable", "POST", "crudController", "getDataTable");

$r->addRoute("backoffice/management", "GET", "crudController", "management");
$r->addRoute("parameters", "GET", "crudController", "getParameters");

//tickets
$r->addRoute("addTicket", "POST", "ticketController", "addTicket");
$r->addRoute("changeTicketStatus", "POST", "ticketController", "changeTicketStatus");
$r->addRoute("getTicketsTabla", "POST", "ticketController", "getTicketsTabla");
$r->addRoute("getTicketDetalle/:id", "GET", "ticketController", "getTicketDetalle");
$r->addRoute("sendMensaje", "POST", "ticketController", "sendMensaje");


// datatable
$r->addRoute("backoffice/getDataTable", "POST", "dataTableController", "getDataTable");
$r->addRoute("getRegistersFromTable", "POST", "dataTableController", "getRegistersFromTable");

//run
$r->route($_GET['action'], $_SERVER['REQUEST_METHOD']);