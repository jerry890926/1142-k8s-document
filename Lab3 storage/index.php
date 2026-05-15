<?php
date_default_timezone_set('Asia/Taipei');

$pod = getenv('POD_NAME');
$podip = getenv('POD_IP');
$ns = getenv('POD_NAMESPACE');
$node = getenv('NODE_NAME');
$nodeip = getenv('NODE_IP');
$now = date("Y-m-d H:i:s");
$appVer = getenv('APP_VERSION');
?>


<html><body>
<h2>K8s Teaching Web</h2>

<ul>
  <li><b>Pod:</b> <?= htmlspecialchars($pod) ?></li>
  <li><b>Pod IP:</b> <?= htmlspecialchars($podip) ?></li>
  <li><b>Namespace:</b> <?= htmlspecialchars($ns) ?></li>
  <li><b>Node:</b> <?= htmlspecialchars($node) ?></li>
  <li><b>Node IP:</b> <?= htmlspecialchars($nodeip) ?></li>
  <li><b>Time:</b> <?= htmlspecialchars($now) ?></li>
  <li><b>App Version:</b> <?= htmlspecialchars($appVer) ?></li>
</ul>

<p>Tip: 重新整理頁面，觀察分流（不同 Pod/Node）。</p>



<!-- ===== Lab3 (PVC write/read) logic on homepage ===== -->
<?php
$lab3_path = "/data/lab3.txt";
$lab3_mounted = is_dir("/data") && is_writable("/data");
$lab3_msg = null;
$hostname = gethostname();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["lab3_write"]) && $_POST["lab3_write"] === "1") {
  if (!$lab3_mounted) {
    $lab3_msg = "/data 未掛載或不可寫（請在 Lab3 加 PVC mount）";
  } else {
    $line = date("Y-m-d H:i:s") . " | " . getenv('POD_NAME') . " | " . getenv('POD_IP') . " | " . $hostname . "\n";
    file_put_contents($lab3_path, $line, FILE_APPEND);
    $lab3_msg = "已寫入一行到 $lab3_path";
  }

}

$lab3_content = file_exists($lab3_path) ? file_get_contents($lab3_path) : "(尚無內容)";
?>


<hr>
<h3>Lab3 - PVC Write/Read</h3>

<?php if ($lab3_msg !== null): ?>
  <p><b><?= htmlspecialchars($lab3_msg) ?></b></p>
<?php endif; ?>

<form method="POST">
  <input type="hidden" name="lab3_write" value="1">
  <button type="submit">Write one line to /data</button>
</form>

<p><b>File:</b> <?= htmlspecialchars($lab3_path) ?></p>
<pre style="white-space: pre-wrap;"><?= htmlspecialchars($lab3_content) ?></pre>

</body></html> 
