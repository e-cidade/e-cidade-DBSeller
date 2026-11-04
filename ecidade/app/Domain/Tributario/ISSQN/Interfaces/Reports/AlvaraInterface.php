<?php

namespace App\Domain\Tributario\ISSQN\Interfaces\Reports;

interface AlvaraInterface
{
    public function generate();
    public function getBase64($deleteFile = true);
}
