<?php
	function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ?action=login');
        exit();
    }
}
?>
