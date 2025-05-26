<?php
	requireLogin();
        $id = $_GET['id'] ?? null;
        $cssData = ['id' => '', 'title' => '', 'content' => ''];
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM custom_css WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $res = $stmt->execute();
            $found = $res->fetchArray(SQLITE3_ASSOC);
            if ($found) {
                $cssData = $found;
            }
        }
        if (isset($_POST['save_custom_css'])) {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if ($id) {
                $stmt = $db->prepare("UPDATE custom_css SET title = :title, content = :content WHERE id = :id");
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            } else {
                $stmt = $db->prepare("INSERT INTO custom_css (title, content) VALUES (:title, :content)");
            }
            $stmt->bindValue(':title', $title, SQLITE3_TEXT);
            $stmt->bindValue(':content', $content, SQLITE3_TEXT);
            $stmt->execute();
            header('Location: ?action=list_custom_css');
            exit();
        }
        $html = "<div class='admin-form'>
                    <h2>" . ($id ? "Edit Custom CSS" : "Add New Custom CSS") . "</h2>
                    <form method='post'>
                        <label>Title:</label>
                        <input type='text' name='title' value='" . htmlspecialchars($cssData['title']) . "' required>
                        <label>CSS Content:</label>
                        <textarea name='content' rows='10'>" . htmlspecialchars($cssData['content']) . "</textarea>
                        <button type='submit' name='save_custom_css'>Save</button>
                    </form>
                 </div>";
        renderAdmin($html);
?>
