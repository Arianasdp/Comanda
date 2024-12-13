<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/Logger.php';
require_once './middlewares/FiltrarPorRol.php';
require_once './middlewares/ActualizarTiempoPedido.php';
require_once './middlewares/VerificarRol.php';
require_once './middlewares/ActualizarPrecioPedido.php';
require_once './middlewares/VerificarPedidoListo.php';
require_once './middlewares/CalcularGanancias.php';
require_once './middlewares/ActualizarStock.php';
require_once './middlewares/DatosSeteadosPost.php';
require_once './middlewares/ActualizarMesaServida.php';
require_once './middlewares/ManejarAsistencias.php';
require_once './middlewares/RegistrarOperacion.php';
require_once './middlewares/ValidarEncuesta.php';
require_once './middlewares/ValidarRol.php';
require_once './middlewares/ValidarPedidoYProducto.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/OperacionController.php';
require_once './controllers/AsistenciaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/Pedido_productoController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/PdfController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

$loggerMiddleware = new LoggerMiddleware();
$filtroPorRolMiddleware = new FiltrarPorRolMiddleware();
$verificarAdminMiddleware = new VerificarRolMiddleware('socio');
$verificarMozoMiddleware = new VerificarRolMiddleware('mozo');
$verificarEmpleadoMiddleware = new VerificarRolMiddleware(['cocinero', 'cervecero', 'bartender']);
$verificarClienteMiddleware = new VerificarRolMiddleware('cliente');
$actualizarTiempoEstimadoMiddleware = new ActualizarTiempoPedidoMiddleware();
$actualizarPrecioPedidoMiddleware = new ActualizarPrecioPedidoMiddleware();
$verificarPedidoListoMiddleware = new VerificarPedidoListoMiddleware();
$calcularGananciasMiddleware = new CalcularGananciasMiddleware();
$actualizarStockMiddleware = new ActualizarStockMiddleware();
$actualizarMesaServidaMiddleware = new ActualizarMesaServidaMiddleware();
$datosSeteadosEncuestaMiddleware = new DatosSeteadosPostMiddleware(["id_pedido", "codigo_mesa", "mesa", "restaurante", "mozo", "cocinero", "experiencia"]);
$manejarAsistenciasMiddleware = new ManejarAsistenciasMiddleware(['cocinero', 'cervecero', 'bartender', 'mozo']);
$registrarOperacionMiddleware = new RegistrarOperacionMiddleware();
$validarEncuestaMiddleware = new ValidarEncuestaMiddleware();
$validarRolMiddleware = new ValidarRolMiddleware(['cocinero', 'cervecero', 'bartender', 'mozo']);
$validarPedidoYProductoMiddleware = new ValidarPedidoYProductoMiddleware();

//mi conexión
//php -S localhost:666 -t app

//login
$app->post('/login', \UsuarioController::class . ':login')->add($manejarAsistenciasMiddleware);

//Mozo
$app->group('/mozo', function (RouteCollectorProxy $group) use ($actualizarPrecioPedidoMiddleware, $calcularGananciasMiddleware, $actualizarStockMiddleware, $actualizarMesaServidaMiddleware, $validarPedidoYProductoMiddleware) {
  $group->get('/mesalibre', \MesaController::class . ':TraerUno');
  $group->put('/mesa/{id}', \MesaController::class . ':ModificarUno');
  $group->post('/pedido', \PedidoController::class . ':CargarUno');
  $group->get('/productos', \ProductoController::class . ':TraerTodos');
  $group->post('/producto', \Pedido_productoController::class . ':CargarUno')->add($actualizarPrecioPedidoMiddleware)->add($actualizarStockMiddleware)->add($validarPedidoYProductoMiddleware);
  $group->get('/pedidos/listos', \PedidoController::class . ':TraerListos');
  $group->put('/pedido/{id}', \PedidoController::class . ':Servido')->add($actualizarMesaServidaMiddleware);
  $group->put('/cobro/mesa/{id}', \MesaController::class . ':ModificarUno')->add($calcularGananciasMiddleware);
})->add($registrarOperacionMiddleware)->add($verificarMozoMiddleware)->add($loggerMiddleware);

//empleados(cocinero, bartender, cervecero)
$app->group('/empleado', function (RouteCollectorProxy $group) use ($filtroPorRolMiddleware, $actualizarTiempoEstimadoMiddleware, $verificarPedidoListoMiddleware) 
{
  $group->get('[/]', \Pedido_productoController::class . ':TraerTodos')->add($filtroPorRolMiddleware);
  $group->put('/pedido/{id}', \PedidoController::class . ':ModificarUno')->add($verificarPedidoListoMiddleware);
  $group->put('/producto/{id}', \Pedido_productoController::class . ':ModificarUno')->add($actualizarTiempoEstimadoMiddleware)->add([$filtroPorRolMiddleware, 'AsignarPorRol']);
})->add($registrarOperacionMiddleware)->add($verificarEmpleadoMiddleware)->add($loggerMiddleware);

//cliente
$app->group('/cliente', function (RouteCollectorProxy $group) use ($datosSeteadosEncuestaMiddleware, $validarEncuestaMiddleware) {
  $group->get('/pedido/{id}', \PedidoController::class . ':TraerTiempoRestante');
  $group->post('/encuesta', \EncuestaController::class . ':CargarUno')->add($validarEncuestaMiddleware)->add($datosSeteadosEncuestaMiddleware);
})->add($verificarClienteMiddleware)->add($loggerMiddleware);

//admin
$app->group('/admin', function (RouteCollectorProxy $group) use ($validarRolMiddleware) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno');
    $group->delete('/despedir/{id}', \UsuarioController::class . ':BorrarUno')->add($validarRolMiddleware);
    $group->get('/pedido/{id}', \PedidoController::class . ':TraerTiempoRestante');
    $group->get('/pedidos/tarde', \PedidoController::class . ':TraerPedidosEntregadosTarde');
    $group->get('/pedidos/estado', \PedidoController::class . ':TraerEstadoDePedidos');
    $group->get('/mesas/estado', \MesaController::class . ':TraerEstadoDeMesas');
    $group->get('/encuestas/comentarios', \EncuestaController::class . ':TraerMejoresComentarios');
    $group->get('/mesa/usos', \PedidoController::class . ':TraerMesaMasUsada');
    $group->put('/mesa/{id}', \MesaController::class . ':CerrarMesa');
    $group->get('/logo/pdf', \PdfController::class . ':DescargarPDF');
    $group->get('/pedidos/estadisticas', \PedidoController::class . ':TraerEstadisticas');
    $group->get('/productos/descargar/csv', \ProductoController::class . ':DescargarProductos');
    $group->get('/productos/cargar/csv', \ProductoController::class . ':CargarProductos');
})->add($verificarAdminMiddleware)->add($loggerMiddleware); 

//sprint 1
$app->group('/sprint', function (RouteCollectorProxy $group) 
{
  $group->group('/cargar', function (RouteCollectorProxy $group) {
    $group->post('/usuario', \UsuarioController::class . ':CargarUno');
    $group->post('/producto', \ProductoController::class . ':CargarUno');
    $group->post('/mesa', \MesaController::class . ':CargarUno');
    $group->post('/pedido', \PedidoController::class . ':CargarUno');
  });
  $group->group('/listar', function (RouteCollectorProxy $group) {
    $group->get('/usuarios', \UsuarioController::class . ':TraerTodos');
    $group->get('/productos', \ProductoController::class . ':TraerTodos');
    $group->get('/mesas', \MesaController::class . ':TraerTodos');
    $group->get('/pedidos', \PedidoController::class . ':TraerTodos');
  });
});

//probando conexión
$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
