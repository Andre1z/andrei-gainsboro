<?php
	requireLogin();
        $res = $db->query("SELECT * FROM social_media ORDER BY id DESC");
        $html = "<h2>Redes Sociales</h2>
                 <p><a href='?action=edit_social_media'>[+] Agregar Nuevo Enlace</a></p>
                 <table class='admin-table'>
                    <tr>
                        <th>ID</th>
                        <th>Categoría</th>
                        <th>Nombre</th>
                        <th>URL</th>
                        <th>Logo</th>
                        <th>Acciones</th>
                    </tr>";
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $html .= "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['category']) . "</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['url']) . "</td>
                        <td><img src='img/" . htmlspecialchars($row['logo']) . "' alt='" . htmlspecialchars($row['name']) . "' style='max-width:50px;'></td>
                        <td>
                          <a href='?action=edit_social_media&id={$row['id']}'>Editar</a> |
                          <a href='?action=delete_social_media&id={$row['id']}' onclick='return confirm(\"¿Eliminar?\");'>Eliminar</a>
                        </td>
                      </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>
