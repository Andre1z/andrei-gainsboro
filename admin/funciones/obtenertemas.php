<?php
function getAvailableThemes() {
    $themeFiles = glob('../css/*.css');
    $themes = [];
    if ($themeFiles !== false) {
        foreach ($themeFiles as $filePath) {
            $themes[] = pathinfo($filePath, PATHINFO_FILENAME);
        }
    }
    return $themes;
}
?>
