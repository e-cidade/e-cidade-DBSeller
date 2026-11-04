<?php
/**
 * Created by PhpStorm.
 * User: robson
 * Date: 2020-02-05
 * Time: 16:36
 */

namespace ECidade\Financeiro\Contabilidade\Relatorio\TCE\RO\IN22;

use ECidade\Library\SpreadSheet\Template\Parser;
use Periodo;

/**
 * Class Anexo2
 * @package ECidade\Financeiro\Contabilidade\Relatorio\TCE\RO\IN22
 */
class Anexo7 extends Base implements In22
{

    const CODIGO_RELATORIO = 212;
    const TEMPLATE = 'config/templates/IN22/anexo7.xlsx';

    /**
     * @var \RelatoriosLegaisBase
     */
    protected $relatorioLegal;

    /**
     * @var \stdClass[]
     */
    protected $linhas = array();

    /**
     * @return mixed|void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \ParameterException
     */
    public function processar()
    {
        $this->processarDados();
        $parser = new Parser();
        $parser->loadXLS(self::TEMPLATE);
        $mes = \DBDate::getMesExtenso($this->relatorioLegal->getPeriodo()->getMesInicial());
        $parser->addVariable('mes_emissao', $mes);
        $parser->addVariable('ano_emissao', $this->ano);
        $parser->addCollection('dados', $this->linhas);
        $parser->parse();
        $nomeArquivo = 'tmp/Anexo7_'.date('d_m_Y').'.xlsx';
        $parser->save($nomeArquivo);
        return $nomeArquivo;
    }

    /**
     * @return bool
     */
    private function processarDados()
    {
        $periodo = new Periodo($this->codigoPeriodo);
        $this->relatorioLegal = new \RelatoriosLegaisBase($this->ano, self::CODIGO_RELATORIO, $this->codigoPeriodo);
        $this->relatorioLegal->setInstituicoes(implode(',', $this->instituicoes));
        $this->relatorioLegal->setDataInicialPeriodo($periodo->getDataInicial($this->ano));
        $this->relatorioLegal->setDataInicial($this->relatorioLegal->getDataInicialPeriodo());

        $linhasRelatorio = $this->relatorioLegal->getDados();
        foreach ($linhasRelatorio as $linha) {
            $valorRelacao = 0;
            if (!empty($linha->ano)) {
                $valorRelacao = ($linha->mes / $linha->ano) * 100;
            }
            $this->linhas[] = (object)array(
                "origemrecurso" => $linha->descricao,
                "nomes" => $this->formataValor($linha->mes),
                "noano" => $this->formataValor($linha->ano),
                "relacao" => round($valorRelacao, 2),
            );
        }
        return true;
    }
}
