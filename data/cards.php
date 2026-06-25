<?php
declare(strict_types=1);

function cards(): array
{
    $directory = __DIR__ . '/../assets/cards';
    $files = glob($directory . '/*.png') ?: [];
    $cards = [];

    foreach ($files as $file) {
        $filename = basename($file);
        if (!preg_match('/m5-(\d+)/', $filename, $matches)) {
            continue;
        }

        $number = (int) $matches[1];
        $cardNo = str_pad((string) $number, 3, '0', STR_PAD_LEFT);
        $rarity = card_rarity($number);

        $cardId = 'm5-' . $cardNo;
        $override = $_SESSION['card_overrides'][$cardId] ?? [];

        $cards[] = [
            'id' => $cardId,
            'number' => $cardNo,
            'name' => card_display_name($cardNo),
            'series' => 'M5 卡包圖鑑',
            'rarity' => $rarity,
            'type' => card_type($number),
            'price' => isset($override['price']) ? (int) $override['price'] : card_price($rarity, $number),
            'stock' => isset($override['stock']) ? (int) $override['stock'] : max(1, 12 - ($number % 9)),
            'status' => card_condition($number),
            'seller' => card_seller($number),
            'description' => '來自 M5 卡包圖鑑的第 ' . $cardNo . ' 號卡，可加入收藏管理或購物車模擬交易。',
            'image' => 'assets/cards/' . $filename,
            'accent' => 'card-image',
        ];
    }

    usort($cards, function (array $a, array $b): int {
        return (int) $a['number'] <=> (int) $b['number'];
    });

    return $cards;
}

function card_display_name(string $number): string
{
    return 'M5-' . $number;
}

function card_rarity(int $number): string
{
    if ($number >= 110) {
        return '特殊稀有';
    }

    if ($number >= 75) {
        return '稀有';
    }

    if ($number % 10 === 0 || $number % 10 === 5) {
        return '稀有';
    }

    if ($number % 3 === 0) {
        return '非普通';
    }

    return '普通';
}

function card_type(int $number): string
{
    if ($number >= 75) {
        return '訓練家/能量';
    }

    if ($number <= 15) {
        return '草';
    }

    if ($number <= 28) {
        return '火';
    }

    if ($number <= 42) {
        return '水';
    }

    if ($number <= 55) {
        return '超/惡';
    }

    return '無色/其他';
}

function card_price(string $rarity, int $number): int
{
    $base = [
        '普通' => 60,
        '非普通' => 100,
        '稀有' => 180,
        '特殊稀有' => 680,
    ][$rarity] ?? 80;

    return $base + (($number % 7) * 20);
}

function card_condition(int $number): string
{
    $conditions = ['近全新', '良好', '普通'];
    return $conditions[$number % count($conditions)];
}

function card_seller(int $number): string
{
    $sellers = ['TCG 小舖', '卡牌研究社', '寶可夢收藏家', '對戰補給站'];
    return $sellers[$number % count($sellers)];
}
