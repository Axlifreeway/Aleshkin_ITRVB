<?php

namespace App\Logging;

interface ILoggerInterface {
    public function info(string $message): void;
    public function warning(string $message): void;
}
