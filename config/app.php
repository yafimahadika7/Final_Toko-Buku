<?php
declare(strict_types=1);

// AUTO BASE_URL (AMAN UNTUK LOCAL SUBFOLDER & HOSTING ROOT)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];

// Deteksi folder project (untuk localhost)
$scriptName = dirname($_SERVER['SCRIPT_NAME']); 
$basePath   = str_replace('/public', '', $scriptName);
$basePath   = ($basePath === '/') ? '' : $basePath;

define('BASE_URL', $scheme . '://' . $host . $basePath);

// App name
define('APP_NAME', 'Store Admin');
