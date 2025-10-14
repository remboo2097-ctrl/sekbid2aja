<?php
// config.php
define('ADMIN_PASSWORD', 'ganti_password_admin');
define('UPLOAD_DIR', __DIR__ . '/dokumentasi');
define('THUMB_DIR', UPLOAD_DIR . '/thumbs');
define('MAX_FILE_SIZE', 50 * 1024 * 1024);
$GLOBALS['ALLOWED_EXT'] = ['jpg','jpeg','png','gif','mp4','mov','avi','webm','mkv','wmv'];
define('ITEMS_PER_PAGE', 20);
if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
if (!file_exists(THUMB_DIR)) mkdir(THUMB_DIR, 0755, true);
?>