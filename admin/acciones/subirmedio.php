<?php
	if (isset($_POST['upload'])) {
            if (!empty($_FILES['file']['name'])) {
                $fileName = $_FILES['file']['name'];
                $tmpName  = $_FILES['file']['tmp_name'];
                $targetDir = '../static/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                // Create unique name
                $uniqueName = time() . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                $targetPath = $targetDir . $uniqueName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    // Save in DB
                    $dbFilePath = 'static/' . $uniqueName;
                    $stmt = $db->prepare("INSERT INTO media (filename, filepath) VALUES (:fn, :fp)");
                    $stmt->bindValue(':fn', $fileName, SQLITE3_TEXT);
                    $stmt->bindValue(':fp', $dbFilePath, SQLITE3_TEXT);
                    $stmt->execute();
                    header('Location: ?action=list_media');
                    exit();
                } else {
                    $message = "<p class='danger'>Error al mover el archivo subido.</p>";
                }
            } else {
                $message = "<p class='danger'>No se ha seleccionado ningún archivo.</p>";
            }
        } else {
            $message = '';
        }
        $html = "<div class='admin-form'>
                    <h2>Subir Nuevo Archivo</h2>
                    $message
                    <form method='post' enctype='multipart/form-data'>
                        <label>Selecciona el archivo:</label>
                        <input type='file' name='file'>
                        <button type='submit' name='upload'>Subir</button>
                    </form>
                 </div>";
        renderAdmin($html);
?>
