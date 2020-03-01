<?php

/** This enables .env file support */
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

# Bibles.org API
$config['biblesorg_base_url'] = getenv('BIBLESORG_BASE_URL');
$config['biblesorg_api_key'] = getenv('BIBLESORG_API_KEY');
$config['biblesorg_default_version'] = getenv('BIBLESORG_DEFAULT_VERSION');
