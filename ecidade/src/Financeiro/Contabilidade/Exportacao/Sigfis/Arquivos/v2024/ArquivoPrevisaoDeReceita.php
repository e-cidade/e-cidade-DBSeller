<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use BusinessException;
use db_utils;
use stdClass;

class ArquivoPrevisaoDeReceita extends ArquivoBase
{
    protected $sNomeArquivo  = 'PrevisaoDeReceita';

    public function gerarDados()
    {
        $oDaoReceita = new \cl_orcreceita;

        $campos = "sum(receita.o70_valor) as valor";
        $where = " where substr(plrec.conta,1,8) = substr(planoreceita.conta,1,8)";
        $where .= " and receita.o70_anousu = {$this->iAnoUsu}";
        $where .= " and receita.o70_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $where .= " and fontrec.codigo_siconfi = fonterecurso.codigo_siconfi";

        $where1 = $where . " and substr(o57_fonte,1,1) = '4'";
        $where2 = $where . " and substr(o57_fonte,1,1) = '9' and substr(o57_fonte,1,3) <> '917'";
        $where3 = $where . " and substr(o57_fonte,1,3) = '917'";

        $groupBy = "group by fontrec.codigo_siconfi,plrec.conta";

        $sqlSubQueryReceitaSigfis = "
              select $campos
                  from orcreceita receita
            inner join orcfontes fontes on fontes.o57_codfon = receita.o70_codfon
                                       and fontes.o57_anousu = receita.o70_anousu
            inner join conplanoorcamento orcamento on orcamento.c60_codcon = fontes.o57_codfon
                                                  and orcamento.c60_anousu = fontes.o57_anousu
            inner join planoreceitaconplanoorcamento plorcamento on plorcamento.conplanoorcamento_codigo
                = orcamento.c60_codigo
            inner join planoreceita plrec on plrec.id = plorcamento.planoreceita_id
                                         and plrec.exercicio = receita.o70_anousu
                                         and plrec.uniao = 't'
            inner join orctiporec recurso on recurso.o15_codigo = receita.o70_codigo
            inner join fonterecurso fontrec on fontrec.orctiporec_id = recurso.o15_codigo
                                   and fontrec.exercicio = receita.o70_anousu

        ";
        $sqlSubQueryReceitaSigfis1 = $sqlSubQueryReceitaSigfis.$where1.$groupBy;
        $sqlSubQueryReceitaSigfis2 = $sqlSubQueryReceitaSigfis.$where2.$groupBy;
        $sqlSubQueryReceitaSigfis3 = $sqlSubQueryReceitaSigfis.$where3.$groupBy;

        $campos = "distinct substr(planoreceita.conta,1,8) as codigo_item_receita,o70_anousu,";
        $campos .= "RANK() OVER (ORDER BY substr(planoreceita.conta,1,8),codigo_siconfi) AS grupo_receita,";
        $campos .= "substr(codigo_siconfi,1,4) as codigo_siconfi,";
        $campos .= "($sqlSubQueryReceitaSigfis1) as valor_deducao1,";
        $campos .= "($sqlSubQueryReceitaSigfis2) as valor_deducao2,";
        $campos .= "($sqlSubQueryReceitaSigfis3) as valor_deducao3,";
        $campos .= "case when substr(o57_fonte,1,1) = '4' then 1
                        when substr(o57_fonte,1,1) = '9' and substr(o57_fonte,1,3) <> '917' then 2
                         when  substr(o57_fonte,1,3) = '917' then 3 else 0 end as deducao";

        $where  = "o70_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $where .= " and o70_anousu = ".$this->iAnoUsu;
        $where .= " and o70_datacriacao between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";
 
        $orderBy = "codigo_item_receita,deducao";


        $sqlReceitaPlano =  $oDaoReceita->sql_query_receita_planoreceita($campos, $orderBy, $where, null);
        $rsReceitaPlano = db_query($sqlReceitaPlano);
        if (pg_num_rows($rsReceitaPlano) == 0) {
            throw new BusinessException("Nenhum registro encontrado");
        }

        $RemessaPrevisaoDeReceita = new stdClass();
        $previsaoReceitas = [];

        for ($i=0; $i<pg_num_rows($rsReceitaPlano); $i++) {
            $receita = db_utils::fieldsMemory($rsReceitaPlano, $i);

            $oDadosPrevisaoReceita = new stdClass();

            $identificador = substr($receita->o70_anousu, 2, 2);
            $identificador .= date('m', db_getsession('DB_datausu'));
            $identificador .= $receita->grupo_receita;
            $identificador .= $receita->deducao;


            $oDadosPrevisaoReceita->Identificador = $identificador;

            $oDadosPrevisaoReceita->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $oDadosPrevisaoReceita->Ano = $receita->o70_anousu;
            $oDadosPrevisaoReceita->CodigoItemReceita = $receita->codigo_item_receita;
            $oDadosPrevisaoReceita->FonteRecursos = $receita->codigo_siconfi;

            //Tipo 1 nao eh deducao

            if ($receita->deducao == 1) {
                $valor = $receita->valor_deducao1;
            } elseif ($receita->deducao == 2) {
                $valor = abs($receita->valor_deducao2);
            } elseif ($receita->deducao == 3) {
                $valor = abs($receita->valor_deducao3);
            }

            $oDadosPrevisaoReceita->ValorPrevisaoReceita = $valor;
            $oDadosPrevisaoReceita->Deducao = $receita->deducao;

            $previsaoReceitas[] = (object) ['PrevisaoDeReceita' => $oDadosPrevisaoReceita];
        }
        $RemessaPrevisaoDeReceita->PrevisaoDeReceitas = $previsaoReceitas;
        $this->aDados = $RemessaPrevisaoDeReceita;
    }
}
