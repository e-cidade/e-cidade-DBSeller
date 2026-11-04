<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;

class ArquivoBalancete extends ArquivoBase
{
    protected $sNomeArquivo = 'Balancete';

    public function gerarDados()
    {
        $dados = (array)$this->contasPlanoUniao();
        if (empty($dados)) {
            throw new \Exception('Não foi encontrado dado para emissão do arquivo da Remessa "Balancete".');
        }

        $obj = new \stdClass();
        $obj->Balancetes = [];
        $identificador = 1;
        $contasRP = [];

        foreach ($dados as $dado) {
            $obj->Balancetes[] = (object)['Balancete' => $this->montaObjeto(
                $identificador,
                $dado,
                $dado->saldo_anterior,
                $dado->natureza_saldo_anterior,
                1
            )];
            $identificador++;

            if ($dado->saldo_debito > 0) {
                $obj->Balancetes[] = (object)['Balancete' => $this->montaObjeto(
                    $identificador,
                    $dado,
                    $dado->saldo_debito,
                    'D',
                    2
                )];
            }
            $identificador++;

            if ($dado->saldo_credito != 0) {
                $obj->Balancetes[] = (object)['Balancete' => $this->montaObjeto(
                    $identificador,
                    $dado,
                    $dado->saldo_credito,
                    'C',
                    2
                )];
            }
            $identificador++;


            $obj->Balancetes[] = (object)['Balancete' => $this->montaObjeto(
                $identificador,
                $dado,
                $dado->saldo_final,
                $dado->natureza_saldo_final,
                3
            )];
            $identificador++;

            /*
             * Caso o estrutural iniciar com 53 ou 63 somamos os valores para posterior verificacao de divergencias
             */
            if (in_array(substr($dado->estrutural_padrao, 0, 2), [53,63])) {
                if (!key_exists($dado->estrutural_padrao, $contasRP)) {
                    $contaRP = $dado;
                    $contaRP->reduzidos[] = $dado->reduzido;
                    $contasRP[$dado->estrutural_padrao] = $contaRP;
                } else {
                    $contasRP[$dado->estrutural_padrao]->saldo_anterior           += $dado->saldo_anterior;
                    $contasRP[$dado->estrutural_padrao]->saldo_debito             += $dado->saldo_debito;
                    $contasRP[$dado->estrutural_padrao]->saldo_credito            += $dado->saldo_credito;
                    $contasRP[$dado->estrutural_padrao]->saldo_final              += $dado->saldo_final;
                    $contasRP[$dado->estrutural_padrao]->saldo_anterior_balancete += $dado->saldo_anterior_balancete;
                    $contasRP[$dado->estrutural_padrao]->saldo_debito_balancete   += $dado->saldo_debito_balancete;
                    $contasRP[$dado->estrutural_padrao]->saldo_credito_balancete  += $dado->saldo_credito_balancete;
                    $contasRP[$dado->estrutural_padrao]->saldo_final_balancete    += $dado->saldo_final_balancete;
                    if (!in_array($dado->reduzido, $contasRP[$dado->estrutural_padrao]->reduzidos)) {
                        $contasRP[$dado->estrutural_padrao]->reduzidos[] = $dado->reduzido;
                    }
                }
            }
        }


        /*
         * Percorremos as contas apontadas como RP
         * Verificando se existe diferenca nos valores, caso encontrado, geramos uma linha com o valor da diferenca
         */
        foreach ($contasRP as $conta) {
            if ($conta->saldo_anterior != $conta->saldo_anterior_balancete) {
                $diferenca = $conta->saldo_anterior_balancete - $conta->saldo_anterior;

                $obj->Balancetes[] = (object)['Balancete' => $this->montaObjeto(
                    $identificador,
                    $conta,
                    $diferenca,
                    $conta->natureza_saldo_anterior,
                    1
                )];
                $identificador++;
            }

            if ($conta->saldo_debito != $conta->saldo_debito_balancete) {
                $diferenca = $conta->saldo_debito_balancete - $conta->saldo_debito;

                $obj->Balancetes[] = (object)['Balancete' => $this->montaObjeto(
                    $identificador,
                    $conta,
                    $diferenca,
                    $conta->natureza_saldo_anterior,
                    2
                )];
                $identificador++;
            }

            if ($conta->saldo_credito != $conta->saldo_credito_balancete) {
                $diferenca = $conta->saldo_credito_balancete - $conta->saldo_credito;

                $obj->Balancetes[] = (object)['Balancete' => $this->montaObjeto(
                    $identificador,
                    $conta,
                    $diferenca,
                    $conta->natureza_saldo_anterior,
                    2
                )];
                $identificador++;
            }

            if ($conta->saldo_final != $conta->saldo_final_balancete) {
                $diferenca = $conta->saldo_final_balancete - $conta->saldo_final;

                $obj->Balancetes[] = (object)['Balancete' => $this->montaObjeto(
                    $identificador,
                    $conta,
                    $diferenca,
                    $conta->natureza_saldo_final,
                    3
                )];
                $identificador++;
            }
        }

        $this->aDados = $obj;
    }


    /**
     * Retorna todas as contas PCASP com movimentacao
     * @return array[]
     */
    public function contasPlanoUniao()
    {

        $chamadasFuncaoMatriz = [];
        foreach ($this->getListaInstituicoes() as $instituicao) {
            $chamadasFuncaoMatriz[] = <<<SQL
select *
  from contabilidade.matriz(
        $this->iAnoUsu,
        $instituicao,
        '$this->dtDataInicial',
        '$this->dtDataFinal',
        false,
        '{}'
  )
SQL;
        }
        $sqlChamadasMatriz = implode(' union ', $chamadasFuncaoMatriz);

        $sql = <<<SQL
select *,
       round(planosaldo[1]::float8, 2)::float8 as saldo_anterior_balancete,
       round(planosaldo[2]::float8, 2)::float8 as saldo_debito_balancete,
       round(planosaldo[3]::float8, 2)::float8 as saldo_credito_balancete,
       round(planosaldo[4]::float8, 2)::float8 as saldo_final_balancete,
      fc_sinal_saldo_conta(substring(estrutural_padrao,1 ,1)::int,  saldo_anterior) as natureza_saldo_anterior,
      fc_sinal_saldo_conta(substring(estrutural_padrao,1 ,1)::int,  saldo_final) as natureza_saldo_final
from (
select estrutural_padrao,
       exercicio,
       reduzido,
       poder_ordao,
       siconfi,
       complemento,
       indicador_superavit,
       divida_consolidada,
       nr,
       nd,
       ai,
       funcao,
       subfuncao,
       informacoescomplementares,
       sum(saldo_anterior) as saldo_anterior,
       sum(saldo_debito) as saldo_debito,
       sum(saldo_credito) as saldo_credito,
       sum(saldo_final) as saldo_final,
       fc_planosaldonovo_array({$this->iAnoUsu},
                               reduzido,
                               '{$this->dtDataInicial}',
                               '{$this->dtDataFinal}',
                               false) as planosaldo
  from ( {$sqlChamadasMatriz}
       where  (saldo_anterior != 0 or saldo_debito != 0 or saldo_credito != 0 or saldo_final != 0)
       ) as matriz
 group by 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14
 order by 1
) as x
SQL;
        return DB::select($sql);
    }

    private function montaObjeto($id, $dado, $valor, $natureza, $tipo)
    {
        if ($this->relatorio) {
            return (object)[
                'Identificador' => $id,
                'Reduzido' => $dado->reduzido,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'Exercicio' => $this->iAnoUsu,
                'Competencia' => $this->competencia,
                'ContaContabil' => $dado->estrutural_padrao,
                'CodigoPoderOrgao' => $dado->poder_ordao,
                'CodigoSuperavitFinanceiro' => $dado->indicador_superavit,
                'DividaConsolidada' => $dado->divida_consolidada,
                'FonteDestinacaoRecursos' => $dado->siconfi,
                'NaturezaReceita' => $dado->nr,
                'NaturezaDespesa' => $dado->nd,
                'Funcao' => $dado->funcao,
                'SubFuncao' => $dado->subfuncao,
                'AnoInscricaoRestosPagar' => $dado->ai,
                'CodigoAcompanhamentoExecucaoOrcamentaria' => str_pad($dado->complemento, 4, '0', STR_PAD_LEFT),
                'Valor' => $valor,
                'Tipo' => $tipo,
                'NaturezaSaldo' => $natureza,
                'Deducao' => null,
                'CodigoDetalhamentoSubfuncao' => null
            ];
        } else {
            return (object)[
                'Identificador' => $id,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'Exercicio' => $this->iAnoUsu,
                'Competencia' => $this->competencia,
                'ContaContabil' => $dado->estrutural_padrao,
                'CodigoPoderOrgao' => $dado->poder_ordao,
                'CodigoSuperavitFinanceiro' => $dado->indicador_superavit,
                'DividaConsolidada' => $dado->divida_consolidada,
                'FonteDestinacaoRecursos' => $dado->siconfi,
                'NaturezaReceita' => $dado->nr,
                'NaturezaDespesa' => $dado->nd,
                'Funcao' => $dado->funcao,
                'SubFuncao' => $dado->subfuncao,
                'AnoInscricaoRestosPagar' => $dado->ai,
                'CodigoAcompanhamentoExecucaoOrcamentaria' => str_pad($dado->complemento, 4, '0', STR_PAD_LEFT),
                'Valor' => abs($valor),
                'Tipo' => $tipo,
                'NaturezaSaldo' => $natureza,
                'Deducao' => null,
                'CodigoDetalhamentoSubfuncao' => null
            ];
        }
    }
}
