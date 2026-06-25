<?php
require_once __DIR__ . '/includes/bootstrap.php';

unset($_SESSION['user']);
$_SESSION['flash'] = '已登出。';
redirect_to('index.php');

