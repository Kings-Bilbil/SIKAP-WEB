<?php

// 1. Buat folder sementara agar Vercel bisa bernapas
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Beri tahu Laravel untuk menggunakan folder sementara ini
$_ENV['APP_STORAGE'] = '/tmp/storage';

// 3. Jalankan aplikasi utama
require __DIR__ . '/../public/index.php';