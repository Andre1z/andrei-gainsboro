<?php
	$id = $_GET['id'] ?? 0;
        $stmt = $db->prepare("SELECT * FROM contact WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $res = $stmt->execute();
        $messageData = $res->fetchArray(SQLITE3_ASSOC);
        if (!$messageData) {
            header('Location: ?action=list_contact');
            exit();
        }
        $html = "<h2>Ver Mensaje</h2>
                 <table class='admin-table'>
                    <tr><th>ID</th><td>{$messageData['id']}</td></tr>
                    <tr><th>Nombre</th><td>" . htmlspecialchars($messageData['name']) . "</td></tr>
                    <tr><th>Correo Electrónico</th><td>" . htmlspecialchars($messageData['email']) . "</td></tr>
                    <tr><th>Asunto</th><td>" . htmlspecialchars($messageData['subject']) . "</td></tr>
                    <tr><th>Mensaje</th><td>" . nl2br(htmlspecialchars($messageData['message'])) . "</td></tr>
                    <tr><th>Creado</th><td>{$messageData['created_at']}</td></tr>
                  </table>";
        renderAdmin($html);
?>
