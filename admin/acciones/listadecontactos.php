<?php
	$res = $db->query("SELECT * FROM contact ORDER BY id DESC");
        $html = "<h2>Contacto</h2>
                 <p><a href='?action=view_contact'>Ver Mensajes</a></p>
                 <table class='admin-table'>
                   <tr>
                     <th>ID</th>
                     <th>Nombre</th>
                     <th>Correo Electrónico</th>
                     <th>Asunto</th>
                     <th>Fecha</th>
                     <th>Acciones</th>
                   </tr>";
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $html .= "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['subject']) . "</td>
                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                        <td><a href='?action=view_contact&id={$row['id']}'>Ver</a></td>
                      </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>
