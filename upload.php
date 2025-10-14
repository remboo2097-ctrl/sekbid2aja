<?php
session_start();
require 'config.php';

$messages = [];
$jsonIndex = UPLOAD_DIR . '/index.json';

if (isset($_POST['admin_login'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['is_admin'] = true;
    } else {
        $messages[] = "❌ Password salah.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: upload.php');
    exit;
}

function createImageThumb($src, $dest, $width = 400) {
    [$origW, $origH, $type] = getimagesize($src);
    $ratio = $origH / $origW;
    $newW = $width;
    $newH = $width * $ratio;
    $thumb = imagecreatetruecolor($newW, $newH);
    switch ($type) {
        case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($src); break;
        case IMAGETYPE_PNG: $image = imagecreatefrompng($src); break;
        case IMAGETYPE_GIF: $image = imagecreatefromgif($src); break;
        default: return false;
    }
    imagecopyresampled($thumb, $image, 0,0,0,0, $newW,$newH,$origW,$origH);
    imagejpeg($thumb, $dest, 85);
    imagedestroy($thumb);
    imagedestroy($image);
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files']) && isset($_SESSION['is_admin'])) {
    $files = $_FILES['files'];
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        $origName = $files['name'][$i];
        $size = $files['size'][$i];
        $tmp = $files['tmp_name'][$i];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($ext, $GLOBALS['ALLOWED_EXT'])) {
            $messages[] = "❌ $origName - ekstensi tidak diizinkan.";
            continue;
        }
        if ($size > MAX_FILE_SIZE) {
            $messages[] = "❌ $origName - ukuran terlalu besar.";
            continue;
        }
        $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/','_', pathinfo($origName, PATHINFO_FILENAME));
        $newName = $safeBase . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = UPLOAD_DIR . '/' . $newName;
        if (move_uploaded_file($tmp, $dest)) {
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                createImageThumb($dest, THUMB_DIR . '/' . $newName);
            } elseif (in_array($ext, ['mp4','mov','avi','webm','mkv','wmv'])) {
                $thumbPath = THUMB_DIR . '/' . $newName . '.jpg';
                @exec("ffmpeg -i " . escapeshellarg($dest) . " -ss 00:00:02 -vframes 1 " . escapeshellarg($thumbPath) . " 2>/dev/null");
            }
            $meta = [
                'filename' => $newName,
                'original' => $origName,
                'size' => $size,
                'uploaded_at' => date('c')
            ];
            $list = file_exists($jsonIndex) ? json_decode(file_get_contents($jsonIndex), true) : [];
            array_unshift($list, $meta);
            file_put_contents($jsonIndex, json_encode($list, JSON_PRETTY_PRINT));
            $messages[] = "✅ $origName berhasil diupload.";
        } else {
            $messages[] = "❌ Gagal menyimpan $origName.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Upload Dokumentasi</title>
<style>
body{font-family:Arial;padding:20px;background:#f5f5f5;}
form{background:#fff;padding:20px;border-radius:8px;max-width:400px;}
ul{color:#d00;}
a{color:#06f;text-decoration:none;}
</style>
</head>
<body>
<?php if (!isset($_SESSION['is_admin'])): ?>
    <h2>Login Admin</h2>
    <?php foreach($messages as $m) echo "<p>$m</p>"; ?>
    <form method="post">
      <input type="password" name="password" placeholder="Password admin" required><br><br>
      <button type="submit" name="admin_login">Login</button>
    </form>
<?php else: ?>
    <h2>Upload Foto / Video</h2>
    <p><a href="gallery.php">📸 Lihat Galeri Publik</a> | <a href="?logout=1">Logout</a></p>
    <?php foreach($messages as $m) echo "<p>$m</p>"; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="file" name="files[]" multiple accept="image/*,video/*" required><br><br>
      <button type="submit">Upload</button>
    </form>
<?php endif; ?>
</body>
</html>