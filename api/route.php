<?php
require_once 'routeClass.php';
require_once 'controller/authController.php';
require_once 'controller/crudController.php';
require_once 'controller/ticketController.php';

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
$r->addRoute("getDataTable", "POST", "crudController", "getDataTable");
$r->addRoute("cargarAgenciaCliente", "GET", "crudController", "cargarAgenciaCliente");
$r->addRoute("validarDNI", "POST", "crudController", "validarDNI");
$r->addRoute("borrarSalidas", "GET", "crudController", "borrarSalidas");
$r->addRoute("usoRestringido/vaciarBasePrueba", "GET", "crudController", "vaciarBasePrueba");

$r->addRoute("backoffice/management", "GET", "crudController", "management");
$r->addRoute("parameters", "GET", "crudController", "getParameters");

//tickets
$r->addRoute("getTicketsTabla", "GET", "ticketController", "getTicketsTabla");
$r->addRoute("getTicketDetalle/:id", "GET", "ticketController", "getTicketDetalle");

//run
$r->route($_GET['action'], $_SERVER['REQUEST_METHOD']);


