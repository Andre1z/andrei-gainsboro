<?php
	$themeToActivate = $_GET['theme'] ?? '';
        $themes = getAvailableThemes();
        if (in_array($themeToActivate, $themes)) {
            setActiveTheme($db, $themeToActivate);
        }
        header('Location: ?action=list_themes');
?>
