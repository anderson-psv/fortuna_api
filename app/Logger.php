<?php

namespace Fortuna;

use Stringable;
use function error_log;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    /**
     * @param mixed             $level
     * @param string|Stringable $message
     * @param array<mixed>      $context
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function log($level, $message, array $context = []): void
    {
        $path = dirname(__DIR__) . '/log';

        if (!is_dir($path)) {
            mkdir($path);
        }

        $filename = $path . '/fortuna-' . date('Ymd') . '.log';

        if (is_array($message)) {
            $message = json_encode($message);
        }

        if ($context) {
            $context = json_encode($context);
        }

        $now = date('Y-m-d H:i:s');
        $message  = "[$now] - [$level] - $message - $context" . PHP_EOL;

        file_put_contents($filename, $message, FILE_APPEND);
    }
}
