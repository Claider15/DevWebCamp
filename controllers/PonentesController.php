<?php

namespace Controllers;

use Classes\Paginacion;
use MVC\Router;
use Model\Ponentes;
use Intervention\Image\ImageManagerStatic as Image;

class PonentesController {

    public static function index(Router $router) {
        if (!is_auth()) {
            header('Location: /login');
        }

        if (!is_admin()) {
            header('Location: /login');
        }
        
        $pagina_actual = $_GET['page'];
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        
        if (!$pagina_actual || $pagina_actual<1) {
            header('Location: /admin/ponentes?page=1');
        }

        $registros_por_pagina = 5;
        $total_registros = Ponentes::total();

        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total_registros);
        

        
        if ($paginacion->total_paginas() < $pagina_actual) {
            header('Location: /admin/ponentes?page=1');
        }

        $ponentes = Ponentes::paginar($registros_por_pagina, $paginacion->offset());


        

        $router->render('/admin/ponentes/index', [
            'titulo' => 'Ponentes / Conferencistas',
            'ponentes' => $ponentes,
            'paginacion' =>$paginacion->paginacion() 
        ]);
    }

    public static function crear(Router $router) {

        if (!is_auth()) {
            header('Location: /login');
        }
        
        if (!is_admin()) {
            header('Location: /login');
        }
        $alertas = [];
        $ponente = new Ponentes;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('Location: /login');
            }
            $ponente->sincronizar($_POST);
            if (!empty($_FILES['imagen']['tmp_name'])) {
                
                $carpeta_imagenes = '../public/img/speakers';

                if (!is_dir($carpeta_imagenes)) {
                    mkdir($carpeta_imagenes, 0755, true);
                }

                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('png', 80);// debe ser png porque nuestro diseño requiere cierta transparencia en el área de ponentes
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('webp', 80);

                $nombre_imagen = md5(uniqid(rand(), true));

                $_POST['imagen'] = $nombre_imagen;
            }

            $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);

            $ponente->sincronizar($_POST);

            // validar
            $alertas = $ponente->validar();

            // Guardar el registro
            if (empty($alertas)) {
                // Guardar las imágenes 
                $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.png');
                $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.webp');

                // Guardar en la BD
                $resultado = $ponente->guardar();

                if ($resultado) {
                    header('Location: /admin/ponentes?page=1');
                }
            }
        }

        $router->render('/admin/ponentes/crear', [
            'titulo' => 'Registrar Ponente',
            'alertas' => $alertas,
            'ponente' => $ponente ?? null,
            'redes' => json_decode($ponente->redes)
        ]);
    }

    public static function editar(Router $router) {

        if (!is_auth()) {
            header('Location: /login');
        }
        
        if (!is_admin()) {
            header('Location: /login');
        }
        $alertas = [];
        $ponente = '';

        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /admin/ponentes?page=1');
        }

        // Obtener ponente a editar
        $ponente = Ponentes::find($id);

        if (!$ponente) {
            header('Location: /admin/ponentes?page=1');
        }

        $ponente->imagen_actual = $ponente->imagen;

        $redes = json_decode($ponente->redes); // json_decode colvierte el string (convertido desde array por json_encode) en un objeto

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('Location: /login');
            }
            if (!empty($_FILES['imagen']['tmp_name'])) {
                
                $carpeta_imagenes = '../public/img/speakers';

                if (!is_dir($carpeta_imagenes)) {
                    mkdir($carpeta_imagenes, 0755, true);
                }

                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('png', 80);// debe ser png porque nuestro diseño requiere cierta transparencia en el área de ponentes
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('webp', 80);

                $nombre_imagen = md5(uniqid(rand(), true));

                $_POST['imagen'] = $nombre_imagen;

            } else {
                $_POST['imagen'] = $ponente->imagen_actual;
            }

            $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);
            $ponente->sincronizar($_POST);

            $alertas = $ponente->validar();

            if (empty($alertas)) {
                if (isset($nombre_imagen)) {
                    $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.png');
                    $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.webp');
                }
                $resultado = $ponente->guardar();

                if ($resultado) {
                    header('Location: /admin/ponentes?page=1');
                }
            }
        }

        $router->render('/admin/ponentes/editar', [
            'titulo' => 'Actualizar Ponente',
            'alertas' => $alertas,
            'ponente' => $ponente ?? null,
            'redes' => $redes
        ]);
    }

    public static function eliminar() {

        if (!is_auth()) {
            header('Location: /login');
        }
        
        if (!is_admin()) {
            header('Location: /login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!is_admin()) {
                header('Location: /login');
            }
            $id = $_POST['id'];
            $ponente = Ponentes::find($id);
            
            if (!isset($ponente)) {
                header('Location: /admin/ponentes?page=1');
            }
            
            $resultado = $ponente->eliminar();

            if ($resultado) {
                header('Location: /admin/ponentes?page=1');
            }
        }
    }
}