<?php
require_once __DIR__ . '/includes/bootstrap.php';

try {
    $pdo = db();
    $version = $pdo->query('SELECT VERSION() AS version')->fetch();
    $message = 'MySQL 連線成功，版本：' . ($version['version'] ?? 'unknown');
} catch (Throwable $error) {
    $message = 'MySQL 連線失敗：' . $error->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MySQL 連線測試</title>
  <link rel="stylesheet" href="app.css">
</head>
<body>
  <main class="auth-page">
    <section class="auth-card">
      <h1>MySQL 連線測試</h1>
      <p><?= h($message) ?></p>
      <a class="secondary-button" href="index.php">回首頁</a>
    </section>
  </main>
</body>
</html>
