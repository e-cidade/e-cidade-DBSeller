<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common;

class Logger
{
    private $logFilePath;

    public function __construct($logFilePath)
    {
        $this->logFilePath = $logFilePath;
        $this->clearFile();
    }

    public function log($message)
    {
        $logEntry = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

        file_put_contents($this->logFilePath, $logEntry, FILE_APPEND);
    }

    public function getPath()
    {
        return $this->logFilePath;
    }

    private function clearFile()
    {
        file_put_contents($this->logFilePath, '');
    }
}
