<?php
	$id = $_GET['id'] ?? null;
        if ($id) {
            $st = $db->prepare("SELECT * FROM media WHERE id = :id");
            $st->bindValue(':id', $id, SQLITE3_INTEGER);
            $res = $st->execute();
            $media = $res->fetchArray(SQLITE3_ASSOC);
            if ($media) {
                $filePath = __DIR__ . '/' . $media['filepath'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $st2 = $db->prepare("DELETE FROM media WHERE id = :id");
                $st2->bindValue(':id', $id, SQLITE3_INTEGER);
                $st2->execute();
            }
        }
        header('Location: ?action=list_media');
?>
