<?php
spl_autoload_register(function ($class) {
    // Замена разделителя пространства имен на DIRECTORY_SEPARATOR
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    // Замена символа _ на DIRECTORY_SEPARATOR
    $class = str_replace('_', DIRECTORY_SEPARATOR, $class);

    // Определение пути к файлу
    $file = __DIR__ . '/src/' . $class . '.php';

    // Подключение файла, если он существует
    if (file_exists($file)) {
        require_once $file;
    } else {
        throw new Exception("Class file for $class not found.");
    }
});
