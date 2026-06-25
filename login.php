<?php
require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $users = users();

    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['user'] = [
            'username' => $users[$username]['username'],
            'name' => $users[$username]['name'],
            'role' => $users[$username]['role'],
        ];
        $_SESSION['flash'] = '登入成功。';
        redirect_to('index.php');
    }

    $_SESSION['flash'] = '帳號或密碼錯誤。';
}

$pageTitle = '會員登入 - TCG Market';
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
  <form class="auth-card" method="post" action="login.php">
    <h1>會員登入</h1>
    <p>一般使用者登入後可以使用收藏管理。測試帳號：member / 123456</p>
    <label>
      帳號
      <input name="username" required autocomplete="username">
    </label>
    <label>
      密碼
      <input name="password" type="password" required autocomplete="current-password">
    </label>
    <button class="primary-button" type="submit">登入</button>
  </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

