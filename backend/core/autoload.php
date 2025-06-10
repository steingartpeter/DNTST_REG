<?php
// backend/core/autoload.php
spl_autoload_register(function ($className) {
    // Define your class directories
    $dirs = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/', // For classes in core like Database.php, Auth.php
    ];

    foreach ($dirs as $dir) {
        $file = $dir . str_replace('\\', '/', $className) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
