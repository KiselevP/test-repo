<?php

/**
 * This file is included very early. See autoload.files in composer.json and
 * https://getcomposer.org/doc/04-schema.md#files
 */

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

if (class_exists(\Dotenv\Dotenv::class)) {
    /**
     * Load any .env file. See /.env.example.
     */
    $dotenv = Dotenv::createUnsafeImmutable(__DIR__);
    try {
        $dotenv->load();
    }
    catch (InvalidPathException $e) {
        // Do nothing. Production environments rarely use .env files.
    }
}