<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$conn = new mysqli('127.0.0.1', 'root', '', 'shilpanepal', 3307);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");
define('SITE_NAME',           'ShilpaNepal');
define('SITE_URL',            'http://localhost/shilpanepal');
define('SITE_EMAIL',          'info@shilpanepal.com');
define('CURRENCY',            'NPR');
define('LOW_STOCK_LIMIT',     5);
define('ESEWA_MERCHANT_CODE', 'EPAYTEST');
define('ESEWA_SECRET',        '8gBm/:&EnhH.1/q');
define('ESEWA_URL',           'https://rc-epay.esewa.com.np/api/epay/main/v2/form');
define('ESEWA_VERIFY_URL',    'https://rc-epay.esewa.com.np/api/epay/transaction/status/');
