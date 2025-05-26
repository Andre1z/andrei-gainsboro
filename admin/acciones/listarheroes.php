<?php
	requireLogin();
        $res = $db->query("SELECT * FROM heroes ORDER BY id DESC");
        $html = "<h2>Héroes (Hero Banners)</h2>
                 <p><a href='?action=edit_hero'>[+] Agregar Nuevo Hero</a></p>
                 <table class='admin-table'>
                    <tr>
                        <th>ID</th>
                        <th>Page Slug</th>
                        <th>Título</th>
                        <th>Subtítulo</th>
                        <th>Background Image</th>
                        <th>Acciones</th>
                    </tr>";
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $html .= "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['page_slug']) . "</td>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['subtitle']) . "</td>
                        <td>" . htmlspecialchars($row['background_image']) . "</td>
                        <td>
                          <a href='?action=edit_hero&id={$row['id']}'>Editar</a> |
                          <a href='?action=delete_hero&id={$row['id']}' onclick='return confirm(\"¿Eliminar?\");'>Eliminar</a>
                        </td>
                      </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>	
