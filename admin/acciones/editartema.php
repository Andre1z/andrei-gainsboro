<?php
	$themeName = $db->querySingle("SELECT value FROM config WHERE key='active_theme'");
        $themePath = '../css/' . $themeName . '.css';
        if (isset($_POST['save_theme'])) {
            $cssContent = $_POST['css_content'] ?? '';
            file_put_contents($themePath, $cssContent);
            $message = "<p class='success'>Tema actualizado correctamente.</p>";
        } else {
            $message = '';
        }
        $cssContent = file_get_contents($themePath);
        $html = "<div class='admin-form'>
                    <h2>Editar Tema: $themeName</h2>
                    $message
                    <form method='post'>
                        <label>Contenido CSS:</label>
                        <textarea name='css_content' rows='20'>" . htmlspecialchars($cssContent) . "</textarea>
                        <button type='submit' name='save_theme'>Guardar</button>
                    </form>
                 </div>";
        renderAdmin($html);
?>
