<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023\BalanceteReceita;

class BalanceteReceita2023Builder extends PadBuilder
{
    /**
     * @var BalanceteReceita
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new BalanceteReceita();
    }

    public function build()
    {
        $this->create();
        $this->processar();

        return $this->layout;
    }

    protected function processar()
    {
        $tipo = 'A';
        if (empty($this->dados['reduzido'])) {
            $tipo = 'S';
        }

        $saldoInicial = $this->formataValor($this->dados['valor_inicial'], 13, true);
        $arrecadadoAcumulado = $this->formataValor($this->dados['arrecadado_acumulado'], 13, true);
        $previsaoAtualizada = $this->formataValor($this->dados['previsao_atualizada'], 13, true);

        $this->layout->setFonte($this->formataNumerico($this->dados['natureza'], 20));
        $this->layout->setOrgaoUnidade($this->formataNumerico($this->dados['orgao_unidade'], 4));
        $this->layout->setSaldoInicial($saldoInicial);
        $this->layout->setArrecadadoAcumulado($arrecadadoAcumulado);
        $nomeReceita = substr(str_replace(["\r\n", "\n", "\r"], ['', '', ''], trim($this->dados['descricao'])), 0, 170);
        $this->layout->setNomeReceita($this->formataCaractere($nomeReceita, 170));
        $this->layout->setTipo($tipo);
        $this->layout->setNivel($this->formataNumerico($this->dados['nivel'], 2));
        $this->layout->setCp($this->formataNumerico($this->dados['cp'], 3));
        $this->layout->setPrevisaoAtualizada($previsaoAtualizada);
        $this->layout->setFonteRecurso($this->formataNumerico($this->dados['siconfi'], 4));
        $this->layout->setComplemento($this->formataNumerico($this->dados['complemento'], 4));

        if ($this->modeloMGS) {
            $this->layout->setCampoObsoleto1($this->formataNumerico($this->dados['subrecurso'], 4));
            $this->layout->setCampoObsoleto2($this->formataNumerico($this->dados['complemento'], 4));
        }
    }
}
