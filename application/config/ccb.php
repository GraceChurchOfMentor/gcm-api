<?php

/** This enables .env file support */
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

# CCB API login settings
$config['ccb_base_url'] = getenv('CCB_BASE_URL');
$config['ccb_username'] = getenv('CCB_USERNAME');
$config['ccb_password'] = getenv('CCB_PASSWORD');
