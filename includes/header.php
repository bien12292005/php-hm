<?php
$user = current_user();
$flash = flash_message();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle ?? 'TCG Market 卡牌買賣平台') ?></title>
  <link rel="stylesheet" href="app.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="index.php">
      <span class="brand-mark">TCG</span>
      <span>
        <strong>TCG Market</strong>
        <small>卡牌買賣平台</small>
      </span>
    </a>
    <nav class="main-nav" aria-label="主要導覽">
      <a href="index.php">商品</a>
      <a href="collection.php">收藏</a>
      <a href="cart.php">購物車</a>
      <a href="admin/login.php">後台</a>
    </nav>
    <div class="user-tools">
      <?php if ($user): ?>
        <span><?= h($user['name']) ?></span>
        <a class="ghost-button" href="logout.php">登出</a>
      <?php else: ?>
        <a class="ghost-button" href="login.php">會員登入</a>
      <?php endif; ?>
    </div>
  </header>
  <?php if ($flash): ?>
    <div class="flash"><?= h($flash) ?></div>
  <?php endif; ?>
  <main>

