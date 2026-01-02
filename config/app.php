<?php
declare(strict_types=1);

/**
 * =========================================================
 * AUTO BASE_URL (AMAN UNTUK VPS / HOSTING / LOCALHOST)
 * - DocumentRoot diarahkan ke folder /public
 * - TIDAK PERNAH menambahkan /public ke URL
 * =========================================================
 */

// Tentukan scheme (http / https)
$scheme = 'http';
if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || ($_SERVER['SERVER_PORT'] ?? null) == 443
) {
    $scheme = 'https';
}

// Host (domain atau IP)
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Karena DocumentRoot sudah /public,
// BASE_URL cukup domain saja
define('BASE_URL', $scheme . '://' . $host);

// Nama aplikasi
define('APP_NAME', 'Store Admin');
