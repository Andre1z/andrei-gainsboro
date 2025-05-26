<?php
requireLogin();
        $id = $_GET['id'] ?? null;
        $adminData = ['id' => '', 'name' => '', 'email' => '', 'username' => '', 'password' => ''];
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM admins WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $res = $stmt->execute();
            $found = $res->fetchArray(SQLITE3_ASSOC);
            if ($found) {
                $adminData = $found;
            }
        }
        if (isset($_POST['save_admin'])) {
            $name     = $_POST['name'] ?? '';
            $email    = $_POST['email'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($id) {
                if (!empty($password)) {
                    $stmt = $db->prepare("UPDATE admins SET name = :name, email = :email, username = :username, password = :password WHERE id = :id");
                    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
                } else {
                    $stmt = $db->prepare("UPDATE admins SET name = :name, email = :email, username = :username WHERE id = :id");
                }
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            } else {
                $stmt = $db->prepare("INSERT INTO admins (name, email, username, password) VALUES (:name, :email, :username, :password)");
                $stmt->bindValue(':password', $password, SQLITE3_TEXT);
            }
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $stmt->execute();
            header('Location: ?action=list_admins');
            exit();
        }
        $html = "<div class='admin-form'>
                    <h2>" . ($id ? "Editar Administrador" : "Agregar Administrador") . "</h2>
                    <form method='post'>
                        <label>Nombre Completo:</label>
                        <input type='text' name='name' value='" . htmlspecialchars($adminData['name']) . "' required>
                        <label>Email:</label>
                        <input type='email' name='email' value='" . htmlspecialchars($adminData['email']) . "' required>
                        <label>Username:</label>
                        <input type='text' name='username' value='" . htmlspecialchars($adminData['username']) . "' required>
                        <label>" . ($id ? "Nueva Contraseña (dejar vacío para mantener la actual):" : "Contraseña:") . "</label>
                        <input type='password' name='password' value=''>
                        <button type='submit' name='save_admin'>Guardar</button>
                    </form>
                 </div>";
        renderAdmin($html);
?>
