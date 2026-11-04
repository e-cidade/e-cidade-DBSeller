<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2022;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2022\LivroDiarioGeral;

class LivroDiarioGeralBuilder extends PadBuilder
{

    protected $numerosLotesGerados = [];
    /**
     * @var LivroDiarioGeral
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new LivroDiarioGeral();
    }

    protected function processar()
    {
        $historico = $this->dados['historico'];
        if (strlen($historico) > 150) {
            $historico = substr($historico, 0, 150);
        }

        $this->layout->setCodigoContabalanceteVerificao($this->formataNumerico($this->dados['estrutural'], 20));
        $this->layout->setOrgaoUnidade($this->formataNumerico($this->dados['orgaounidade'], 4));
        $this->layout->setLancamento($this->formataNumerico($this->dados['numerolancamento'], 12));
        $this->layout->setNumeroLote($this->formataNumerico($this->getNumeroLote(), 12));
        $this->layout->setNumeroDocumento($this->formataNumerico($this->dados['numerodocumento'], 13));
        $this->layout->setDataLancamento($this->formataData($this->dados['datalancamento']));
        $this->layout->setValor($this->formataValor($this->dados['valor'], 17));
        $this->layout->setTipoLancamento($this->dados['tipolancamento']);
        $this->layout->setHistorico($this->formataCaractere($historico, 150));
        $this->layout->setTipoDocumento($this->getTipoDocumento()); // usar c53_tipo
        $this->layout->setNatureza($this->dados['naturezainformacao']);
        $this->layout->setIndicadorSuperavitFinanceiro($this->dados['indicadorsuperavitfinanceiro']);
        $this->layout->setFonteRecurso($this->formataNumerico($this->dados['recurso'], 4));
        $this->layout->setComplemento($this->formataNumerico($this->dados['complemento_recurso'], 4));
    }

    protected function getTipoDocumento()
    {
        if (empty($this->dados['c53_tipo'])) {
            return 0;
        }
        switch ($this->dados['c53_tipo']) {
            case 10:
            case 11:
                return 1;
            case 20:
            case 21:
                return 3;
            case 30:
            case 31:
                return 2;
            default:
                return 9;
        }
    }

    /**
     * Valida se o número do lote é um inteiro, caso não seja, é gerado um novo valor para o mesmo
     * Lógica extraída do programa antigo que gera o arquivo do livro diário geral
     * @deprecated no redmine M23661
     */
    private function getNumeroLote()
    {
        $numeroLote = $this->dados['numerolote'];
        if (!preg_match('/^\d+$/', $numeroLote)) {
            if (!array_key_exists($numeroLote, $this->numerosLotesGerados)) {
                $this->numerosLotesGerados[$numeroLote] = $this->gerarNumeroLote();
            }
            $numeroLote = $this->numerosLotesGerados[$numeroLote];
        }

        return $numeroLote;
    }

    /**
     * Gera um número inteiro contendo 12 dígitos
     * @return int
     */
    private function gerarNumeroLote()
    {
        $novoNumero = mt_rand(100000000000, 999999999999);
        if (in_array($novoNumero, $this->numerosLotesGerados)) {
            $this->gerarNumeroLote();
        }
        return $novoNumero;
    }
}
