<?php
$mediaItems = getAllMedia($db);
        $html = "<h2>Biblioteca</h2>
                 <p><a href='?action=upload_media'>[+] Subir Nuevo Archivo</a></p>
                 <table class='admin-table'>
                   <tr>
                     <th>ID</th>
                     <th>Nombre de Archivo</th>
                     <th>Ruta</th>
                     <th>Fecha</th>
                     <th>Vista Previa</th>
                     <th>Acciones</th>
                   </tr>";
        foreach ($mediaItems as $m) {
            $html .= "<tr>
                        <td>{$m['id']}</td>
                        <td>" . htmlspecialchars($m['filename']) . "</td>
                        <td>" . htmlspecialchars($m['filepath']) . "</td>
                        <td>" . htmlspecialchars($m['created_at']) . "</td>
                        <td><img src='{$m['filepath']}' alt='' style='max-width:100px;'></td>
                        <td>
                           <a href='?action=delete_media&id={$m['id']}' onclick='return confirm(\"¿Eliminar?\");'>Eliminar</a>
                        </td>
                      </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>
