<?php

namespace ECidade\File\Csv;

/**
 * Lê um arquivo CSV usando Generator para melhorar a performace em arquivos muito grandes.
 */
class LerCsv extends Csv
{
    /**
     * @var resource
     */
    private $file;

    public function __construct($file)
    {
        $this->file = fopen($file, "r");
    }

    /**
     * @return \Generator
     */
    public function read()
    {
        while ($result = fgetcsv($this->file, 0, $this->delimiter, $this->enclosure)) {
            if (!feof($this->file)) {
                yield $result;
            }
        }
    }

    public function __destruct()
    {
        fclose($this->file);
    }
}
