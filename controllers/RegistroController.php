<?php

namespace Controllers;

use Model\Dia;
use Model\Hora;
use MVC\Router;
use Model\Evento;
use Model\Usuario;
use Model\Paquetes;
use Model\Ponentes;
use Model\Registro;
use Model\Categoria;
use Classes\Paginacion;
use Intervention\Image\ImageManagerStatic as Image;
use Model\EventosRegistros;
use Model\Regalo;

class RegistroController {

    public static function crear(Router $router) {

        if (!is_auth() ) {
            header('Location: /');
            return;
        }

        // Verificar si el Usuario ya está registrado
        $registro = Registro::where('usuario_id', $_SESSION['id']);

        if (isset($registro) && ($registro->paquete_id === '3' || $registro->paquete_id === '2')) {
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        if (isset($registro) && $registro->paquete_id === '1') {
            header('Location: /finalizar-registro/conferencias');
            return;
        }

        $router->render('registro/crear', [
            'titulo' => 'Finalizar Registro'
        ]);
    }

    public static function gratis() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_auth()) {
                header('Location: /login');
                return;
            }

            // Verificar si el Usuario ya está registrado
            $registro = Registro::where('usuario_id', $_SESSION['id']);

            if (isset($registro) && $registro->paquete_id === '3') {
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }

            $token = substr(md5(uniqid(rand(), true)), 0, 8);

            // Crear Registro
            $datos = array(
                'paquete_id' => 3,
                'pago_id' => '',
                'token' => $token,
                'usuario_id' => $_SESSION['id']
            );

            $registro = new Registro($datos);
            $resultado = $registro->guardar();

            if ($resultado) {
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }
        }
    }

    public static function boleto(Router $router) {

        // Validar la URL
        $id = $_GET['id'];

        if (!$id || !strlen($id) === 8) {
            header('Location: /');
            return;
        }

        // Buscar eltoken en la BD
        $registro = Registro::where('token', $id);
        if (!$registro) {
            header('Location: /');
            return;
        }

        // Llenar las tablas de referencia
        $registro->usuario = Usuario::find($registro->usuario_id);
        $registro->paquete = Paquetes::find($registro->paquete_id);

        $router->render('registro/boleto', [
            'titulo' => 'Asistencia a DevWebcamp',
            'registro' => $registro
        ]);
    }

    public static function pagar() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_auth()) {
                header('Location: /login');
                return;
            }

            // Validar que post no venga vacío
            if (empty($_POST)) {
                echo json_encode([]);
                return; // para que no se modifique nuestra BD si viene un $_POST vacío
            }

            // Crear el Registro
            $datos = $_POST;
            $datos['token'] = substr(md5(uniqid(rand(), true)), 0, 8);
            $datos['usuario_id'] = $_SESSION['id'];

            try {
                $registro = new Registro($datos);
                $resultado = $registro->guardar();
                echo json_encode($resultado); // al tener este json_encode es lo que se retorna a crear.php (y va a ser el console.log de ese resultado)
            } catch (\Throwable $th) {
                echo json_encode([ 
                    'resultado' => 'error'
                ]);
            }

            if ($resultado) {
                header('Location: /boleto?id=' . urlencode($registro->token));
            }
        }
    }

    public static function conferencias(Router $router) {

        if (!is_auth()) {
            header('Location: /login');
            return;
        }

        // Validar que el Usuario tenga el plan presencial
        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', $usuario_id);

        if (isset($registro) && $registro->paquete_id === "2") {
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }
        
        if ($registro->paquete_id !== "1") {
            header('Location: /');
            return;
        }

        // Redireccionar a boleto virtual en caso de haber finalizado su registro
        if (isset($registro->regalo_id) && $registro->paquete_id === "1") {
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formateados = [];
        foreach ($eventos as $evento) {
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponentes::find($evento->ponente_id);

            if ($evento->dia_id === "1" && $evento->categoria_id === "1") {
                $eventos_formateados['conferencias_v'][] = $evento;
            }
        }

        foreach ($eventos as $evento) {
            if ($evento->dia_id === "2" && $evento->categoria_id === "1") {
                $eventos_formateados['conferencias_s'][] = $evento;
            }
        }

        foreach ($eventos as $evento) {
            if ($evento->dia_id === "1" && $evento->categoria_id === "2") {
                $eventos_formateados['workshops_v'][] = $evento;
            }
        }

        foreach ($eventos as $evento) {
            if ($evento->dia_id === "2" && $evento->categoria_id === "2") {
                $eventos_formateados['workshops_s'][] = $evento;
            }
        }

        $regalos = Regalo::all('ASC');

        // Manejando el registro mediante el $_POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Revisar que el Usuario esté autenticado
            if (!is_auth()) {
                header('Location: /login');
                return;
            }

            $eventos = explode(',', $_POST['eventos']);

            if (empty($eventos)) {
                echo json_encode(['resultado' => false]); // si no hay eventos, retornamos un resultado con false
                return;
            }

            // Obtener el registro de Usuario
            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if (!isset($registro) || $registro->paquete_id !== '1') {
                echo json_encode(['resultado' => false]); // si no hay un usuario con la inf proporcionada, retornamos un resultado con false
                return;
            }

            
            $eventos_array = [];
            // Validar la disponibilidad de los eventos seleccionados
            foreach ($eventos as $evento_id) {
                $evento = Evento::find($evento_id);
                
                // Comprobar que el evento exista
                if (!isset($evento) || $evento->disponibles === '0') {
                    echo json_encode(['resultado' => false]);
                    return;
                }

                $eventos_array[] = $evento; // si llega hasta acá, quiere decir que es un evento que existe y que hay lugares disponibles
            }

            foreach ($eventos_array as $evento) {
                $evento->disponibles -= 1;
                $evento->guardar();

                // Almacenar el registro
                $datos = [
                    'evento_id' => (int) $evento->id,
                    'registro_id' => (int) $registro->id
                ];

                $registro_usuario = new EventosRegistros($datos);
                $registro_usuario->guardar();
            }

            // Almacenar el regalo
            $registro->sincronizar(['regalo_id' => $_POST['regalo_id']]);
            $resultado = $registro->guardar();

            if ($resultado) {
                echo json_encode([
                    'resultado' => $resultado,
                    'token' => $registro->token
                ]);
            } else {
                echo json_encode(['resultado' => false]);
            }
            return;
        }

        $router->render('registro/conferencias', [
            'titulo' => 'Elige Workshops y Conferencias',
            'eventos' => $eventos_formateados,
            'regalos' => $regalos
        ]);
    }
}