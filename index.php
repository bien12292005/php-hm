<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = '商品列表 - TCG Market';
$keyword = trim((string) ($_GET['keyword'] ?? ''));
$type = trim((string) ($_GET['type'] ?? ''));

$cards = array_values(array_filter(cards(), function (array $card) use ($keyword, $type): bool {
    $matchesKeyword = $keyword === ''
        || strpos($card['name'], $keyword) !== false
        || strpos($card['series'], $keyword) !== false
        || strpos($card['rarity'], $keyword) !== false
        || strpos($card['number'], $keyword) !== false;
    $matchesType = $type === '' || $card['type'] === $type;
    return $matchesKeyword && $matchesType;
}));

$types = array_values(array_unique(array_map(function (array $card): string {
    return $card['type'];
}, cards())));
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero compact-hero">
  <div>
    <p class="eyebrow">Pokemon TCG Trading</p>
    <h1>卡牌交易與收藏平台</h1>
    <p>這裡是實際模擬交易網站，不再是報告展示頁。使用者可以搜尋卡牌、查看商品、加入收藏與購物車，管理員則需登入後台才可管理平台資料。</p>
  </div>
  <div class="hero-card">
    <strong><?= count(cards()) ?></strong>
    <span>目前卡包卡牌</span>
    <small>M5 卡包圖鑑已匯入</small>
  </div>
</section>

<section class="page-section">
  <form class="search-panel" method="get" action="index.php">
    <label>
      搜尋卡牌
      <input type="search" name="keyword" value="<?= h($keyword) ?>" placeholder="輸入卡名、系列或稀有度">
    </label>
    <label>
      屬性分類
      <select name="type">
        <option value="">全部屬性</option>
        <?php foreach ($types as $option): ?>
          <option value="<?= h($option) ?>" <?= $type === $option ? 'selected' : '' ?>><?= h($option) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button class="primary-button" type="submit">搜尋</button>
  </form>

  <div class="section-title">
    <h2>卡包商品</h2>
    <span><?= count($cards) ?> 張結果</span>
  </div>

  <div class="product-grid">
    <?php foreach ($cards as $card): ?>
      <article class="product-card">
        <a class="card-art image-art" href="product.php?id=<?= h($card['id']) ?>">
          <img src="<?= h($card['image']) ?>" alt="<?= h($card['name']) ?>">
        </a>
        <div class="product-body">
          <span class="tag"><?= h($card['rarity']) ?></span>
          <h3><?= h($card['name']) ?></h3>
          <p><?= h($card['description']) ?></p>
          <div class="meta-row">
            <span>庫存 <?= h((string) $card['stock']) ?></span>
            <strong><?= h(format_price($card['price'])) ?></strong>
          </div>
        </div>
        <div class="card-actions">
          <a class="secondary-button" href="product.php?id=<?= h($card['id']) ?>">詳情</a>
          <form method="post" action="actions.php">
            <input type="hidden" name="action" value="add_cart">
            <input type="hidden" name="id" value="<?= h($card['id']) ?>">
            <input type="hidden" name="redirect" value="<?= h($_SERVER['REQUEST_URI']) ?>">
            <button class="primary-button" type="submit">加入購物車</button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
