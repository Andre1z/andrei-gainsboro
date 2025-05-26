<?php
    requireLogin();
    $id = $_GET['id'] ?? null;
    if ($id) {
        // Retrieve the current active status
        $stmt = $db->prepare("SELECT active FROM custom_css WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $currentActive = $row ? $row['active'] : 0;
        $newStatus = ($currentActive == 1) ? 0 : 1;
        
        // Update with the toggled status
        $stmt = $db->prepare("UPDATE custom_css SET active = :newStatus WHERE id = :id");
        $stmt->bindValue(':newStatus', $newStatus, SQLITE3_INTEGER);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
    }
    header('Location: ?action=list_custom_css');
?>

