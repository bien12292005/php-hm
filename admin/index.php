<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$orders = $_SESSION['orders'] ?? [];
$reports = $_SESSION['reports'] ?? [];
$reviews = $_SESSION['reviews'] ?? [];
$cards = cards();
$collectionCount = count(collection_ids());
$pendingReports = count(array_filter($reports, function (array $report): bool {
    return ($report['status'] ?? '') === 'pending';
}));
$pendingReviews = count(array_filter($reviews, function (array $review): bool {
    return ($review['status'] ?? '') === 'pending';
}));
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>後台管理 - TCG Market</title>
  <link rel="stylesheet" href="../app.css">
</head>
<body class="admin-body">
  <header class="admin-header">
    <div>
      <strong>TCG Market 後台</strong>
      <span>已登入：<?= h(current_user()['name']) ?></span>
    </div>
    <nav>
      <a href="../index.php">回前台</a>
      <a href="../logout.php">登出</a>
    </nav>
  </header>

  <?php if ($message = flash_message()): ?>
    <div class="flash"><?= h($message) ?></div>
  <?php endif; ?>

  <main class="admin-main">
    <section class="admin-metrics">
      <article><span>商品數</span><strong><?= count($cards) ?></strong></article>
      <article><span>模擬訂單</span><strong><?= count($orders) ?></strong></article>
      <article><span>收藏紀錄</span><strong><?= $collectionCount ?></strong></article>
      <article><span>待處理檢舉</span><strong><?= $pendingReports ?></strong></article>
      <article><span>待審核評價</span><strong><?= $pendingReviews ?></strong></article>
    </section>

    <section class="admin-panel">
      <div class="section-title">
        <h1>檢舉與退貨處理</h1>
        <span>商品瑕疵、卡況不符、退貨申請</span>
      </div>
      <?php if (empty($reports)): ?>
        <div class="empty-state">目前沒有檢舉案件。</div>
      <?php else: ?>
        <div class="admin-table issue-table">
          <div class="table-head">
            <span>案件編號</span>
            <span>商品</span>
            <span>原因</span>
            <span>說明</span>
            <span>狀態</span>
            <span>處理</span>
          </div>
          <?php foreach ($reports as $report): ?>
            <div>
              <span><?= h($report['id']) ?></span>
              <span><?= h($report['card_name']) ?><br><small><?= h($report['user_name']) ?> · <?= h($report['created_at']) ?></small></span>
              <span><?= h($report['reason']) ?></span>
              <span><?= h($report['message']) ?></span>
              <span><?= h(report_status_label($report['status'])) ?></span>
              <span class="admin-actions">
                <form method="post" action="../actions.php">
                  <input type="hidden" name="action" value="update_report">
                  <input type="hidden" name="report_id" value="<?= h($report['id']) ?>">
                  <button class="text-button" name="status" value="processing" type="submit">處理中</button>
                  <button class="text-button" name="status" value="resolved" type="submit">已退貨/結案</button>
                  <button class="text-button" name="status" value="rejected" type="submit">駁回</button>
                </form>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="admin-panel">
      <div class="section-title">
        <h1>評價審核</h1>
        <span>通過後會顯示在商品頁</span>
      </div>
      <?php if (empty($reviews)): ?>
        <div class="empty-state">目前沒有待審或已審評價。</div>
      <?php else: ?>
        <div class="admin-table review-table">
          <div class="table-head">
            <span>評價編號</span>
            <span>商品</span>
            <span>評分</span>
            <span>內容</span>
            <span>狀態</span>
            <span>審核</span>
          </div>
          <?php foreach ($reviews as $review): ?>
            <div>
              <span><?= h($review['id']) ?></span>
              <span><?= h($review['card_name']) ?><br><small><?= h($review['user_name']) ?> · <?= h($review['created_at']) ?></small></span>
              <span><?= h((string) $review['rating']) ?> 分</span>
              <span><?= h($review['comment']) ?></span>
              <span><?= h(review_status_label($review['status'])) ?></span>
              <span class="admin-actions">
                <form method="post" action="../actions.php">
                  <input type="hidden" name="action" value="update_review">
                  <input type="hidden" name="review_id" value="<?= h($review['id']) ?>">
                  <button class="text-button" name="status" value="approved" type="submit">通過</button>
                  <button class="text-button" name="status" value="rejected" type="submit">拒絕</button>
                </form>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="admin-panel">
      <div class="section-title">
        <h1>商品管理</h1>
        <span>後續可接 MySQL 新增、修改、刪除</span>
      </div>
      <div class="admin-table product-manage-table">
        <div class="table-head">
          <span>編號</span>
          <span>卡牌名稱</span>
          <span>稀有度</span>
          <span>價格</span>
          <span>庫存</span>
          <span>儲存</span>
        </div>
        <?php foreach ($cards as $card): ?>
          <form class="admin-edit-row" method="post" action="../actions.php">
            <input type="hidden" name="action" value="update_card">
            <input type="hidden" name="id" value="<?= h($card['id']) ?>">
            <span><?= h($card['number']) ?></span>
            <span><?= h($card['name']) ?></span>
            <span><?= h($card['rarity']) ?></span>
            <label class="admin-inline-field">
              <span class="sr-only">價格</span>
              <input type="number" name="price" min="0" step="1" value="<?= h((string) $card['price']) ?>" required>
            </label>
            <label class="admin-inline-field">
              <span class="sr-only">庫存</span>
              <input type="number" name="stock" min="0" step="1" value="<?= h((string) $card['stock']) ?>" required>
            </label>
            <span>
              <button class="primary-button compact-button" type="submit">儲存</button>
            </span>
          </form>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>
