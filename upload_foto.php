<?php
// Folder penyimpanan
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

// Batas ukuran file (10MB)
$maxSize = 10 * 1024 * 1024;

// Hanya gambar
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'Gagal upload file.']);
        exit;
    }

    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['error' => 'Ukuran file terlalu besar (maks. 10MB).']);
        exit;
    }

    if (!in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Tipe file tidak diizinkan.']);
        exit;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = uniqid('foto_', true) . '.' . $ext;
    $path = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        echo json_encode([
            'success' => true,
            'url' => 'uploads/' . $newName
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Gagal menyimpan file.']);
    }
}
?>
