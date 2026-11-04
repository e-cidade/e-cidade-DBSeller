<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoUm;

use Exception;

class AnexoUmInRsService extends AnexoUmService
{
    protected $sections = [
        'despesas' => [1, 25],
    ];

    /**
     * Mapa das linhas que totaliza outras linhas
     * @var \int[][]
     */
    protected $totalizar = [
        1 => [3, 4, 7, 8, 9, 10],
        2 => [3, 4],
        6 => [7, 8],
        11 => [12, 13, 14, 15, 16]
    ];

    protected $linhasSimplificado = [];

    /**
     * Mapeia as linhas do relatório que não deve executar o calculo do período.
     * @var int[]
     */
    protected $linhasNaoProcessar = [1, 2, 5, 6, 11, 17, 18, 21, 22, 23, 24, 25];

    protected $linhasNaoProcessarMensal = [21, 22, 23, 24, 25];

    /**
     * @var int[]
     */
    protected $linhasMensais = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17];

    /**
     * @param $filtros
     * @throws Exception
     */
    public function __construct($filtros)
    {
        //        $template = TemplateFactory::getTemplate(
        //            $filtros['codigo_relatorio'],
        //            $filtros['periodo'],
        //            TemplateFactory::MODELO_IN_RS
        //        );
        //
        //        $this->parser = new XlsAnexoUm($template);
        parent::__construct($filtros);
    }

    public function emitir()
    {
        $this->processar();
    }

    protected function posTotalizarLinhas()
    {
    }

    protected function processarPeriodo()
    {
        //        $this->alteraOrigemLinhas(self::ORIGEM_RP);
        //        $this->processaLinhas($this->linhas);
    }

    protected function processarMensal()
    {
        //        $this->alteraOrigemLinhas(self::ORIGEM_DESPESA);
        //        $this->processaLinhasMensais($this->linhas);
    }

    protected function processarLinhasRCL()
    {
        $service = $this->getServiceRCL();
        $valores = $service->getApuracaoCumprimentoLimiteLegal();

        $valorRCL = $valores[0]->total_meses;
        $this->linhas[18]->total_meses = $valorRCL;
        $this->linhas[19]->total_meses = $valores[1]->total_meses;
        $this->linhas[20]->total_meses = $valores[2]->total_meses;

        $this->linhas[21]->total_meses = $valorRCL - $this->linhas[19]->total_meses - $this->linhas[20]->total_meses;

        $this->calculaPercentualRCL($this->linhas[19], $valorRCL);
        $this->calculaPercentualRCL($this->linhas[20], $valorRCL);

        $inscricao_menos_anulacao_rp_nao_processado = $this->linhas[17]->inscricao_menos_anulacao_rp_nao_processado;
        $this->linhas[22]->total_meses = $this->linhas[17]->total_meses + $inscricao_menos_anulacao_rp_nao_processado;
        $this->calculaLinhaSoma($this->linhas[22], $this->linhas[20]);


        // LIMITE MÁXIMO (VIII) (incisos I, II e III, art. 20 da LRF) é 54% da linha
        // RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DA DESPESA COM PESSOAL (VII)= (IV-V-VI)
        $this->linhas[22]->percentual = 54;
        $this->linhas[22]->total_meses = round($this->linhas[21]->total_meses * 0.54, 2);
    }


    /**
     * Essa função foi criada para resolver o problema de ter duas origens de dados na mesma linha
     * @param $origem
     */
    private function alteraOrigemLinhas($origem)
    {
        foreach ($this->linhas as $linha) {
            if (in_array($linha->origem, $this->linhasNaoProcessar)) {
                continue;
            }

            $linha->origem = $origem;
        }
    }

    public function processaLinhasSimplificado()
    {
        // TODO: Implement processaLinhasSimplificado() method.
    }
}
