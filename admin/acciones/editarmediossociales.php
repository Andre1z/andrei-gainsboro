<?php
	requireLogin();
        $id = $_GET['id'] ?? null;
        $socialMediaData = [
            'id' => '',
            'category' => '',
            'name' => '',
            'url' => '',
            'logo' => ''
        ];

        // Fetch existing if editing
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM social_media WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $res = $stmt->execute();
            $found = $res->fetchArray(SQLITE3_ASSOC);
            if ($found) {
                $socialMediaData = $found;
            }
        }

        // Handle form submission
        if (isset($_POST['save_social_media'])) {
            $category = trim($_POST['category']);
            $name = trim($_POST['name']);
            $url = trim($_POST['url']);
            $logo = trim($_POST['logo']);

            if ($id) {
                // Update existing
                $st = $db->prepare("UPDATE social_media
                                    SET category = :category,
                                        name = :name,
                                        url = :url,
                                        logo = :logo
                                    WHERE id = :id");
                $st->bindValue(':id', $id, SQLITE3_INTEGER);
            } else {
                // Insert new
                $st = $db->prepare("INSERT INTO social_media (category, name, url, logo)
                                    VALUES (:category, :name, :url, :logo)");
            }
            $st->bindValue(':category', $category, SQLITE3_TEXT);
            $st->bindValue(':name', $name, SQLITE3_TEXT);
            $st->bindValue(':url', $url, SQLITE3_TEXT);
            $st->bindValue(':logo', $logo, SQLITE3_TEXT);
            $st->execute();

            header('Location: ?action=list_social_media');
            exit();
        }

        // Render form
        $html = "<div class='admin-form'>
                    <h2>" . ($id ? "Editar Enlace de Red Social" : "Agregar Enlace de Red Social") . "</h2>
                    <form method='post'>
                        <label for='category'>Categoría:</label>
                        <input type='text' name='category' id='category' value='" . htmlspecialchars($socialMediaData['category']) . "' required>

                        <label for='name'>Nombre:</label>
                        <input type='text' name='name' id='name' value='" . htmlspecialchars($socialMediaData['name']) . "' required>

                        <label for='url'>URL:</label>
                        <input type='text' name='url' id='url' value='" . htmlspecialchars($socialMediaData['url']) . "' required>

                        <label for='logo'>Logo URL:</label>
                        <input type='text' name='logo' id='logo' value='" . htmlspecialchars($socialMediaData['logo']) . "' required>

                        <button type='submit' name='save_social_media'>Guardar</button>
                    </form>
                 </div>";
        renderAdmin($html);
?>
