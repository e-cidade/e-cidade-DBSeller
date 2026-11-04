<?php

namespace App\Domain\Tributario\ISSQN\Factories\Reports;

use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\ParIssqn;
use App\Domain\Tributario\ISSQN\Reports\Alvara\AgataDocumentTemplateAlvara;
use App\Domain\Tributario\ISSQN\Reports\Alvara\AlvaraBaseReport;
use App\Domain\Tributario\ISSQN\Reports\Alvara\DefaultAlvara;
use App\Domain\Tributario\ISSQN\Reports\Alvara\PreviouslyPrintedAlvara;
use App\Domain\Tributario\ISSQN\Reports\Alvara\WordDocumentTemplateAlvara;

class GenerateAlvaraFactory
{
    /**
     * @throws \BusinessException
     */
    public static function get(IssBase $issBase, $hasDatabaseTransaction = false)
    {
        $parIssqn = ParIssqn::first();

        switch ($parIssqn->q60_modalvara) {
            case AlvaraBaseReport::DOCUMENTO_ALVARA:
                return GenerateAlvaraFactory::getDocumentTemplateInstance($issBase, $hasDatabaseTransaction);
            case AlvaraBaseReport::ALVARA_PRE_IMPRESSO:
                return new PreviouslyPrintedAlvara($issBase);
            case AlvaraBaseReport::ALVARA_TAMANHO_A5:
            case AlvaraBaseReport::ALVARA_TAMANHO_A4:
            case AlvaraBaseReport::ALVARA_A4_FONTE_REDUZIDA:
            case AlvaraBaseReport::ALVARA_PRE_IMPRESSO_TAMANHO_A4:
            case AlvaraBaseReport::ALVARA_PRE_IMPRESSO_TAMANHO_A4_CNAE:
            case AlvaraBaseReport::ALVARA_A4_FRENTE_VERSO:
            case AlvaraBaseReport::ALVARA_TAMANHO_A4_PROCESSO_AREA:
                return new DefaultAlvara($issBase, $parIssqn->q60_modalvara);
            default:
                throw new \BusinessException("Modelo não implementado.");
        }
    }

    /**
     * @param $issBase
     * @return AlvaraBaseReport
     */
    private static function getDocumentTemplateInstance($issBase, $hasDatabaseTransaction)
    {
        $isAgataTemplateEnabled = true;

        if ($isAgataTemplateEnabled) {
            return new AgataDocumentTemplateAlvara($issBase, $hasDatabaseTransaction);
        }

        return new WordDocumentTemplateAlvara($issBase);
    }
}
