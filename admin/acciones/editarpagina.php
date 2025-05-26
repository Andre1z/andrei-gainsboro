<?php
$id = $_GET['id'] ?? null;
$pageData = ['id' => '', 'title' => '', 'content' => '', 'parent_id' => null];
if ($id) {
    $st = $db->prepare("SELECT * FROM pages WHERE id = :id");
    $st->bindValue(':id', $id, SQLITE3_INTEGER);
    $res = $st->execute();
    $found = $res->fetchArray(SQLITE3_ASSOC);
    if ($found) {
        $pageData = $found;
    }
}
if (isset($_POST['save_page'])) {
    $title   = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $parent_id = $_POST['parent_id'] ?? '';
    $parent_id = ($parent_id === '') ? null : (int)$parent_id;
    if ($id) {
        $st = $db->prepare("UPDATE pages SET title = :title, content = :content, parent_id = :parent_id WHERE id = :id");
        $st->bindValue(':id', $id, SQLITE3_INTEGER);
    } else {
        $st = $db->prepare("INSERT INTO pages (title, content, parent_id) VALUES (:title, :content, :parent_id)");
    }
    $st->bindValue(':title', $title, SQLITE3_TEXT);
    $st->bindValue(':content', $content, SQLITE3_TEXT);
    if ($parent_id === null) {
        $st->bindValue(':parent_id', null, SQLITE3_NULL);
    } else {
        $st->bindValue(':parent_id', $parent_id, SQLITE3_INTEGER);
    }
    $st->execute();
    header('Location: ?action=list_pages');
    exit();
}

// Build options for parent page dropdown
$parentOptions = "<option value=''>-- Ninguno (página raíz) --</option>";
$res = $db->query("SELECT id, title FROM pages ORDER BY title ASC");
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    // Exclude the current page from being its own parent
    if (isset($pageData['id']) && $row['id'] == $pageData['id']) continue;
    $selected = ($pageData['parent_id'] == $row['id']) ? "selected" : "";
    $parentOptions .= "<option value='{$row['id']}' $selected>" . htmlspecialchars($row['title']) . "</option>";
}

$html = "<div class='admin-form'>
            <h2>" . ($id ? "Editar Página" : "Agregar Página") . "</h2>
            <form method='post'>
                <label>Título:</label>
                <input type='text' name='title' value='" . htmlspecialchars($pageData['title']) . "' required>
                <label>Contenido:</label>
                <textarea name='content' class='jocarsa-lightslateblue' rows='10'>" . htmlspecialchars($pageData['content']) . "</textarea>
                <label>Parent Page:</label>
                <select name='parent_id'>
                    $parentOptions
                </select>
                <button type='submit' name='save_page'>Guardar</button>
            </form>
         </div>";
renderAdmin($html);
?>

