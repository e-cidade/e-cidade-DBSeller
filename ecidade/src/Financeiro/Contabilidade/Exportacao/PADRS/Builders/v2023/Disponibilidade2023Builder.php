<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023\Disponibilidade;

class Disponibilidade2023Builder extends PadBuilder
{
    /**
     * @var Disponibilidade
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new Disponibilidade();
    }

    protected function processar()
    {
        $subRecurso = $this->dados['subrecurso'];
        $complementoAntigo = $this->dados['complemento'];
        $complementoValido = [3110, 3120, 3140, 3150, 3160, 1111, 1121, 2111, 2121, 0000];
        if (!in_array($this->dados['complemento'], $complementoValido)) {
            $complementoAntigo = '0000';
        }
        if ($this->dados['tem_saldo_anterior'] !== 't') {
            $subRecurso = '0000';
            $complementoAntigo = '0000';
        }

        $agencia = $this->buildAgencia();
        $conta = $this->buildConta();
        $this->layout->setEstrutural($this->formatar($this->dados['estrutural'], 20, '0', STR_PAD_LEFT))
            ->setOrgaoUnidade($this->formataNumerico($this->dados['orgao_unidade'], 4))
            ->setSubrecurso($this->formataNumerico($subRecurso, 4))
            ->setBanco($this->formataNumerico($this->dados['banco'], 5))
            ->setAgencia($this->formataNumerico($agencia, 5))
            ->setConta($this->formatar($conta, 20, '0', STR_PAD_LEFT))
            ->setTipoConta($this->buildTipoConta())
            ->setClassificacaoConta($this->buildClassificacaoConta())
            ->setComplementoAntigo($this->formataNumerico($complementoAntigo, 4))
            ->setSiconfi($this->formataNumerico($this->dados['siconfi'], 4))
            ->setComplemento($this->formataNumerico($this->dados['complemento'], 4));
    }

    protected function buildAgencia()
    {
        switch ($this->dados['banco']) {
            case '041':
            case '104':
                return $this->sanitize($this->dados['agencia']);
            default:
                $digitoAgencia = '';
                if (!empty($this->dados['digito_agencia'])) {
                    $digitoAgencia = $this->dados['digito_agencia'];
                }
                return $this->sanitize($this->dados['agencia'] . $digitoAgencia);
        }
    }

    protected function buildConta()
    {
        $digitoConta = '';
        if ($this->dados['digito_conta'] !== '' or !is_null($this->dados['digito_conta'])) {
            $digitoConta = $this->dados['digito_conta'];
        }
        switch ($this->dados['banco']) {
            case '104':
                $conta = $this->formataNumerico($this->sanitize($this->dados['conta'] . $digitoConta), 9);
                return $this->dados['codigo_operacao'] . $conta;
            default:
                return $this->sanitize($this->dados['conta'] . $digitoConta);
        }
    }

    protected function sanitize($string)
    {
        return str_replace(['-', '.'], ['', ''], $string);
    }

    /**
     * Tipo da Conta - SG
     * mesma lógica aplicada na versão antiga
     *
     * 1 Caixa
     * 2 Banco Conta Movimento
     * 3 Banco Conta Aplicação
     * 4 Dep. Sentenças Judiciais
     * 5 Dep. Judiciais de Restos a Pagar
     * @return int
     */
    protected function buildTipoConta()
    {
        $estrutural = substr($this->dados['estrutural'], 0, 7);
        if ($estrutural === '1111101') {
            return 1; // caixa
        }
        $estruturaPart = substr($this->dados['estrutural'], 0, 5);
        if (in_array($estrutural, ['1111106', '1111116', '1111130']) ||
            in_array($estruturaPart, ['11131', '11112'])) {
            return 2; // banco conta movimento
        }

        $estruturalPart = substr($this->dados['estrutural'], 0, 3);
        if ($estrutural === '1111150' ||
            ($estruturalPart === '114' && $this->dados['indicador_superavit'] === 'F')) {
            return 3; // banco conta aplicacao
        }

        $estruturalPart = substr($this->dados['estrutural'], 0, 11);
        if (in_array($estruturalPart, ['11251020001', '11251020002', '11251020003'])) {
            return 4; // deposito sentencas judiciais
        }
        if (in_array($estruturalPart, ['11251020004', '11251020005', '11251020006'])) {
            return 5; // depositos sentencas judiciais rp
        }

        return 2;
    }

    /**
     * Classificação das Contas Analíticas do Disponível
     * 1 Poder Executivo
     * 2 Poder Legislativo
     * 3 RPPS
     * 9 Outros
     * @return int
     */
    protected function buildClassificacaoConta()
    {
        switch ($this->dados['tipo_instituicao']) {
            case 1:
            case 3:
            case 4:
                return 1;
            case 2:
                return 2;
            case 5:
            case 6:
                return 3;
            default:
                return 9;
        }
    }
}
