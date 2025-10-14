<?php
require 'config.php';
$jsonIndex = UPLOAD_DIR . '/index.json';
$items = file_exists($jsonIndex) ? json_decode(file_get_contents($jsonIndex), true) : [];
$total = count($items);
$pages = ceil($total / ITEMS_PER_PAGE);
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;
$items = array_slice($items, $offset, ITEMS_PER_PAGE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Galeri Dokumentasi</title>
<style>
body{font-family:Arial;padding:20px;background:#f8f8f8;}
.grid{display:flex;flex-wrap:wrap;gap:16px;}
.card{width:250px;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.1);}
.thumb{height:160px;display:flex;align-items:center;justify-content:center;background:#eee;}
.thumb img, .thumb video{max-width:100%;max-height:100%;object-fit:cover;}
.meta{padding:10px;font-size:13px;}
.pagination{margin-top:20px;}
.pagination a{margin:0 4px;padding:6px 10px;background:#fff;border:1px solid #ccc;text-decoration:none;color:#333;}
.pagination .active{background:#333;color:#fff;}
</style>
</head>
<body>
  <h1>📸 Galeri Dokumentasi</h1>
  <p>Total file: <?= $total ?></p>
  <div class="grid">
    <?php foreach ($items as $it): 
      $file = $it['filename'];
      $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
      $filePath = 'dokumentasi/' . rawurlencode($file);
      $thumbPath = 'dokumentasi/thumbs/' . rawurlencode($file);
      $thumbVideo = $thumbPath . '.jpg';
    ?>
    <div class="card">
      <div class="thumb">
        <?php if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
          <a href="<?= $filePath ?>" target="_blank"><img src="<?= $thumbPath ?>" alt=""></a>
        <?php elseif (in_array($ext, ['mp4','mov','avi','webm','mkv','wmv'])): ?>
          <?php if (file_exists(UPLOAD_DIR . '/thumbs/' . $file . '.jpg')): ?>
            <a href="<?= $filePath ?>" target="_blank"><img src="<?= $thumbVideo ?>" alt=""></a>
          <?php else: ?>
            <video controls src="<?= $filePath ?>"></video>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="meta">
        <div><?= htmlspecialchars($it['original']) ?></div>
        <small><?= date('d M Y H:i', strtotime($it['uploaded_at'])) ?></small>
        <div><a href="<?= $filePath ?>" download>Download</a></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if ($pages > 1): ?>
  <div class="pagination">
    <?php for ($p=1;$p<=$pages;$p++): ?>
      <a href="?page=<?= $p ?>" class="<?= $p==$page?'active':'' ?>"><?= $p ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</body>
</html>
