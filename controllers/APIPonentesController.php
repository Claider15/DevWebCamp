<?php 

namespace Controllers;

use Model\Ponentes;

class APIPonentesController {

    public static function index() {
        $ponentes = Ponentes::all();
        echo json_encode($ponentes);
    }

    public static function ponente() {
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id || $id <1) {
            echo json_encode([]);
            return;
        }

        $ponente = Ponentes::find($id);
        echo json_encode($ponente, JSON_UNESCAPED_SLASHES);
    }
}