<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023\Empenho;

class Empenho2023Builder extends PadBuilder
{
    /**
     * @var Empenho
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new Empenho();
    }

    protected function processar()
    {
        $this->layout->setCodigoOrgao(formatar($this->dados['o58_orgao'], 2, 'n'));
        $this->layout->setCodigoUnidade(formatar($this->dados['o58_unidade'], 2, 'n'));
        $this->layout->setCodigoFuncao(formatar($this->dados['o58_funcao'], 2, 'n'));
        $this->layout->setCodigoSubfuncao(formatar($this->dados['o58_subfuncao'], 3, 'n'));
        $this->layout->setCodigoPrograma(formatar($this->dados['o58_programa'], 4, 'n'));
        $this->layout->setCodigoProjeto(formatar($this->dados['o58_projativ'], 5, 'n'));
        $this->layout->setCodigoRubrica(formatar($this->dados['elemento'], 15, 'n'));

        $numero_empenho = sprintf(
            '%s%s%s',
            $this->dados['e60_anousu'],
            str_pad($this->dados['e60_instit'], 2, '0', STR_PAD_LEFT),
            formatar($this->dados['e60_codemp'], 7, 'n')
        );

        $historico = str_replace("\n", " ", $this->dados['e60_resumo']);
        $historico = str_replace("\r", "", $historico);
        $historico = formatar($historico, 400, 'c');

        $registroPreco = str_pad($this->dados['registroPreco'], 1, ' ', STR_PAD_LEFT);
        $numeroLicitacao = str_pad($this->dados['numeroLicitacao'], 20, '0', STR_PAD_LEFT);

        $this->layout->setNumeroEmpenho($numero_empenho);
        $this->layout->setDataEmpenho(formatar($this->dados['e60_emiss'], 8, 'd'));
        $this->layout->setValorEmpenho(formatar($this->dados['valor_empenho'], 13, 'v'));
        $this->layout->setSinalValor($this->dados['sinal']);
        $this->layout->setCodigoCredor(formatar($this->dados['e60_numcgm'], 10, 'n'));

        $cp = formatar($this->dados['e60_concarpeculiar'], 3, 'n');
        $cpsAceitas = ['000', '502', '901', '902', '903', '904', '905', '906'];
        if (!in_array($cp, $cpsAceitas)) {
            $cp = '000';
        }
        $this->layout->setCaracteristicaPeculiar($cp);
        $this->layout->setRegistroPrecos($registroPreco);
        $this->layout->setNumeroLicitacao($numeroLicitacao);
        $this->layout->setAnoLicitacao(str_pad($this->dados['anoLicitacao'], 4, '0', STR_PAD_LEFT));
        $this->layout->setHistoricoEmpenho($historico);

        $this->layout->setModalidadeLicitacao(str_pad($this->dados['sigla'], 2, '0', STR_PAD_LEFT));
        $this->layout->setBaseLegalContratacao($this->dados['baseLegalContratacao']);
        $this->layout->setIdentificadorDespesaFuncionario($this->dados['identificadorDespesaFuncionario']);
        $this->layout->setLicitacaoCompartilhada($this->dados['licitacaoCompartilhada']);
        $this->layout->setCnpjOrgaoGerenciadorLicitacao(str_pad($this->dados['cnpj'], 14, '0', STR_PAD_LEFT));

        $subrecurso = $this->dados['recurso'];
        $fonteSiconfi = substr($this->dados['codigo_siconfi'], 1);

        $complementoVinculado = $this->dados['complemento'];
        $complemento = $this->dados['complemento'];

        if ($this->dados['e60_anousu'] < 2023) {
           // $fonteSiconfi = '0000';
            $complemento = '0';

            if (!in_array($complementoVinculado, [3110, 3120, 3140, 3150, 3160, 1111, 1121, 2111, 2121, 0])) {
                $complementoVinculado = '0';
            }
        }
        if ($this->dados['e60_anousu'] >= 2023) {
            $subrecurso = '0000';
            $complementoVinculado = '0';
        }


        $complementoVinculado = str_pad($complementoVinculado, 4, '0', STR_PAD_LEFT);
        $complemento = str_pad($complemento, 4, '0', STR_PAD_LEFT);

        // recurso e complemento de empenhos anterior a 2023
        $this->layout->setCodigoRecursoVinculado($subrecurso);
        $this->layout->setContrapartidaRecurso($subrecurso);
        $this->layout->setComplementoVinculado($complementoVinculado);

        // recurso e complemento de empenhos anterior a 2023
        $this->layout->setCodigoFonteRecurso($this->formataNumerico($fonteSiconfi, 4));
        $this->layout->setComplemento($complemento);

        if ($this->modeloMGS) {
            $this->layout->setCodigoRecursoVinculado($this->dados['recurso']);
            $this->layout->setContrapartidaRecurso($this->dados['recurso']);
            $this->layout->setComplementoVinculado($this->dados['complemento']);
        }
    }
}
