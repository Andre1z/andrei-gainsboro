<?php
/**
 * Archivo: admin/acciones/listartemas.php
 *
 * Descripción:
 *   Permite al administrador ver la lista de temas disponibles (detectados en la carpeta "css")
 *   y cambiar el tema activo almacenado en la tabla de configuración.
 *
 *   Se asume que ya se ha iniciado la sesión y se ha cargado la conexión a la base de datos,
 *   y que la configuración actual está en la variable $config.
 */

// Incluir la configuración y la conexión a la base de datos
require_once '../config.php';

// Asegúrate de que la base de datos y la variable $config estén disponibles.
// Si en tu estructura ya se han incluido en un controlador global, estos require pueden no ser necesarios.

// Detecta los archivos de tema disponibles en la carpeta "css"
$themeFiles = glob(__DIR__ . '/../css/*.css');
$availableThemes = [];
if ($themeFiles) {
    foreach ($themeFiles as $themeFile) {
        // Usamos el nombre del archivo sin la extensión como identificador del tema
        $availableThemes[] = pathinfo($themeFile, PATHINFO_FILENAME);
    }
}

// Obtener el tema activo actual (por defecto 'gainsboro' si no está definido)
$currentTheme = $config['active_theme'] ?? 'gainsboro';

// Procesar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTheme = $_POST['theme'] ?? '';
    if (!empty($newTheme) && in_array($newTheme, $availableThemes)) {
        // Actualizamos la configuración para el tema activo en la base de datos
        $stmt = $db->prepare("UPDATE config SET value = :theme WHERE key = 'active_theme'");
        $stmt->bindValue(':theme', $newTheme, SQLITE3_TEXT);
        $stmt->execute();
        // También puedes actualizar la variable $config si se utiliza en el mismo request
        $config['active_theme'] = $newTheme;
        // Redirige para evitar reenviar el formulario al actualizar la página
        header('Location: admin.php?action=list_themes');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Tema - Panel Administrativo</title>
    <!-- Puedes incluir aquí la hoja de estilos del panel de administración -->
    <link rel="stylesheet" href="https://jocarsa.github.io/jocarsa-lightslateblue/jocarsa%20|%20lightslateblue.css">
    <style>
        body { padding: 20px; font-family: sans-serif; }
        form { margin-top: 20px; }
        label, select, button { font-size: 1rem; }
    </style>
</head>
<body>
    <h2>Seleccionar Tema</h2>
    <p>Elige uno de los temas disponibles para personalizar la apariencia de la aplicación.</p>
    <form action="admin.php?action=list_themes" method="POST">
        <label for="theme">Temas disponibles:</label>
        <select name="theme" id="theme">
            <?php foreach ($availableThemes as $theme): ?>
                <option value="<?php echo $theme; ?>" <?php echo ($theme === $currentTheme) ? 'selected' : ''; ?>>
                    <?php echo $theme; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Cambiar Tema</button>
    </form>
</body>
</html>