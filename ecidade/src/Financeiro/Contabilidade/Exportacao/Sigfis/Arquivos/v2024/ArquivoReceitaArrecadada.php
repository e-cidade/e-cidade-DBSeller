<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use db_utils;

class ArquivoReceitaArrecadada extends ArquivoBase
{
    protected $sNomeArquivo  = 'ReceitaArrecadada';

    /**
    * Busca os dados para gerar o Arquivo de Unidade Or�ament�ria
    */
    public function gerarDados()
    {

        $daoConlancam = new \cl_conlancam;
        $campos = " distinct substr(planoreceita.conta,1,8) as codigo_item_receita,";
        $campos .= "codigo_siconfi,o15_complemento as complemento,c70_anousu,";
        $campos .= "RANK() OVER (ORDER BY substr(planoreceita.conta,1,8),codigo_siconfi) AS grupo_receita,";
        $campos .= "extract(YEAR from c70_data) || to_char(c70_data,'MM')  as competencia,";
        $campos .= " case when substr(o57_fonte, 1, 1) = '4' then 1
                           when substr(o57_fonte, 1, 1) = '9' and substr(o57_fonte, 1, 3) <> '917' then 2
                           when substr(o57_fonte, 1, 3) = '917' then 3 else 0 end as deducao";

        $where = " o70_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $where .= " and c70_anousu = {$this->iAnoUsu}";
        $where .= " and c70_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";
        $where .= " and c53_tipo in (100,101)";

        $sSqlConlancam = $daoConlancam->sql_query_receita_planoreceita($campos, null, $where);
        $rsConlancam   = db_query($sSqlConlancam);

        if (pg_num_rows($rsConlancam) == 0) {
            throw new \Exception("Nenhum registro encontrado para o período");
        }
        
        if (empty($this->sCodigoTribunal)) {
            throw new \Exception("O código do tribunal deve ser informado para geração do arquivo");
        }

        $RemessaReceitaArrecadada = new \stdClass();
        $receitaArrecadada = array();

        $valorArrecadado = 0;
        for ($i = 0; $i < pg_num_rows($rsConlancam); $i++) {
            $oDadosQuery = db_utils::fieldsMemory($rsConlancam, $i);


            $campos = "sum(c70_valor) as valor,c53_tipo";
            $whereValorArrecadado = $where ." and substr(planoreceita.conta,1,8) = ";
            $whereValorArrecadado .= "'{$oDadosQuery->codigo_item_receita}'";
            $whereValorArrecadado .= " and codigo_siconfi = '{$oDadosQuery->codigo_siconfi}' ";

            if ($oDadosQuery->deducao == 1) {
                $whereValorArrecadado .= " and substr(o57_fonte, 1, 1) = '4'";
            } elseif ($oDadosQuery->deducao == 2) {
                $whereValorArrecadado .= " and substr(o57_fonte, 1, 1) = '9'
                        and substr(o57_fonte, 1, 3) <> '917'";
            } else {
                $whereValorArrecadado .= " and substr(o57_fonte, 1, 3) = '917'";
            }

            $groupBy = "c53_tipo";

            $sqlValorArrecadado = $daoConlancam->sql_query_receita_planoreceita(
                $campos,
                null,
                $whereValorArrecadado,
                $groupBy
            );

            $rsValorArrecadado = db_query($sqlValorArrecadado);

            $valorArrecadado = 0;
            $tipoMaiorArrecadado = '';
            if (pg_num_rows($rsValorArrecadado)) {
                for ($j =0; $j<pg_num_rows($rsValorArrecadado); $j++) {
                    $tipoArrecadacao = db_utils::fieldsMemory($rsValorArrecadado, $j);

                    if ($tipoArrecadacao->valor > abs($valorArrecadado)) {
                        $tipoMaiorArrecadado = $tipoArrecadacao->c53_tipo;
                    }

                    if ($tipoArrecadacao->c53_tipo == '101') {
                        $valorArrecadado -= $tipoArrecadacao->valor;
                    } else {
                        $valorArrecadado += $tipoArrecadacao->valor;
                    }
                }
            }

            $identificador = substr($oDadosQuery->c70_anousu, 2, 2);
            $identificador .= date('m', db_getsession('DB_datausu'));
            $identificador .= $oDadosQuery->grupo_receita;
            $identificador .= $oDadosQuery->deducao;

            $estorno = "";
            if (in_array($oDadosQuery->deducao, [2,3]) && $tipoMaiorArrecadado == '100') {
                $estorno = 'Sim';
            } elseif ($oDadosQuery->deducao == 1 && $tipoMaiorArrecadado == '101') {
                $estorno = 'Sim';
            } else {
                $estorno = 'Nao';
            }

            $complemento = $oDadosQuery->complemento;

            if ($oDadosQuery->complemento == '0') {
                $complemento = '0000';
            }

            $oDadosReceitaArrecadada = new \stdClass();
            $oDadosReceitaArrecadada->Identificador = $identificador;
            $oDadosReceitaArrecadada->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $oDadosReceitaArrecadada->Competencia = $oDadosQuery->competencia;
            $oDadosReceitaArrecadada->CodigoItemReceita =$oDadosQuery->codigo_item_receita ;
            $oDadosReceitaArrecadada->FonteRecursos = $oDadosQuery->codigo_siconfi;
            $oDadosReceitaArrecadada->Valor = abs($valorArrecadado);
            $oDadosReceitaArrecadada->Deducao = $oDadosQuery->deducao;
            $oDadosReceitaArrecadada->Estorno = $estorno;
            $oDadosReceitaArrecadada->CodigoAcompanhamentoExecucaoOrcamentaria = $complemento;

            $receitaArrecadada[] =  (object) ['ReceitaArrecadada' => $oDadosReceitaArrecadada];
        }

            $RemessaReceitaArrecadada->ReceitasArrecadadas = $receitaArrecadada;
            $this->aDados= $RemessaReceitaArrecadada;
    }
}
