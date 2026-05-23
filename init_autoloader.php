<?php

if (file_exists('vendor/autoload.php')) {
    include 'vendor/autoload.php';
}

if (!class_exists('Laminas\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load Laminas. Run `composer install` before starting the application.');
}
