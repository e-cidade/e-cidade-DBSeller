<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Enum\Financeiro\Contabilidade\PcaspIndicadorSuperavitEnum;
use ECidade\Enum\Financeiro\Contabilidade\PcaspNaturezaInformacaoEnum;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2022\LivroDiarioGeralBuilder;

class LivroDiarioGeral2023Builder extends LivroDiarioGeralBuilder
{
    protected function processar()
    {
        parent::processar();

        $siconfi = substr($this->dados['codigo_siconfi'], 1);

        $subrecurso = $this->dados['recurso'];
        $complementoAntigo = $this->dados['complemento_recurso'];
        $complementoValido = [3110, 3120, 3140, 3150, 3160, 1111, 1121, 2111, 2121, 0000];
        if (!in_array($this->dados['complemento_recurso'], $complementoValido)) {
            $complementoAntigo = '0000';
        }

        if ($this->dados['tem_encerramento'] !== 't') {
            $subrecurso = '0000';
            $complementoAntigo = '0000';
        }
        $natureza = PcaspNaturezaInformacaoEnum::getPorEstrutural($this->dados['estrutural'])->value();
        $indicador = PcaspIndicadorSuperavitEnum::getInstance($this->dados['indicadorsuperavitfinanceiro'])->value();

        // alterado para o numero do lançamento ser o campo c69_sequen e o lote ser o código do lançamento
        $this->layout->setNatureza($natureza);
        $this->layout->setLancamento($this->formataNumerico($this->dados['sequencial_lancamento'], 12));
        $this->layout->setNumeroLote($this->formataNumerico($this->dados['numerolancamento'], 12));
        $this->layout->setIndicadorSuperavitFinanceiro($indicador);
        $this->layout->setFonteRecurso($this->formataNumerico($subrecurso, 4));
        $this->layout->setComplemento($this->formataNumerico($complementoAntigo, 4));
        $this->layout->setFonteRecursoSiconfi($this->formataNumerico($siconfi, 4));
        $this->layout->setComplementoSiconfi($this->formataNumerico($this->dados['complemento_recurso'], 4));
    }
}
