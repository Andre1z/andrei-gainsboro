<?php
	$id = $_GET['id'] ?? null;
        $blogData = ['id' => '', 'title' => '', 'content' => '', 'created_at' => ''];
        if ($id) {
            $st = $db->prepare("SELECT * FROM blog WHERE id = :id");
            $st->bindValue(':id', $id, SQLITE3_INTEGER);
            $res = $st->execute();
            $found = $res->fetchArray(SQLITE3_ASSOC);
            if ($found) {
                $blogData = $found;
            }
        }
        if (isset($_POST['save_blog'])) {
            $title   = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if ($id) {
                $st = $db->prepare("UPDATE blog SET title = :title, content = :content WHERE id = :id");
                $st->bindValue(':id', $id, SQLITE3_INTEGER);
            } else {
                $st = $db->prepare("INSERT INTO blog (title, content) VALUES (:title, :content)");
            }
            $st->bindValue(':title', $title, SQLITE3_TEXT);
            $st->bindValue(':content', $content, SQLITE3_TEXT);
            $st->execute();
            header('Location: ?action=list_blog');
            exit();
        }
        $html = "<div class='admin-form'>
                    <h2>" . ($id ? "Editar Entrada del Blog" : "Agregar Entrada") . "</h2>
                    <form method='post'>
                        <label>Título:</label>
                        <input type='text' name='title' value='" . htmlspecialchars($blogData['title']) . "' required>
                        <label>Contenido:</label>
                        <textarea name='content' class='jocarsa-lightslateblue' rows='10'>" . htmlspecialchars($blogData['content']) . "</textarea>
                        <button type='submit' name='save_blog'>Guardar</button>
                    </form>
                 </div>";
        renderAdmin($html);
?>
