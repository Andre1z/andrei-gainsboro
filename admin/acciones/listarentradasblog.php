<?php
	$res = $db->query("SELECT * FROM blog ORDER BY id DESC");
        $html = "<h2>Entradas del Blog</h2>
                 <p><a href='?action=edit_blog'>[+] Agregar Nueva Entrada</a></p>
                 <table class='admin-table'>
                    <tr><th>ID</th><th>Título</th><th>Fecha</th><th>Acciones</th></tr>";
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $html .= "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                        <td>
                            <a href='?action=edit_blog&id={$row['id']}'>Editar</a> |
                            <a href='?action=delete_blog&id={$row['id']}' onclick='return confirm(\"¿Eliminar?\");'>Eliminar</a>
                        </td>
                      </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>
