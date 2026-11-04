<?php

namespace App\Domain\Saude\Farmacia\Relatorios;

use ECidade\Pdf\Pdf;

class ProtocoloBnafarPdf extends Pdf
{
    /**
     * @var array
     */
    private $dados;

    /**
     * @param array $dados
     */
    public function __construct(array $dados)
    {
        parent::__construct();

        $this->dados = $dados;
        $this->addTitulo('Relatório de Protocolos do BNAFAR.');
    }

    /**
     * @return string[]
     */
    public function imprimir()
    {
        $this->init();
        $this->imprimirCabecalho();
        $linhaImpressa = 0;
        foreach ($this->dados as $dados) {
            if ($this->getAvailableHeight() < 5) {
                $this->addPage();
                $this->imprimirCabecalho();
                $linhaImpressa = 0;
            }
            $this->setFont('ARIAL', '', 7);
            $cor = !!($linhaImpressa % 2);
            $this->cell(20, 5, $dados->protocolo, 0, 0, 'C', $cor);
            $this->cell(20, 5, $dados->codigoIbge, 0, 0, 'C', $cor);
            $this->cell(30, 5, $dados->usuarioEnvio, 0, 0, 'C', $cor);
            $this->cell(32, 5, $dados->dataProtocolo, 0, 0, 'C', $cor);
            $this->cell(30, 5, $dados->situacao, 0, 0, 'C', $cor);
            $this->cell(30, 5, $dados->tipoServico, 0, 0, 'C', $cor);
            $this->cell(30, 5, $dados->tipoOperacao, 0, 1, 'C', $cor);
            $linhaImpressa++;
        }

        return $this->emitir();
    }

    /**
     * @return string[]
     */
    public function emitir()
    {
        $path = 'tmp/protocolo-bnafar' . time() . '.pdf';
        $this->output('F', $path);

        return [
            'name' => 'Relatório de Protocolos BNAFAR',
            'path' => $path,
            'pathExterno' => ECIDADE_REQUEST_PATH . $path
        ];
    }

    private function imprimirCabecalho()
    {
        $this->setFont('ARIAL', 'B', 8);
        $this->cell(20, 5, 'PROTOCOLO', 1, 0, 'C', 1);
        $this->cell(20, 5, 'IBGE', 1, 0, 'C', 1);
        $this->cell(30, 5, 'USUÁRIO BNAFAR', 1, 0, 'C', 1);
        $this->cell(32, 5, 'DATA', 1, 0, 'C', 1);
        $this->cell(30, 5, 'SITUAÇÃO', 1, 0, 'C', 1);
        $this->cell(30, 5, 'SERVIÇO', 1, 0, 'C', 1);
        $this->cell(30, 5, 'OPERAÇÃO', 1, 1, 'C', 1);
    }
}
