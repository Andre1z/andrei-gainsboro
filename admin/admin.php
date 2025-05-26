<?php
/**
 * Archivo: admin.php
 *
 * Descripción:
 *   Este archivo es el punto de entrada del panel administrativo del CMS.
 *   Se encarga de iniciar la sesión, establecer la conexión a la base de datos,
 *   inicializar las tablas necesarias, incluir las funciones auxiliares para
 *   autenticación y enrutar las diversas acciones administrativas definidas
 *   mediante el parámetro GET "action".
 *
 *   Tanto el título (en la etiqueta <title>) como el encabezado (<h1>) muestran
 *   el texto "andrei | gainsboro" de forma fija.
 *
 * @package CMS-ANDREI-ADMIN
 */

session_start();

// Se incluye la configuración (ruta de la base de datos, etc.)
require_once '../config.php';

// Inicializa la base de datos y crea las tablas necesarias.
include "inc/inicializarbasededatos.php";

// Inserta las redes sociales por defecto si aún no existen.
foreach ($defaultSocialMedia as $item) {
    list($category, $name, $logo) = $item;
    $existing = $db->querySingle("SELECT COUNT(*) FROM social_media WHERE name = '$name'");
    if ($existing == 0) {
        $db->exec("INSERT INTO social_media (category, name, url, logo) VALUES ('$category', '$name', '', '$logo')");
    }
}

// Se incluyen las funciones auxiliares necesarias.
include "funciones/comprobarlogin.php";      // Función isLoggedIn()
include "funciones/requerirlogin.php";        // Redirige al login si no se está autenticado
include "funciones/accionactual.php";         // Función para marcar la acción activa en la navegación
include "funciones/obtenermedios.php";        // Función para obtener medios
include "funciones/obtenertemas.php";         // Función para obtener los temas disponibles
include "funciones/activartema.php";          // Función para activar el tema seleccionado

/**
 * Función renderAdmin
 *
 * Renderiza la estructura completa del panel administrativo, incluyendo:
 * - La navegación lateral.
 * - El encabezado, que muestra "andrei | gainsboro" (tanto en la etiqueta <h1> como en el title).
 * - El contenido principal recibido.
 *
 * @param string $content Contenido HTML a mostrar en la sección principal.
 * @param bool   $showNav Indica si se debe mostrar la navegación lateral.
 */
function renderAdmin($content, $showNav = true) {
    echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>andrei | gainsboro</title>
    <link rel='stylesheet' href='admin.css'>
    <link rel='stylesheet' href='https://jocarsa.github.io/jocarsa-lightslateblue/jocarsa%20|%20lightslateblue.css'>
</head>
<body>
<div id='admin-container'>";

    if ($showNav) {
        echo "<div id='admin-sidebar'>
            <nav>
                <a href='?action=dashboard'" . accionActual($_GET['action'] ?? '', 'dashboard') . ">Inicio</a>
                <hr>
                <a href='?action=list_pages'" . accionActual($_GET['action'] ?? '', 'list_pages') . ">Páginas</a>
                <a href='?action=list_blog'" . accionActual($_GET['action'] ?? '', 'list_blog') . ">Blog</a>
                <a href='?action=list_media'" . accionActual($_GET['action'] ?? '', 'list_media') . ">Biblioteca</a>
                <a href='?action=list_heroes'" . accionActual($_GET['action'] ?? '', 'list_heroes') . ">Héroes</a>
                <a href='?action=list_social_media'" . accionActual($_GET['action'] ?? '', 'list_social_media') . ">Redes Sociales</a>
                <hr>
                <a href='?action=list_themes'" . accionActual($_GET['action'] ?? '', 'list_themes') . ">Temas</a>
                <a href='?action=edit_theme'" . accionActual($_GET['action'] ?? '', 'edit_theme') . ">Editar Tema</a>
                <a href='?action=list_custom_css'" . accionActual($_GET['action'] ?? '', 'list_custom_css') . ">CSS personalizado</a>
                <hr>
                <a href='?action=list_contact'" . accionActual($_GET['action'] ?? '', 'list_contact') . ">Contacto</a>
                <hr>
                <a href='?action=list_admins'" . accionActual($_GET['action'] ?? '', 'list_admins') . ">Administradores</a>
                <a href='?action=list_config'" . accionActual($_GET['action'] ?? '', 'list_config') . ">Configuración</a>
                <hr>
                <a href='?action=logout'" . accionActual($_GET['action'] ?? '', 'logout') . ">Salir</a>
            </nav>
        </div>";
    }

    echo "<div id='admin-content'>";
    if ($showNav) {
        echo "<div id='admin-header'>
            <img src='gainsboro.png' alt='URL de la imagen' style='width:50px; margin-right:20px;'>
            <h1>andrei | gainsboro</h1>
        </div>";
    }
    echo "<div class='admin-section'>
            $content
          </div>";
    echo "</div>
</div>
<script src='https://jocarsa.github.io/jocarsa-lightslateblue/jocarsa%20|%20lightslateblue.js'></script>
</body>
</html>";
}

// ---------------------------------------------------------------------
// Manejo de rutas y autenticación
// ---------------------------------------------------------------------
$action = $_GET['action'] ?? 'login';
$message = "";

// Si la acción es "logout", se cierra la sesión.
if ($action === 'logout') {
    include "rutas/cerrarsesion.php";
}

// Si la acción es "do_login", se procesa el inicio de sesión.
if ($action === 'do_login') {
    include "rutas/iniciarsesion.php";
}

// Si el usuario no está autenticado (excepto cuando se está en "login"), se fuerza el acceso al login.
if (!isLoggedIn() && $action !== 'login') {
    include "rutas/forzarlogin.php";
}

// ---------------------------------------------------------------------
// Enrutamiento de las acciones del panel administrativo
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
            header("Location: ?action=dashboard");
        } else {
            header("Location: ?action=login");
        }
        exit();
}
?>