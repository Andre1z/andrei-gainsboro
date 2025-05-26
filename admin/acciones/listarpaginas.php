<?php
	$res = $db->query("SELECT * FROM pages ORDER BY id DESC");
        $html = "<h2>Páginas</h2>
                 <p><a href='?action=edit_page'>[+] Agregar Nueva Página</a></p>
                 <table class='admin-table'>
                    <tr><th>ID</th><th>Título</th><th>Acciones</th></tr>";
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $html .= "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>
                            <a href='?action=edit_page&id={$row['id']}'>Editar</a> |
                            <a href='?action=delete_page&id={$row['id']}' onclick='return confirm(\"¿Eliminar?\");'>Eliminar</a>
                        </td>
                      </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>
