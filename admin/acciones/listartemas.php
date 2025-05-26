<?php
	$themes = getAvailableThemes();
        $activeTheme = $db->querySingle("SELECT value FROM config WHERE key='active_theme'");
        $html = "<h2>Temas</h2>";
        if (empty($themes)) {
            $html .= "<p>No se encontraron temas en la carpeta css.</p>";
            renderAdmin($html);
            break;
        }
        $html .= "<table class='admin-table'>
                    <tr><th>Nombre del Tema</th><th>Activo</th><th>Acción</th></tr>";
        foreach ($themes as $tName) {
            $isActive = ($tName === $activeTheme) ? 'Sí' : 'No';
            $html .= "<tr>
                        <td>$tName</td>
                        <td>$isActive</td>
                        <td>";
            if ($isActive === 'No') {
                $html .= "<a href='?action=activate_theme&theme=$tName'>Activar</a>";
            } else {
                $html .= "Ya Está Activo";
            }
            $html .= "</td>
                      </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>
