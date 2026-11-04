<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Factories;

use App\Domain\Configuracao\GeradorRelatorio\Contracts\Relatorio;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\RelatorioCSV;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\RelatorioPDF;
use App\Domain\Configuracao\GeradorRelatorio\Relatorios\RelatorioTXT;

class RelatorioFactory
{
    /**
     * @param $tipo
     * @return Relatorio
     * @throws \Exception
     */
    public static function get($tipo)
    {
        switch ($tipo) {
            case 'pdf':
                return new RelatorioPDF();
            case 'csv':
                return new RelatorioCSV();
            case 'txt':
                return new RelatorioTXT();
            default:
                throw new \Exception('Tipo de relatório não configurado', 400);
        }
    }
}
