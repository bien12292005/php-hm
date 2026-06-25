<?php
require_once __DIR__ . '/includes/bootstrap.php';

$action = (string) ($_POST['action'] ?? '');
$id = (string) ($_POST['id'] ?? '');
$card = $id !== '' ? find_card($id) : null;
$redirect = (string) ($_POST['redirect'] ?? '');

if ($redirect === '' || strpos($redirect, '://') !== false || strpos($redirect, "\n") !== false || strpos($redirect, "\r") !== false) {
    $redirect = 'index.php';
}

if (in_array($action, ['add_collection', 'remove_collection', 'submit_report', 'submit_review'], true)) {
    require_login();
}

if ($action === 'add_cart' && $card !== null) {
    $_SESSION['cart'][$id] = (int) ($_SESSION['cart'][$id] ?? 0) + 1;
    $_SESSION['flash'] = '已加入購物車。';
    redirect_to($redirect);
}

if ($action === 'remove_cart') {
    unset($_SESSION['cart'][$id]);
    $_SESSION['flash'] = '已從購物車移除。';
    redirect_to('cart.php');
}

if ($action === 'checkout') {
    if (empty($_SESSION['cart'])) {
        $_SESSION['flash'] = '購物車目前沒有商品。';
        redirect_to('cart.php');
    }

    $_SESSION['orders'][] = [
        'number' => 'OD' . date('YmdHis'),
        'total' => cart_total(),
        'created_at' => date('Y-m-d H:i:s'),
        'items' => $_SESSION['cart'],
    ];
    $_SESSION['cart'] = [];
    $_SESSION['flash'] = '訂單已建立，這是模擬交易流程。';
    redirect_to('cart.php');
}

if ($action === 'add_collection' && $card !== null) {
    $_SESSION['collection'] = array_values(array_unique(array_merge(collection_ids(), [$id])));
    $_SESSION['flash'] = '已加入收藏管理。';
    redirect_to('collection.php');
}

if ($action === 'remove_collection') {
    $_SESSION['collection'] = array_values(array_filter(
        collection_ids(),
        function (string $cardId) use ($id): bool {
            return $cardId !== $id;
        }
    ));
    $_SESSION['flash'] = '已從收藏移除。';
    redirect_to('collection.php');
}

if ($action === 'submit_report' && $card !== null) {
    $reason = trim((string) ($_POST['reason'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    if ($reason === '' || $message === '') {
        $_SESSION['flash'] = '請填寫完整檢舉內容。';
        redirect_to($redirect);
    }

    $_SESSION['reports'][] = [
        'id' => 'RP' . date('YmdHis') . mt_rand(10, 99),
        'card_id' => $card['id'],
        'card_name' => $card['name'],
        'user_name' => current_user()['name'],
        'reason' => $reason,
        'message' => $message,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
    ];
    $_SESSION['flash'] = '檢舉已送出，管理員會在後台處理。';
    redirect_to($redirect);
}

if ($action === 'submit_review' && $card !== null) {
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim((string) ($_POST['comment'] ?? ''));

    if ($rating < 1 || $rating > 5 || $comment === '') {
        $_SESSION['flash'] = '請填寫完整評價內容。';
        redirect_to($redirect);
    }

    $_SESSION['reviews'][] = [
        'id' => 'RV' . date('YmdHis') . mt_rand(10, 99),
        'card_id' => $card['id'],
        'card_name' => $card['name'],
        'user_name' => current_user()['name'],
        'rating' => $rating,
        'comment' => $comment,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
    ];
    $_SESSION['flash'] = '評價已送出，通過管理員審核後會顯示。';
    redirect_to($redirect);
}

if ($action === 'update_report') {
    require_admin();
    $reportId = (string) ($_POST['report_id'] ?? '');
    $status = (string) ($_POST['status'] ?? '');

    if (isset($_SESSION['reports']) && in_array($status, ['processing', 'resolved', 'rejected'], true)) {
        foreach ($_SESSION['reports'] as $index => $report) {
            if (($report['id'] ?? '') === $reportId) {
                $_SESSION['reports'][$index]['status'] = $status;
                $_SESSION['reports'][$index]['handled_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
    }

    $_SESSION['flash'] = '檢舉狀態已更新。';
    redirect_to('admin/index.php');
}

if ($action === 'update_review') {
    require_admin();
    $reviewId = (string) ($_POST['review_id'] ?? '');
    $status = (string) ($_POST['status'] ?? '');

    if (isset($_SESSION['reviews']) && in_array($status, ['approved', 'rejected'], true)) {
        foreach ($_SESSION['reviews'] as $index => $review) {
            if (($review['id'] ?? '') === $reviewId) {
                $_SESSION['reviews'][$index]['status'] = $status;
                $_SESSION['reviews'][$index]['handled_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
    }

    $_SESSION['flash'] = '評價審核狀態已更新。';
    redirect_to('admin/index.php');
}

if ($action === 'update_card') {
    require_admin();
    $price = (int) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);

    if ($card === null || $price < 0 || $stock < 0) {
        $_SESSION['flash'] = '價格或庫存資料不正確。';
        redirect_to('admin/index.php');
    }

    $_SESSION['card_overrides'][$id] = [
        'price' => $price,
        'stock' => $stock,
    ];
    $_SESSION['flash'] = $card['name'] . ' 的價格與庫存已更新。';
    redirect_to('admin/index.php');
}

$_SESSION['flash'] = '操作失敗，請再試一次。';
redirect_to('index.php');
