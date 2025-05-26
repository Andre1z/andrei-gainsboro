<?php
function renderAdmin($content, $showNav = true) {
    $excludedActions = ['edit_theme', 'edit_custom_css'];
    $currentAction = $_GET['action'] ?? '';
    $useCustomClass = !in_array($currentAction, $excludedActions);

    echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>jocarsa | gainsboro</title>
    <link rel='stylesheet' href='admin.css'>
    " . ($useCustomClass ? "<link rel='stylesheet' href='https://jocarsa.github.io/jocarsa-lightslateblue/jocarsa%20%7C%20lightslateblue.css'>" : "") . "
</head>
<body>
<div id='admin-container'>";

    if ($showNav) {
        echo "<div id='admin-sidebar'>
            <nav>
                <a href='?action=dashboard'" . accionActual($_GET['action'] ?? '', "dashboard") . ">Inicio</a>
                <hr>
                <a href='?action=list_pages'" . accionActual($_GET['action'] ?? '', "list_pages") . ">Páginas</a>
                <a href='?action=list_blog'" . accionActual($_GET['action'] ?? '', "list_blog") . ">Blog</a>
                <a href='?action=list_media'" . accionActual($_GET['action'] ?? '', "list_media") . ">Biblioteca</a>
                <a href='?action=list_heroes'" . accionActual($_GET['action'] ?? '', "list_heroes") . ">Heroes</a>
                <a href='?action=list_social_media'" . accionActual($_GET['action'] ?? '', "list_social_media") . ">Redes Sociales</a>
                <hr>
                <a href='?action=list_themes'" . accionActual($_GET['action'] ?? '', "list_themes") . ">Temas</a>
                <a href='?action=edit_theme'" . accionActual($_GET['action'] ?? '', "edit_theme") . ">Editar Tema</a>
                <a href='?action=list_custom_css'" . accionActual($_GET['action'] ?? '', "list_custom_css") . ">CSS personalizado</a>
                <hr>
                <a href='?action=list_contact'" . accionActual($_GET['action'] ?? '', "list_contact") . ">Contacto</a>
                <hr>
                <a href='?action=list_admins'" . accionActual($_GET['action'] ?? '', "list_admins") . ">Administradores</a>
                <a href='?action=list_config'" . accionActual($_GET['action'] ?? '', "list_config") . ">Configuración</a>
                <hr>
                <a href='?action=logout'" . accionActual($_GET['action'] ?? '', "logout") . ">Salir</a>
            </nav>
        </div>";
    }

    echo "<div id='admin-content'>";

    if ($showNav) {
        echo "<div id='admin-header'>
            <img src='gainsboro.png'>
            <h1>jocarsa | gainsboro</h1>
        </div>";
    }

    echo "<div class='admin-section'>
            $content
        </div>";

    if ($showNav) {
       /* echo "<footer>
            &copy; " . date('Y') . " jocarsa | gainsboro
        </footer>";*/
    }

    echo "</div>
</div>
</body>
</html>";
}
?>
