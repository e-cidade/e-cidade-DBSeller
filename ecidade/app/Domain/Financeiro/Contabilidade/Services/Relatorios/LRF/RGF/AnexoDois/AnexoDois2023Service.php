<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoDois;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use App\Domain\Financeiro\Contabilidade\Factories\TemplateFactory;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RGF\XlsAnexoDois;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\AnexosService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres\AnexoTresService;
use DBDate;
use Exception;
use Periodo;

/**
 *
 */
class AnexoDois2023Service extends AnexoDoisService
{
    /**
     * Em 2023 o mapeamento da linha 23 mudou, não precisando mais diminuir a linha 41
     * @return void
     */
    protected function calculaLinha23()
    {
        return;
    }
}
