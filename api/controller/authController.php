<?php

require_once 'controller.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;

class authController extends controller
{

    private static $secret_key = 'Sdw1s9x8@';
    private static $encrypt = ['HS256'];
    private static $aud = null;
    protected $response;
    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data = json_decode(file_get_contents('php://input'));
        $this->response = new stdClass;
    }

    public function authenticate()
    {

        $username = $this->data->username;
        $password = $this->data->password;

        $user = $this->modularModel->getRegistrosTabla("user", "username = '$username'", "", "O");
        //$passverify = password_verify($password,$user[0]->password);
        if ((count($user) > 0) && (password_verify($password, $user[0]->password))) {
            $this->modularModel->sqlVarios('UPDATE user SET last_login = CURRENT_TIMESTAMP WHERE id = ' . $user[0]->id);
            $userData = new stdClass();
            $userData->id = $user[0]->id;
            $userData->name = $user[0]->username;
            $userData->mail = $user[0]->mail;
            $userData->level = $user[0]->nivel;
            $userData->rol = $user[0]->roles_id;
            $this->response->user = new stdClass();
            $token = $this->signIn($userData);
            $userData->token = $token;
            $this->response->user = $userData;
            $this->returnData($this->response, 200);
        }
    }

    public function refreshToken()
    {
        $this->isAuthenticated();
        $userData = $this->getData($this->getToken());
        $token = $this->signIn($userData);
        $userData->token = $token;
        $this->response->user = $userData;
        $this->returnData($this->response, 200);
    }

    protected function getToken()
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            if (strpos($headers['Authorization'], 'Bearer') !== false) {
                //if(isset($headers['Authorization'])){
                $token = str_replace('Bearer ', '', $headers['Authorization']);
                return $token;
            } else {
                http_response_code(401);
                throw new Exception("Invalid token supplied.");
            }
        } else {
            http_response_code(401);
            throw new Exception("Invalid token supplied.");
        }
    }

    public function isAuthenticated()
    {
        $this->check($this->getToken());
        //$this->isAuthorized();
    }

    private function isAuthorized()
    {
        $userData = $this->getData($this->getToken());
        $levelUser = $userData->level;
        $rolUser = $userData->role;
        $route = $_GET['action'];
        $botones = $this->modularModel->getRegistrosTabla("nivelesboton", "enlace = '$route'", "orden", "O");
        if (!$botones) {
            $sql = "INSERT INTO nivelesboton (nombre, detalle, seccion_id, roles_id, nivel, enlace, classbtn, classicon, orden, badge_color, badge_texto, titulo, nivelesboton_id) VALUES ('$route', NULL, '0', '1', '1', '$route', NULL, NULL, '0', NULL, NULL, '0', '1');";
            $botones = $this->modularModel->sqlVarios($sql);
            if ($botones === false) {
                $this->modularModel->rollBackTransaction();
                $this->response->status = 'error add nivelesboton';
                $this->returnData($this->response, 400);
            }
        }
        if ($rolUser != 2) {
            $botones = $this->modularModel->getRegistrosTabla("nivelesboton", "enlace = '$route' AND nivel <= $levelUser AND (roles_id = $rolUser OR roles_id = 1)", "orden", "O");
            if ($botones) {
                return true;
            }
        } else {
            return true;
        }
        http_response_code(401);
        die();
    }

    public static function signIn($data)
    {
        $time = time();
        $token = array(
            'exp' => $time + (60 * 60),
            'aud' => self::aud(),
            'data' => $data
        );
        return JWT::encode($token, self::$secret_key);
    }

    public static function check($token)
    {
        if (empty($token)) {
            http_response_code(401);
            throw new Exception("Invalid token supplied.");
        }
        try {
            $decode = JWT::decode(
                $token,
                self::$secret_key,
                self::$encrypt
            );
        } catch (\Exception $e) {
            print  $e->getMessage() . "
            ";
            http_response_code(401);
            die();
        }

        if ($decode->aud !== self::aud()) {
            http_response_code(401);
            throw new Exception("Invalid user logged in.");
        }
    }

    public static function getData($token)
    {
        try {
            return JWT::decode(
                $token,
                self::$secret_key,
                self::$encrypt
            )->data;
        } catch (\Exception $e) {
            print  $e->getMessage() . "
            ";
            http_response_code(401);
            die();
        }
    }

    private static function aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    public function getButtons()
    {

        $this->isAuthenticated();
        $userData = $this->getData($this->getToken());
        $seccion = $this->data->section;
        $levelUser = $userData->level;
        $rolUser = $userData->role;
        if ($rolUser == 1 || $rolUser == 2) {
            $botones = $this->modularModel->getRegistrosTabla("nivelesboton", "seccion_id = $seccion AND nivelesboton_id = 1", "orden", "O");
        } else {
            $botones = $this->modularModel->getRegistrosTabla("nivelesboton", "seccion_id = $seccion AND nivel <= $levelUser AND (roles_id = $rolUser OR roles_id = 1) AND nivelesboton_id = 1", "orden", "O");
        }
        $navButtons = array();
        foreach ($botones as $boton) {
            $item = $this->getItemButtom($boton);
            array_push($navButtons, $item);
        }

        $this->response->data = $navButtons;
        $this->returnData($this->response, 200);
    }

    private function getItemButtom($boton)
    {
        $this->isAuthenticated();
        $userData = $this->getData($this->getToken());
        $levelUser = $userData->level;
        $rolUser = $userData->role;
        $seccion = $this->data->section;
        $item = new stdClass();
        if ($boton->titulo == 1) {
            $item->title = true;
            $item->name = ucfirst($boton->nombre);
        } else {
            $item->name = ucfirst($boton->nombre);
            $item->url = "/" . $boton->enlace;
            $item->icon = $boton->classicon;

            if (!empty($boton->badge_texto)) {
                $badge = new stdClass();
                $badge->text = $boton->badge_texto;
                $badge->variant = $boton->badge_color;
                $item->badge = $badge;
            }
            if ($boton->nivelesboton_id == 1) {
                if ($rolUser != 2) {
                    $botones = $this->modularModel->getRegistrosTabla("nivelesboton", "seccion_id = $seccion AND nivel <= $levelUser AND (roles_id = $rolUser OR roles_id = 1) AND nivelesboton_id = " . $boton->id, "orden", "O");
                } else {
                    $botones = $this->modularModel->getRegistrosTabla("nivelesboton", "seccion_id = $seccion AND nivelesboton_id = " . $boton->id, "orden", "O");
                }
                if (count($botones) > 0) {
                    $item->children = array();
                    foreach ($botones as $boton_children) {
                        $children = $this->getItemButtom($boton_children);
                        array_push($item->children, $children);
                    }
                }
            }
        }
        return $item;
    }

    protected function verifyTable($table)
    {
        $this->isAuthenticated();
        $userData = $this->getData($this->getToken());
        $levelUser = $userData->level;
        $rolUser = $userData->role;
        $tabla = $this->modularModel->getRegistrosTabla("tablaaux", "nivel <= $levelUser AND nombre = '$table'", "", "O");
        if ($tabla) {
            return true;
        }
        $this->response->error = "Sin autorizacion";
        $this->returnData($this->response, 400);
    }

    protected function verifyTableButton($table)
    {
        $this->isAuthenticated();
        $userData = $this->getData($this->getToken());
        $levelUser = $userData->level;
        $rolUser = $userData->role;
        $tabla = $this->modularModel->getRegistrosTabla("tablaaux", "nivel <= $levelUser AND nombre = '$table'", "", "O");
        if ($tabla) {
            return true;
        }
        return false;
    }

    protected function armar_botones($la_tabla, $titulo)
    {
        $userData = $this->getData($this->getToken());
        $levelUser = $userData->level;
        $menu_botones = '';
        $this->modularModel->beginTransaction();
        $codigo_seccion = $this->modularModel->getRegistro('seccion', 'nombre = "Tablas ' . $la_tabla . '"', "A");
        if (!$codigo_seccion) {
            $controlSql = 'INSERT INTO seccion (nombre, nivel) values ("Tablas ' . $la_tabla . '","9")';
            if ($this->modularModel->sqlVarios($controlSql) === false) {
                $this->modularModel->rollBackTransaction();
                return false;
            }
            $codigo_seccion = $this->modularModel->getRegistro('seccion', 'nombre = "Tablas ' . $la_tabla . '"', "A");
        }
        //$codigo_seccion2 = $codigo_seccion->fetch(PDO::FETCH_ASSOC);
        $lista_botones_control = $this->modularModel->getRegistrosTabla('nivelesboton', 'nivel <= 9 and seccion_id = ' . $codigo_seccion['id'], 'orden', "A");

        if (COUNT($lista_botones_control) == 0) {
            $controlSql = "Replace into nivelesboton (nombre, seccion_id, nivel, enlace, classbtn, classicon, orden, badge_color, badge_texto, titulo,nivelesboton_id) values ('agregar','" . $codigo_seccion['id'] . "', '9', 'agregar', 'warning', 'fa fa-plus', '0', '', '', 1, 1)";
            if ($this->modularModel->sqlVarios($controlSql) === false) {
                $this->modularModel->rollBackTransaction();
                return false;
            }
            $controlSql = "Replace into nivelesboton (nombre, seccion_id, nivel, enlace, classbtn, classicon, orden, badge_color, badge_texto, titulo,nivelesboton_id) values ('borrar','" . $codigo_seccion['id'] . "', '9', 'borrar', 'danger', 'fa fa-trash', '3', '', '', 0, 1)";
            if ($this->modularModel->sqlVarios($controlSql) === false) {
                $this->modularModel->rollBackTransaction();
                return false;
            }
            $controlSql = "Replace into nivelesboton (nombre, seccion_id, nivel, enlace, classbtn, classicon, orden, badge_color, badge_texto, titulo,nivelesboton_id) values ('editar','" . $codigo_seccion['id'] . "', '9', 'editar', 'primary', 'fa fa-pencil', '0', '', '', 0, 1)";
            if ($this->modularModel->sqlVarios($controlSql) === false) {
                $this->modularModel->rollBackTransaction();
                return false;
            }
            $controlSql = "Replace into nivelesboton (nombre, seccion_id, nivel, enlace, classbtn, classicon, orden, badge_color, badge_texto, titulo,nivelesboton_id) values ('subir','" . $codigo_seccion['id'] . "', '9', 'subir', 'dark', 'fa fa-upload', '2', '', '', 0, 1)";
            if ($this->modularModel->sqlVarios($controlSql) === false) {
                $this->modularModel->rollBackTransaction();
                return false;
            }
        }
        $lista_botones = $this->modularModel->getRegistrosTabla('nivelesboton', 'nivel <= ' . $levelUser . ' and seccion_id = ' . $codigo_seccion['id'] . ' AND titulo = ' . $titulo, 'Orden', "A");

        $menu_botones = array();
        foreach ($lista_botones as $key => $value) {
            $boton = new stdClass();
            $boton->name = $value['nombre'];
            $boton->link = $value['enlace'];
            $boton->icon = $value['classicon'];
            $boton->class = $value['classbtn'];
            array_push($menu_botones, $boton);
        }
        $this->modularModel->commitTransaction();
        return $menu_botones;
    }
}
