<?php
function limpiarTexto($texto) {
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

function esTutor() {
    return (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'tutor');
}

function esEstudiante() {
    return (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'estudiante');
}

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}
