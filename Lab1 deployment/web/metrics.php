<?php
header('Content-Type: application/json');

function readFileTrim($path) {
  $v = @file_get_contents($path);
  return $v === false ? null : trim($v);
}

function cpuUsageUsec() {
  // cgroup v2: /sys/fs/cgroup/cpu.stat 內有 usage_usec
  $stat = readFileTrim("/sys/fs/cgroup/cpu.stat");
  if ($stat) {
    foreach (explode("\n", $stat) as $line) {
      if (str_starts_with($line, "usage_usec ")) {
        return intval(substr($line, strlen("usage_usec ")));
      }
    }
  }
  // cgroup v1 fallback（某些環境）
  $v1 = readFileTrim("/sys/fs/cgroup/cpuacct/cpuacct.usage");
  if ($v1) {
    // nanoseconds -> usec
    return intval(intval($v1) / 1000);
  }
  return null;
}

function memCurrentBytes() {
  $v2 = readFileTrim("/sys/fs/cgroup/memory.current");
  if ($v2 !== null && $v2 !== "") return intval($v2);

  $v1 = readFileTrim("/sys/fs/cgroup/memory/memory.usage_in_bytes");
  if ($v1 !== null && $v1 !== "") return intval($v1);

  return null;
}

function memMaxBytes() {
  // cgroup v2: memory.max 可能是 "max"
  $v2 = readFileTrim("/sys/fs/cgroup/memory.max");
  if ($v2 !== null && $v2 !== "") {
    if ($v2 === "max") return null;
    return intval($v2);
  }
  // v1: memory.limit_in_bytes
  $v1 = readFileTrim("/sys/fs/cgroup/memory/memory.limit_in_bytes");
  if ($v1 !== null && $v1 !== "") return intval($v1);

  return null;
}

$nowMs = intval(microtime(true) * 1000);

echo json_encode([
  "ts_ms" => $nowMs,
  "cpu_usage_usec" => cpuUsageUsec(),
  "mem_current_bytes" => memCurrentBytes(),
  "mem_max_bytes" => memMaxBytes(),

  // limits/requests from env (Downward API)
  "cpu_limit_m" => getenv("CPU_LIMIT_MILLICORES") ?: null,
  "mem_limit_bytes" => getenv("MEM_LIMIT_BYTES") ?: null,
  "cpu_request_m" => getenv("CPU_REQUEST_MILLICORES") ?: null,
  "mem_request_bytes" => getenv("MEM_REQUEST_BYTES") ?: null,

  // basic identity (optional)
  "pod" => getenv("POD_NAME") ?: null,
  "pod_ip" => getenv("POD_IP") ?: null,
  "node" => getenv("NODE_NAME") ?: null,
  "node_ip" => getenv("NODE_IP") ?: null,
], JSON_UNESCAPED_SLASHES);
