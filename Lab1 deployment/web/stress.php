<?php
$timeout = 100;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $start = microtime(true);
  while (microtime(true) - $start < $timeout) {
    sqrt(rand());
  }
  $done = true;
}
?>
<html><body>
<h2>Lab5 - Stress CPU</h2>
<p>Pod: <b><?= htmlspecialchars(getenv('POD_NAME')) ?></b> | PodIP: <b><?= htmlspecialchars(getenv('POD_IP')) ?></b></p>

<form method="POST">
  <button name="on" value="1">Stress CPU (<?= $timeout ?>s)</button>
</form>

<?php if (isset($done)) { echo "<p><b>Done.</b></p>"; } ?>

<p><a href="/">Back</a></p>
</body></html>

