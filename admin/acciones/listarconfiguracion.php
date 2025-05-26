<?php
	if (isset($_POST['save_config'])) {
            foreach ($_POST['config'] as $k => $v) {
                $st = $db->prepare("UPDATE config SET value = :val WHERE key = :key");
                $st->bindValue(':val', $v, SQLITE3_TEXT);
                $st->bindValue(':key', $k, SQLITE3_TEXT);
                $st->execute();
            }
            $message = "<p class='success'>Configuración actualizada.</p>";
        } else {
            $message = '';
        }
        $configs = $db->query("SELECT * FROM config ORDER BY id ASC");
        $html = "<h2>Configuración del Sitio</h2>
                 $message
                 <form method='post'>
                 <table class='admin-table'>
                 <tr><th>Clave</th><th>Valor</th></tr>";
        while ($row = $configs->fetchArray(SQLITE3_ASSOC)) {
            $key = htmlspecialchars($row['key']);
            $val = htmlspecialchars($row['value']);
            $html .= "<tr>
                        <td>$key</td>
                        <td><input type='text' name='config[$key]' value='$val'></td>
                      </tr>";
        }
        $html .= "</table>
                  <button type='submit' name='save_config'>Guardar</button>
                  </form>";
        renderAdmin($html);
?>
