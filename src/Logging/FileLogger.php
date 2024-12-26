<?php

namespace App\Logging;

class FileLogger implements ILoggerInterface {
    private string $logFile;

    public function __construct(string $logFile) {
        $this->logFile = $logFile;
    }

    public function info(string $message): void {
        file_put_contents($this->logFile, "[INFO] $message" . PHP_EOL, FILE_APPEND);
    }

    public function warning(string $message): void {
        file_put_contents($this->logFile, "[WARNING] $message" . PHP_EOL, FILE_APPEND);
    }
}