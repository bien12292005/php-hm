<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = '購物車 - TCG Market';
require_once __DIR__ . '/includes/header.php';
$items = cart_items();
?>
<section class="page-section narrow">
  <div class="section-title">
    <h1>購物車</h1>
    <span><?= count($items) ?> 種商品</span>
  </div>

  <?php if (empty($items)): ?>
    <div class="empty-state">購物車目前是空的，可以先到商品列表挑選卡牌。</div>
  <?php else: ?>
    <div class="list-panel">
      <?php foreach ($items as $cardId => $quantity): ?>
        <?php $card = find_card((string) $cardId); ?>
        <?php if ($card): ?>
          <div class="list-row">
            <div>
              <strong><?= h($card['name']) ?></strong>
              <span><?= h(format_price($card['price'])) ?> × <?= h((string) $quantity) ?></span>
            </div>
            <form method="post" action="actions.php">
              <input type="hidden" name="action" value="remove_cart">
              <input type="hidden" name="id" value="<?= h($card['id']) ?>">
              <button class="text-button" type="submit">移除</button>
            </form>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <div class="checkout-panel">
      <strong>總金額 <?= h(format_price(cart_total())) ?></strong>
      <form method="post" action="actions.php">
        <button class="primary-button" name="action" value="checkout" type="submit">建立模擬訂單</button>
      </form>
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

