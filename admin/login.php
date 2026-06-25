<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $users = users();

    if (isset($users[$username]) && $users[$username]['password'] === $password && $users[$username]['role'] === 'admin') {
        $_SESSION['user'] = [
            'username' => $users[$username]['username'],
            'name' => $users[$username]['name'],
            'role' => $users[$username]['role'],
        ];
        $_SESSION['flash'] = '管理員登入成功。';
        redirect_to('index.php');
    }

    $_SESSION['flash'] = '管理員帳號或密碼錯誤。';
}

$pageTitle = '管理員登入 - TCG Market';
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle) ?></title>
  <link rel="stylesheet" href="../app.css">
</head>
<body>
  <?php if ($message = flash_message()): ?>
    <div class="flash"><?= h($message) ?></div>
  <?php endif; ?>
  <main class="auth-page">
    <form class="auth-card" method="post" action="login.php">
      <h1>後台管理登入</h1>
      <p>一般會員不能進入後台。測試管理員：admin / admin123</p>
      <label>
        管理員帳號
        <input name="username" required autocomplete="username">
      </label>
      <label>
        密碼
        <input name="password" type="password" required autocomplete="current-password">
      </label>
      <button class="primary-button" type="submit">進入後台</button>
      <a class="secondary-button" href="../index.php">回前台</a>
    </form>
  </main>
</body>
</html>

