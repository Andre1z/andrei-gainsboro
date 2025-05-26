<?php
	requireLogin();
        $res = $db->query("SELECT * FROM custom_css ORDER BY id DESC");
        $html = "<h2>Custom CSS Rulesets</h2>
                 <p><a href='?action=edit_custom_css'>[+] Add New Custom CSS</a></p>
                 <table class='admin-table'>
                   <tr>
                     <th>ID</th>
                     <th>Title</th>
                     <th>Active</th>
                     <th>Created At</th>
                     <th>Actions</th>
                   </tr>";
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $active = $row['active'] ? 'Yes' : 'No';
            $html .= "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>$active</td>
                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                        <td>
                          <a href='?action=edit_custom_css&id={$row['id']}'>Edit</a> |
                          <a href='?action=delete_custom_css&id={$row['id']}' onclick='return confirm(\"Delete?\");'>Delete</a>";
            if (!$row['active']) {
                $html .= " | <a href='?action=activate_custom_css&id={$row['id']}'>Activate</a>";
            }
            $html .= "</td>
                     </tr>";
        }
        $html .= "</table>";
        renderAdmin($html);
?>
