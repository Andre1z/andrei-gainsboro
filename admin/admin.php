<?php
session_start();
require_once 'config.php';

// ---------------------------------------------------------------------
// Database Connection & Table Creation
// ---------------------------------------------------------------------
include "inc/inicializarbasededatos.php";

foreach ($defaultSocialMedia as $item) {
    list($category, $name, $logo) = $item;
    $existing = $db->querySingle("SELECT COUNT(*) FROM social_media WHERE name = '$name'");
    if ($existing == 0) {
        $db->exec("
            INSERT INTO social_media (category, name, url, logo)
            VALUES ('$category', '$name', '', '$logo')
        ");
    }
}

// ---------------------------------------------------------------------
// Helper Functions
// ---------------------------------------------------------------------
include "funciones/comprobarlogin.php";
include "funciones/requerirlogin.php";
include "funciones/accionactual.php";
include "funciones/renderadmin.php";
include "funciones/obtenermedios.php";
include "funciones/obtenertemas.php";
include "funciones/activartema.php";


// ---------------------------------------------------------------------
// Routing & Login Handling
// ---------------------------------------------------------------------
$action = $_GET['action'] ?? 'login';
$message = '';

// LOGOUT
if ($action === 'logout') {
    include "rutas/cerrarsesion.php";
}

// PROCESS LOGIN using the admins table
if ($action === 'do_login') {
    include "rutas/iniciarsesion.php";
}

// If user not logged in, force login (except for login action)
if (!isLoggedIn() && $action !== 'login') {
   include "rutas/forzarlogin.php";
}

// ---------------------------------------------------------------------
// SWITCH ACTIONS
// ---------------------------------------------------------------------
switch ($action) {

    case 'login':
        include "acciones/login.php";
        break;
    case 'dashboard':
        include "acciones/escritorio.php";
        break;
    case 'list_contact':
        include "acciones/listadecontactos.php";
        break;
    case 'view_contact':
        include "acciones/vermensaje.php";
        break;
    case 'list_media':
        include "acciones/libreriademedios.php";
        break;
    case 'upload_media':
        include "acciones/subirmedio.php";
        break;
    case 'delete_media':
        include "acciones/eliminarmedio.php";
        exit();
    case 'list_pages':
        include "acciones/listarpaginas.php";
        break;
    case 'edit_page':
        include "acciones/editarpagina.php";
        break;
    case 'delete_page':
        include "acciones/eliminarpagina.php";
        exit();
    case 'list_blog':
        include "acciones/listarentradasblog.php";
        break;
    case 'edit_blog':
        include "acciones/editarentradablog.php";
        break;
    case 'delete_blog':
        include "acciones/eliminarentradablog.php";
        exit();
    case 'list_themes':
        include "acciones/listartemas.php";
        break;
    case 'activate_theme':
        include "acciones/activartema.php";
        exit();
    case 'edit_theme':
        include "acciones/editartema.php";
        break;
    case 'list_config':
        include "acciones/listarconfiguracion.php";
        break;
    case 'list_heroes':
        include "acciones/listarheroes.php";
        break;
    case 'edit_hero':
        include "acciones/editarheroe.php";
        break;
    case 'delete_hero':
        include "acciones/eliminarheroe.php";
        exit();
    case 'list_social_media':
        include "acciones/listarmediossociales.php";
        break;
    case 'edit_social_media':
        include "acciones/editarmediossociales.php";
        break;
    case 'delete_social_media':
        include "acciones/eliminarmediossociales.php";
        exit();
    case 'list_custom_css':
        include "acciones/csspersonalizado.php";
        break;
    case 'edit_custom_css':
        include "acciones/editarcsspersonalizado.php";
        break;
    case 'activate_custom_css':
        include "acciones/activarcsspersonalizado.php";
        exit();
    case 'delete_custom_css':
        include "acciones/eliminarcsspersonalizado.php";
        exit();
    case 'list_admins':
        include "acciones/listaradministradores.php";
        break;
    case 'edit_admin':
        include "acciones/editaradministrador.php";
        break;
    case 'delete_admin':
        include "acciones/eliminaradministrador.php";
        exit();
    default:
        if (isLoggedIn()) {
            header('Location: ?action=dashboard');
        } else {
            header('Location: ?action=login');
        }
        exit();
}
?>

<link rel="stylesheet" href="https://jocarsa.github.io/jocarsa-lightslateblue/jocarsa%20%7C%20lightslateblue.css">
<script src="https://jocarsa.github.io/jocarsa-lightslateblue/jocarsa%20%7C%20lightslateblue.js"></script>

