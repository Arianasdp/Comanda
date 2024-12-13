<?php

require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $sector = $parametros['sector'];
        $stock = $parametros['stock'];
        $precio = $parametros['precio'];

        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->sector = $sector;
        $producto->stock = $stock;
        $producto->precio = $precio;

        try 
        {
            $id_nuevo_producto = $producto->crearProducto();
            $payload = json_encode(array("mensaje" => "Producto creado con éxito", "id_producto" => $id_nuevo_producto));
            $response = $response->withStatus(201);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al crear el producto", "error" => $e->getMessage()));
            $response = $response->withStatus(500);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {

        $id = $args['id'];

        try 
        {
            $producto = Producto::obtenerProducto($id);

            if ($producto) 
            {
                $payload = json_encode($producto);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else 
            {
                $payload = json_encode(array("mensaje" => "Producto no encontrado"));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->write($payload);
            }
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al obtener el producto: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['id'], $parametros['nombre'], $parametros['sector'], $parametros['stock'], $parametros['precio'])) 
        {
            
            $id = $parametros['id'];
            $nombre = $parametros['nombre'];
            $sector = $parametros['sector'];
            $stock = $parametros['stock'];
            $precio = $parametros['precio'];

            try {
                Producto::modificarProducto($id, $nombre, $sector, $stock, $precio);
                $payload = json_encode(array("mensaje" => "Producto correctamente modificado"));

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } catch (Exception $e) 
            {
                $payload = json_encode(array("mensaje" => "Error al modificar el producto: " . $e->getMessage()));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
            }
        }
        else
        {
            if(isset($parametros['id']))
            {
                $id = $parametros['id'];

                try {
                    Producto::modificarStock($id);
                    $payload = json_encode(array("mensaje" => "Stock correctamente modificado"));
    
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                } catch (Exception $e) 
                {
                    $payload = json_encode(array("mensaje" => "Error al modificar el stock del producto: " . $e->getMessage()));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
                }

            }
        }

    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];

        try 
        {
            $resultado = Producto::borrarProducto($id);

            if ($resultado) 
            {
                $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
            } else 
            {
                $payload = json_encode(array("mensaje" => "No se encontro producto con ese id"));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al intentar borrar el producto: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function DescargarProductos($request, $response, $args)
    {
        
        $productos = Producto::obtenerTodos();  

        $csvFile = fopen('php://output', 'w');

        $response = $response->withHeader('Content-Type', 'text/csv')->withHeader('Content-Disposition', 'attachment; filename="productos.csv"');
        fputcsv($csvFile, ['id', 'nombre', 'sector', 'stock', 'precio']);

        foreach ($productos as $producto) 
        {
            fputcsv($csvFile, [
                $producto->id,         
                $producto->nombre,       
                $producto->sector,     
                $producto->stock,       
                $producto->precio
            ]);
        }

        fclose($csvFile);

        return $response;
    }

    public static function CargarProductos($request, $response)
    {
        $archivoCSV = "csv/Productos.csv";

        if (!file_exists($archivoCSV) || !is_readable($archivoCSV)) 
        {
            $payload = json_encode(array("mensaje" => "No se encontró el archivo CSV o no es legible"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        if (($gestor = fopen($archivoCSV, "r")) !== false) 
        {
           
            $encabezados = fgetcsv($gestor);

            while (($fila = fgetcsv($gestor)) !== false) 
            {
                $productoDatos = array_combine($encabezados, $fila);
                
                $producto = new Producto();
                $producto->nombre = $productoDatos['nombre'];
                $producto->sector = $productoDatos['sector'];
                $producto->stock = $productoDatos['stock'];
                $producto->precio = $productoDatos['precio'];
                
                $producto->crearProducto();
            }

            fclose($gestor);
        }

        $payload = json_encode(array("mensaje" => "Productos agregados con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}