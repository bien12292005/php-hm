<?php
require_once __DIR__ . '/includes/bootstrap.php';

$card = find_card((string) ($_GET['id'] ?? ''));
if ($card === null) {
    $_SESSION['flash'] = '找不到這張卡牌。';
    redirect_to('index.php');
}

$pageTitle = $card['name'] . ' - TCG Market';
$reviews = $_SESSION['reviews'] ?? [];
$cardReviews = array_values(array_filter($reviews, function (array $review) use ($card): bool {
    return ($review['card_id'] ?? '') === $card['id'] && ($review['status'] ?? '') === 'approved';
}));
require_once __DIR__ . '/includes/header.php';
?>
<section class="detail-layout">
  <div class="detail-art image-detail">
    <img src="<?= h($card['image']) ?>" alt="<?= h($card['name']) ?>">
  </div>
  <article class="detail-panel">
    <span class="tag"><?= h($card['rarity']) ?></span>
    <h1><?= h($card['name']) ?></h1>
    <p><?= h($card['description']) ?></p>
    <dl class="spec-list">
      <div><dt>系列</dt><dd><?= h($card['series']) ?></dd></div>
      <div><dt>卡況</dt><dd><?= h($card['status']) ?></dd></div>
      <div><dt>賣家</dt><dd><?= h($card['seller']) ?></dd></div>
      <div><dt>庫存</dt><dd><?= h((string) $card['stock']) ?></dd></div>
    </dl>
    <div class="purchase-row">
      <strong><?= h(format_price($card['price'])) ?></strong>
      <form method="post" action="actions.php">
        <input type="hidden" name="id" value="<?= h($card['id']) ?>">
        <input type="hidden" name="redirect" value="<?= h($_SERVER['REQUEST_URI']) ?>">
        <button class="primary-button" name="action" value="add_cart" type="submit">加入購物車</button>
        <button class="secondary-button" name="action" value="add_collection" type="submit">加入收藏</button>
      </form>
    </div>
  </article>
</section>
<section class="page-section product-service">
  <div class="section-title">
    <h2>交易服務</h2>
    <span>退貨檢舉與評價審核</span>
  </div>

  <div class="service-grid">
    <form class="service-card" method="post" action="actions.php">
      <h3>商品瑕疵 / 退貨檢舉</h3>
      <p>收到商品後若發現卡況不符、卡片破損、賣家未出貨或其他交易問題，可以送出檢舉交由管理員處理。</p>
      <input type="hidden" name="action" value="submit_report">
      <input type="hidden" name="id" value="<?= h($card['id']) ?>">
      <input type="hidden" name="redirect" value="<?= h($_SERVER['REQUEST_URI']) ?>">
      <label>
        問題類型
        <select name="reason" required>
          <option value="商品有瑕疵">商品有瑕疵</option>
          <option value="卡況與描述不符">卡況與描述不符</option>
          <option value="想申請退貨">想申請退貨</option>
          <option value="賣家未出貨">賣家未出貨</option>
        </select>
      </label>
      <label>
        問題說明
        <textarea name="message" rows="5" required placeholder="請描述瑕疵、退貨原因或交易狀況"></textarea>
      </label>
      <button class="primary-button" type="submit">送出檢舉</button>
    </form>

    <form class="service-card" method="post" action="actions.php">
      <h3>留下商品評價</h3>
      <p>評價送出後會先進入後台審核，管理員通過後才會顯示在商品頁，避免惡意留言或不當內容。</p>
      <input type="hidden" name="action" value="submit_review">
      <input type="hidden" name="id" value="<?= h($card['id']) ?>">
      <input type="hidden" name="redirect" value="<?= h($_SERVER['REQUEST_URI']) ?>">
      <label>
        評分
        <select name="rating" required>
          <option value="5">5 分 - 非常滿意</option>
          <option value="4">4 分 - 滿意</option>
          <option value="3">3 分 - 普通</option>
          <option value="2">2 分 - 不太滿意</option>
          <option value="1">1 分 - 不滿意</option>
        </select>
      </label>
      <label>
        評價內容
        <textarea name="comment" rows="5" required placeholder="請留下商品、卡況或賣家服務評價"></textarea>
      </label>
      <button class="secondary-button" type="submit">送出評價審核</button>
    </form>
  </div>

  <div class="approved-reviews">
    <div class="section-title">
      <h2>已公開評價</h2>
      <span><?= count($cardReviews) ?> 則</span>
    </div>
    <?php if (empty($cardReviews)): ?>
      <div class="empty-state">目前尚無通過審核的評價。</div>
    <?php else: ?>
      <div class="list-panel">
        <?php foreach ($cardReviews as $review): ?>
          <div class="list-row">
            <div>
              <strong><?= h((string) $review['rating']) ?> 分 · <?= h($review['user_name']) ?></strong>
              <span><?= h($review['comment']) ?></span>
            </div>
            <span><?= h($review['created_at']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
