<?php
	requireLogin();
        $id = $_GET['id'] ?? null;
        $heroData = [
            'id' => '',
            'page_slug' => '',
            'title' => '',
            'subtitle' => '',
            'background_image' => ''
        ];

        // Fetch existing if editing
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM heroes WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $res = $stmt->execute();
            $found = $res->fetchArray(SQLITE3_ASSOC);
            if ($found) {
                $heroData = $found;
            }
        }

        // Handle form submission
        if (isset($_POST['save_hero'])) {
            $page_slug       = trim($_POST['page_slug']);
            $title           = trim($_POST['title']);
            $subtitle        = trim($_POST['subtitle']);
            $backgroundImage = trim($_POST['background_image']);

            if ($id) {
                // Update existing
                $st = $db->prepare("UPDATE heroes
                                    SET page_slug = :page_slug,
                                        title = :title,
                                        subtitle = :subtitle,
                                        background_image = :bg
                                    WHERE id = :id");
                $st->bindValue(':id', $id, SQLITE3_INTEGER);
            } else {
                // Insert new
                $st = $db->prepare("INSERT INTO heroes (page_slug, title, subtitle, background_image)
                                    VALUES (:page_slug, :title, :subtitle, :bg)");
            }
            $st->bindValue(':page_slug', $page_slug, SQLITE3_TEXT);
            $st->bindValue(':title', $title, SQLITE3_TEXT);
            $st->bindValue(':subtitle', $subtitle, SQLITE3_TEXT);
            $st->bindValue(':bg', $backgroundImage, SQLITE3_TEXT);
            $st->execute();

            header('Location: ?action=list_heroes');
            exit();
        }

        // Render form
        $html = "<div class='admin-form'>
                    <h2>" . ($id ? "Editar Hero" : "Agregar Hero") . "</h2>
                    <form method='post'>
                        <label for='page_slug'>Page Slug (ej: 'blog', 'contacto', 'inicio', o el título exacto de la página):</label>
                        <input type='text' name='page_slug' id='page_slug' value='" . htmlspecialchars($heroData['page_slug']) . "' required>

                        <label for='title'>Título:</label>
                        <input type='text' name='title' id='title' value='" . htmlspecialchars($heroData['title']) . "' required>

                        <label for='subtitle'>Subtítulo:</label>
                        <input type='text' name='subtitle' id='subtitle' value='" . htmlspecialchars($heroData['subtitle']) . "'>

                        <label for='background_image'>URL de la imagen de fondo:</label>
                        <input type='text' name='background_image' id='background_image' value='" . htmlspecialchars($heroData['background_image']) . "'>

                        <button type='submit' name='save_hero'>Guardar</button>
                    </form>
                 </div>";
        renderAdmin($html);
?>
