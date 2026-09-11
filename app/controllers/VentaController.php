<?php


// falta implementar - implementar
class VentaController
{
    private $model;
    private $detalleCarrito;
    private $metodoPago;
    private $usuario;
    private $sucursal;

    public function __construct()
    {
        $this->model = new VentaModel();
        $this->detalleCarrito = new DetalleCarrito();
        $this->metodoPago = new MetodoPagoModel();
        $this->usuario = new UsuarioModel();
        $this->sucursal = new SucursalModel();
    }

    public function index()
    {

        // verificar si el carrito esta vacio
        $empty_cart = $this->detalleCarrito->isEmpty($_SESSION['usuario']['id_us']);

        if ($empty_cart) {
            $_SESSION['mensaje'] = "El carrito esta vacio.";
            header("Location: /perunet/carrito");
            exit();
        }

        try {
            $usuarioId = isset($_SESSION['usuario']['id_us']) ? $_SESSION['usuario']['id_us'] : null;
            if ($usuarioId === null) {
                // Manejar el caso donde no hay usuario logueado
                $usuario = null;
            } else {
                $usuario = $this->usuario->getById($usuarioId);
            }
            $metodos = $this->metodoPago->getAll();
            $sucursales = $this->sucursal->getAll();
        } catch (Exception $e) {
            $usuario = null;
            $metodos = [];
            $sucursales = [];
            // Aquí podrías loguear el error o mostrar un mensaje
        }
        require_once __DIR__ . '/../views/ventas/index.php';
    }
}
