<?php
$dir = __DIR__ . '/uploads/';
$files = [];

if (is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif'])) {
            $files[] = 'uploads/' . $file;
        }
    }
}

header('Content-Type: application/json');
echo json_encode(array_reverse($files));
?>
