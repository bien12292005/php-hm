<?php
declare(strict_types=1);

function users(): array
{
    return [
        'member' => [
            'username' => 'member',
            'password' => '123456',
            'name' => '一般會員',
            'role' => 'member',
        ],
        'admin' => [
            'username' => 'admin',
            'password' => 'admin123',
            'name' => '平台管理員',
            'role' => 'admin',
        ],
    ];
}

