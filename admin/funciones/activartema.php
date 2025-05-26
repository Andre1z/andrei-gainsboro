<?php
	function setActiveTheme($db, $themeName) {
    $st = $db->prepare("UPDATE config SET value = :val WHERE key = 'active_theme'");
    $st->bindValue(':val', $themeName, SQLITE3_TEXT);
    $st->execute();
}
?>
