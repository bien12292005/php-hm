<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../data/cards.php';
require_once __DIR__ . '/../data/users.php';

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string
{
    return $path;
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user !== null && ($user['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['flash'] = '請先登入會員後再使用此功能。';
        redirect_to('login.php');
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        $_SESSION['flash'] = '此區域限管理員登入後使用。';
        redirect_to('../admin/login.php');
    }
}

function flash_message(): ?string
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $message = (string) $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $message;
}

function format_price(int $price): string
{
    return 'NT$ ' . number_format($price);
}

function find_card(string $id): ?array
{
    foreach (cards() as $card) {
        if ($card['id'] === $id) {
            return $card;
        }
    }

    return null;
}

function collection_ids(): array
{
    return $_SESSION['collection'] ?? [];
}

function cart_items(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_total(): int
{
    $total = 0;

    foreach (cart_items() as $cardId => $quantity) {
        $card = find_card((string) $cardId);
        if ($card !== null) {
            $total += $card['price'] * (int) $quantity;
        }
    }

    return $total;
}

function report_status_label(string $status): string
{
    $labels = [
        'pending' => '待處理',
        'processing' => '處理中',
        'resolved' => '已退貨/結案',
        'rejected' => '已駁回',
    ];

    return $labels[$status] ?? $status;
}

function review_status_label(string $status): string
{
    $labels = [
        'pending' => '待審核',
        'approved' => '已通過',
        'rejected' => '已拒絕',
    ];

    return $labels[$status] ?? $status;
}
