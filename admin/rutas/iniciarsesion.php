<?php
	$user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt->bindValue(':username', $user, SQLITE3_TEXT);
    $res = $stmt->execute();
    $admin = $res->fetchArray(SQLITE3_ASSOC);
    if ($admin && $admin['password'] === $pass) {
        $_SESSION['logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: ?action=dashboard');
        exit();
    } else {
        $message = "<p class='danger'>Credenciales inválidas</p>";
        $action = 'login';
    }
?>
