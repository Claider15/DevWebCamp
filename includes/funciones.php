<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function pagina_actual($path) {
    return str_contains($_SERVER['PATH_INFO'] ?? '/', $path) ? true : false;
}

function is_auth() : bool {
    if (!isset($_SESSION)) {
        session_start();
    }
    session_start();
    return isset($_SESSION['nombre']) && !empty($_SESSION);
}

function is_admin() : bool {
    if (!isset($_SESSION)) {
        session_start();
    }
    session_start();
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
}

function aos_animacion() : void {
    $efectos = ['fade-up', 'fade-down', 'fade-left', 'fade-right', 'flip-left', 'flip-right', 'zoom-in', 
    'zoom-in-up', 'zoom-in-down', 'zoom-out'];

    $efecto = array_rand($efectos, 1); //array_rand entrega una (o varias - números de las posiciones) posición aleatoria de un arreglo
    echo ' data-aos="' . $efectos[$efecto] . '" ';
}

