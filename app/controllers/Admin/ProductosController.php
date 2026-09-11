<?php

namespace Admin;

use ProductoModel;
use SubcategoriasModel;
use CategoriasModel;
use MarcasModel;
use ModelosModel;

require_once __DIR__ . '/../../models/ProductoModel.php';
require_once __DIR__ . '/../../models/SubcategoriasModel.php';
require_once __DIR__ . '/../../models/CategoriasModel.php';
require_once __DIR__ . '/../../models/MarcasModel.php';
require_once __DIR__ . '/../../models/ModelosModel.php';

class ProductosController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProductoModel();
    }

    // Mostrar todos los productos
    public function index()
    {
        // Parámetros de paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $search = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
        
        // Obtener productos paginados
        $productos = $this->model->getPaginated($page, $perPage, $search);
        
        // Obtener total de productos para paginación
        $totalProductos = $this->model->getTotalCount($search);
        
        // Calcular información de paginación
        $totalPages = ceil($totalProductos /perunet/ $perPage);
        $currentPage = max(1, min($page, $totalPages));
        
        // Datos para la vista
        $pagination = [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalProductos' => $totalProductos,
            'perPage' => $perPage,
            'hasNextPage' => $currentPage < $totalPages,
            'hasPrevPage' => $currentPage > 1,
            'nextPage' => $currentPage + 1,
            'prevPage' => $currentPage - 1
        ];
        
        $subcategorias = (new SubcategoriasModel())->getAll();
        $categorias = (new CategoriasModel())->getAll();
        $marcas = (new MarcasModel())->getAll();
        $modelos = (new ModelosModel())->getAll();
        
        require_once __DIR__ . '/../../views/admin/productos/index.php';
    }

    // Mostrar formulario de creación
    public function crear()
    {
        $subcategorias = (new SubcategoriasModel())->getAll();
        $categorias = (new CategoriasModel())->getAll();
        $marcas = (new MarcasModel())->getAll();
        $modelos = (new ModelosModel())->getAll();

        require_once __DIR__ . '/../../views/admin/productos/form.php';
    }

    // Guardar nuevo producto
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreImagen = null;

            // subir la imagen
            $nombreImagen = $this->subirImagen() ?? null;

            $data = [
                'nombre'          => $_POST['nombre'],
                'descripcion'     => $_POST['descripcion'],
                'precio'          => $_POST['precio'],
                'stock'           => $_POST['stock'],
                'id_subcategoria' => $_POST['id_subcategoria'],
                'id_marca'        => $_POST['id_marca'],
                'id_modelo'       => $_POST['id_modelo'],
                'imagen'          => $nombreImagen
            ];

            $this->model->create($data);
            header("Location: /perunet/admin/perunet/productos?mensaje=guardado");
            exit;
        }
    }

    // Mostrar formulario de edición
    public function editar($id)
    {
        $producto = $this->model->getById($id);

        $subcategorias = (new SubcategoriasModel())->getAllWithSubCategoria();
        $categorias = (new CategoriasModel())->getAll();
        $marcas = (new MarcasModel())->getAll();
        $modelos = (new ModelosModel())->getAll();

        require_once __DIR__ . '/../../views/admin/productos/form.php';
    }

    // Actualizar producto
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $nombreImagen = $_POST['imagen_actual'] ?? null;

            // subir la imagen
            $nombreImagen = $this->subirImagen() ?? null;

            $data = [
                'nombre'          => $_POST['nombre'],
                'descripcion'     => $_POST['descripcion'],
                'precio'          => $_POST['precio'],
                'stock'           => $_POST['stock'],
                'id_subcategoria' => $_POST['id_subcategoria'],
                'id_marca'        => $_POST['id_marca'],
                'id_modelo'       => $_POST['id_modelo'],
                'imagen'          => $nombreImagen
            ];

            $this->model->update($id, $data);
            header("Location: /perunet/admin/perunet/productos?mensaje=actualizado");
            exit;
        }
    }

    // Eliminar producto
    public function eliminar($id)
    {
        $this->model->delete($id);
        header("Location: /perunet/admin/perunet/productos?mensaje=eliminado");
        exit;
    }

    public function subirImagen()
    {
        if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
            $nombreTemporal = $_FILES['imagen_file']['tmp_name'];
            $nombreArchivo = basename($_FILES['imagen_file']['name']);
            
            // ruta relativa a la carpeta uploads
            $carpetaRelativa = '/perunet/public/img/uploads/';
            $carpetaAbsoluta = __DIR__ . '/../../..' . $carpetaRelativa;
            
            // crear la carpeta uploads si no existe
            if (!is_dir($carpetaAbsoluta)) {
                if (!mkdir($carpetaAbsoluta, 0755, true)) {
                    die('Error: No se pudo crear el directorio de subidas');
                }
            }
            $rutaDestino = $carpetaAbsoluta . $nombreArchivo;
            
            // revisala si es una imagen el archivo
            $check = getimagesize($nombreTemporal);
            if($check === false) {
                die('El archivo no es una imagen');
            }

            // sube el archivo a la carpeta uploads
            if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
                $nombreImagen = 'uploads/perunet/' . $nombreArchivo;
            } else {
                die('Error al subir el archivo. Por favor, inténtalo de nuevo.');
            }
        }
        return $nombreImagen;
    }
}
