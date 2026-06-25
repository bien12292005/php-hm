<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$pageTitle = '收藏管理 - TCG Market';
$ids = collection_ids();
$collectedCards = array_values(array_filter(array_map('find_card', $ids)));
$collectionTotal = array_sum(array_map(function (array $card): int {
    return $card['price'];
}, $collectedCards));
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-section narrow">
  <div class="section-title">
    <h1>我的收藏管理</h1>
    <span><?= count($collectedCards) ?> 張收藏</span>
  </div>

  <div class="collection-summary">
    <article>
      <span>收藏數量</span>
      <strong><?= count($collectedCards) ?></strong>
    </article>
    <article>
      <span>收藏總值</span>
      <strong><?= h(format_price($collectionTotal)) ?></strong>
    </article>
  </div>

  <?php if (empty($collectedCards)): ?>
    <div class="empty-state">目前還沒有收藏。到商品詳情頁點「加入收藏」就會出現在這裡。</div>
  <?php else: ?>
    <div class="list-panel">
      <?php foreach ($collectedCards as $card): ?>
        <div class="list-row">
          <div>
            <strong><?= h($card['name']) ?></strong>
            <span><?= h($card['series']) ?> · <?= h($card['rarity']) ?> · <?= h(format_price($card['price'])) ?></span>
          </div>
          <form method="post" action="actions.php">
            <input type="hidden" name="action" value="remove_collection">
            <input type="hidden" name="id" value="<?= h($card['id']) ?>">
            <button class="text-button" type="submit">移除收藏</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
