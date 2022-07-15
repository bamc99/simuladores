<?php 
header("Access-Control-Allow-Origin: *");//{$_SERVER['HTTP_ORIGIN']}
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, Access-Control-Request-Method, X-Requested-With"); // X-API-KEY, 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 2400');
header("Content-Type: application/json; charset=utf-8");

date_default_timezone_set('America/Mexico_City');

require_once('controladores/Banorte.controlador.php');
require_once('controladores/Hsbc.controlador.php');
require_once('controladores/Scotiabank.controlador.php');
require_once('controladores/Santander.controlador.php');


$data = ['hola', 'mundo'];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if (isset($uri[2])) {
    switch ($uri[2]) {
        case 'banorte':
            $data = Banorte::ctrAdquisicionTradicional();
            break;
        case 'hsbc':
            $data = Hsbc::ctrAdquisicionTradicional();
            break;
        case 'scotiabank':
            $data = Scotiabank::ctrAdquisicionTradicional();
            break;
        case 'santander':
            $data = Santander::ctrAdquisicionTradicional();
            break;
        default:
            $data = [];
            break;
    }
}

echo json_encode($data);
?>