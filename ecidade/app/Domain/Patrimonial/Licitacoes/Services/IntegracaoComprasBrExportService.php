<?php

namespace App\Domain\Patrimonial\Licitacoes\Services;

use ECidade\File\Csv\Dumper\Dumper;
use InstituicaoRepository;
use LicitacaoRepository;

class IntegracaoComprasBrExportService
{
    /**
     * Dados da licitacao
     *
     * @var licitacao $licitacao
     */
    public $licitacao;

    /**
     * Dados dos itens da licitacao
     *
     * @var ItemLicitacao
     */
    public $licitacaoItens;

    /**
     * Numero de casas decimais
     *
     * @var int|null
     */
    public $casasDecimais= null;

    /**
     * Exportar arquivo para pregao compras BR
     *
     * @param int $licitacao Codigo da licitacao (l20_codigo)
     * @return array
     */
    public function exportFilePregao($licitacao)
    {
        $this->licitacao = LicitacaoRepository::getByCodigo($licitacao);
        $this->licitacaoItens = $this->licitacao->getItens();

        $data = $this->buildDataLicitacao();

        return $this->processFileExport($data);
    }

    /**
     * Seta numero de casas decimais
     * no valor unitario dos itens
     *
     * @param int $casasDecimais
     * @return void
     */
    public function setCasasDecimais($casasDecimais)
    {
        $this->casasDecimais = $casasDecimais;
    }

    /**
     * Processamento para geracao de arquivo
     *
     * @return array
     */
    private function processFileExport($data, $delimiter = '|', $extension = 'txt')
    {
        $dumper = new Dumper;
        $dumper->setCsvControl($delimiter);

        $filename = sprintf(
            'tmp/import-comprasbr-lic%s-%s.%s',
            $this->licitacao->getCodigo(),
            time(),
            $extension
        );

        $dumper->dumpToFile($data, $filename);

        return [
            'file' => $filename,
            'path' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    /**
     * Monta array com os dados da licitacao a serem processados
     *
     * @return array
     */
    private function buildDataLicitacao()
    {
        $licitacao       = $this->licitacao;
        $licitacaoCampos = $licitacao->getDados();
        $tipoJulgamento  = $licitacao->getTipoJulgamento();
        $data            = [];

        /**
         * Edital
         */
        $iInstituicao = InstituicaoRepository::getInstituicaoByCodigo($licitacaoCampos->l20_instit);
        $data['edital']['tipo_registro']  = 1;
        $data['edital']['numero_edital']  = $licitacao->getEdital();
        $data['edital']['ano_edital']     = $licitacao->getAno();
        $data['edital']['processo']       = $licitacao->getProcesso();
        $data['edital']['orgao']          = $iInstituicao->getDescricao();
        $data['edital']['registro_preco'] = $licitacao->usaRegistroDePreco() ? 1 : 0;
        $data['edital']['meses']          = 0;

        /**
         * Lote Global
         */
        if ($tipoJulgamento == 2) {
            $data['lote'] = $this->buildLote(1, 'Lote 1');
        }

        /**
         * Itens da licitação
         */
        $itens = $licitacao->getItens();
        foreach ($itens as $item) {
            $itemSolicitacao = $item->getItemSolicitacao();
            $lote            = $item->getLoteLicitacao();
            $unidade         = $itemSolicitacao->getDadosUnidade();
            $itemTitulo      = urldecode($itemSolicitacao->getDescricaoMaterial());
            $itemDescr       = urldecode($itemSolicitacao->getResumo());

            $preco = $itemSolicitacao->getValorUnitario();
            $preco = ($this->casasDecimais)
                ? number_format($preco, $this->casasDecimais, '.', '')
                : $preco;

            // lote por item
            if ($tipoJulgamento != 2) {
                $data[] = $this->buildLote($lote->getCodigo(), $lote->getDescricao());
            }

            $data[] = [
                'tipo_registro'    => 3,
                'numero_edital'    => $licitacao->getEdital(),
                'ano_edital'       => $licitacao->getAno(),
                'numero_lote'      => ($tipoJulgamento != 2) ? $lote->getCodigo() : 1,
                'numero_item'      => $item->getOrdem(),
                'unidade'          => $unidade->m61_abrev,
                'quantidade'       => $itemSolicitacao->getQuantidade(),
                'preco_referencia' => $preco,
                'especificacao'    => (empty($itemDescr)) ? $itemTitulo : $itemTitulo . ' (' . $itemDescr . ')'
            ];
        }

        return $data;
    }

    /**
     * Monta array com dados do lote
     *
     * @param string $loteNumero
     * @param string $loteDescricao
     * @return array
     */
    private function buildLote($loteNumero, $loteDescricao)
    {
        $licitacao = $this->licitacao;
        $licitacaoCampos = $licitacao->getDados();
        $lote = [];

        $lote['tipo_registro']  = 2;
        $lote['numero_edital']  = $licitacao->getEdital();
        $lote['ano_edital']     = $licitacao->getAno();
        $lote['numero_lote']    = $loteNumero;
        $lote['descricao_lote'] = $loteDescricao;
        $lote['local_entrega']  = $licitacaoCampos->l20_localentrega;
        $lote['data_entegra']   = $licitacaoCampos->l20_prazoentrega;
        $lote['garantia']       = 0;

        return $lote;
    }
}
