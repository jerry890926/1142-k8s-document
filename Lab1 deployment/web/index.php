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
</body></html>
