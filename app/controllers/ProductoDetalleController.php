<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/ProductoModel.php';


class ProductoDetalleController extends Controller
{

    private $productoModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
    }

    public function index($categoria, $subcategoria, $id_producto)
    {

        // validar si es un numero
        if (!filter_var($id_producto, FILTER_VALIDATE_INT)) {
            include(__DIR__ . '/../views/page_404.php');
            exit;
        }

        // obtener datos del producto
        $id_usuario = isset($_SESSION['usuario']['id_us']) ? $_SESSION['usuario']['id_us'] : null;
        $producto = $this->productoModel->getById($id_producto, $id_usuario);


        if (!$producto || (self::slugify($producto['categoria']) != $categoria || self::slugify($producto['subcategoria']) != $subcategoria)) {
            include(__DIR__ . '/../views/page_404.php');
            exit;
        }

        // si el stock es menor a 1, mostrar "Agotado"
        $msgStock = $producto['stock_disponible'] < 1 ? 'Agotado' : $producto['stock_disponible'];

        $this->renderWithLayout('productos/productoSelecionado', [
            'producto' => $producto,
            'msgStock' => $msgStock
        ]);
    }

    // limpiar los nombres de la url dinamicas
    public static function slugify(string $text): string
    {
        // Convertir a minúsculas usando mb_string para soporte UTF-8
        $text = mb_strtolower(trim($text), 'UTF-8');

        // Reemplazar caracteres con tilde o especiales
        $text = strtr($text, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n',
            'Á' => 'a',
            'É' => 'e',
            'Í' => 'i',
            'Ó' => 'o',
            'Ú' => 'u',
            'Ñ' => 'n',
            'ä' => 'a',
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'o',
            'ü' => 'u',
            'Ä' => 'a',
            'Ë' => 'e',
            'Ï' => 'i',
            'Ö' => 'o',
            'Ü' => 'u',
            'ç' => 'c',
            'Ç' => 'c'
        ]);

        // Reemplazar cualquier cosa que no sea letra o número por un guion
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

        // Eliminar guiones repetidos y al inicio/final
        $text = preg_replace('/-+/', '-', $text);
        $text = trim($text, '-');

        return $text;
    }
}
