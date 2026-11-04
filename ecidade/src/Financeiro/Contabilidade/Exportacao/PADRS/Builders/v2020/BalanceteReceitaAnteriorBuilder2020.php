<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2020;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2020\BalanceteReceitaAnterior;

class BalanceteReceitaAnteriorBuilder2020 extends PadBuilder
{
    /**
     * @var BalanceteReceitaAnterior
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new BalanceteReceitaAnterior();
    }

    public function build()
    {
        $this->create();
        $this->processar();

        return $this->layout;
    }

    protected function processar()
    {
        $elemento = array_key_exists('o70_codrec', $this->dados) ? $this->dados['o70_codrec'] : null;
        $nivel = $this->dados['nivel'];

        $descricao = $this->dados['o57_descr'];
        if (empty($descricao)) {
            $descricao = 'Descrição nao localizada - Migração';
        }

        if (!empty($elemento) && substr($elemento, 0, 1) != 9) {
            --$nivel;
        }

        $tipo = empty($this->dados['o70_codrec']) ? 'S' : 'A';
        $this->layout->setCodigoReceita($this->formatEstruturalReceita($this->dados['fonte'], 20));
        $this->layout->setOrgaoUnidade($this->formataNumerico($this->dados['orgao_unidade'], 4));
        $this->layout->setSaldoInicial($this->formataValor($this->dados['saldo_inicial'], 13));
        $this->layout->setSaldoArrecadadoAcumulado($this->formataValor($this->dados['saldo_arrecadado_acumulado'], 13));
        $this->layout->setFonteRecurso($this->formataNumerico($this->dados['recurso'], 4));
        $this->layout->setDescricao($this->formataCaractere($descricao, 170));
        $this->layout->setTipo($tipo);
        $this->layout->setNivel($this->formataNumerico($nivel, 2));
        $this->layout->setCaracteristicaPeculiar($this->formataNumerico($this->dados['o70_concarpeculiar'], 3));
        $this->layout->setComplemento($this->formataNumerico($this->dados['complemento'], 4));
    }
}
