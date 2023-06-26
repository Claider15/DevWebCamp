<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Registro;

class DashboardController {

    public static function index(Router $router) {
        // Obtener los últimos registros
        $registros = Registro::get(5);
        foreach ($registros as $registro) {
            $registro->usuario = Usuario::find($registro->usuario_id);
        }

        // Calcular los registros
        $virtuales = Registro::total('paquete_id', 2);
        $presenciales = Registro::total('paquete_id', 1);

        $ingresos = ($virtuales * 46.41) + ($presenciales * 189.54);


        $router->render('/admin/dashboard/index', [
            'titulo' => 'Panel de Administración',
            'registros' => $registros,
            'ingresos' => $ingresos
        ]);
    }
}