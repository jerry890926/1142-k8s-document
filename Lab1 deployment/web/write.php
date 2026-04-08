<?php
$path = "/data/lab3.txt";
$mounted = is_dir("/data") && is_writable("/data");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$mounted) {
        $msg = "/data 未掛載或不可寫（請在 Lab3 加 PVC mount）";
    } else {
        $line = date("Y-m-d H:i:s") . " | " . getenv('POD_NAME') . " | " . getenv('POD_IP') . "\n";
        file_put_contents($path, $line, FILE_APPEND);
        $msg = "已寫入一行到 $path";
    }
}
$content = file_exists($path) ? file_get_contents($path) : "(尚無內容)";
?>
<html><body>
<h2>Lab3 - PVC Write/Read</h2>

<?php if (isset($msg)) { echo "<p><b>".htmlspecialchars($msg)."</b></p>"; } ?>

<form method="POST">
    <button name="write" value="1">Write one line to /data</button>
</form>

<hr>
<p><b>File:</b> <?= htmlspecialchars($path) ?></p>
<pre style="white-space: pre-wrap;"><?= htmlspecialchars($content) ?></pre>

<p><a href="/">Back</a></p>
</body></html>