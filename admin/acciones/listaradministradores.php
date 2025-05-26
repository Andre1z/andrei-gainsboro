<?php
	requireLogin();
        $res = $db->query("SELECT * FROM admins ORDER BY id DESC");
        $html = "<h2>Administradores</h2>
                 <p><a href='?action=edit_admin'>[+] Agregar Nuevo Administrador</a></p>
                 <table class='admin-table'>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Acciones</th>
                    </tr>";
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $html .= "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>
                            <a href='?action=edit_admin&id={$row['id']}'>Editar</a> |
                            <a href='?action=delete_admin&id={$row['id']}' onclick='return confirm(\"¿Eliminar?\");'>Eliminar</a>
                        </td>
                      </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>
