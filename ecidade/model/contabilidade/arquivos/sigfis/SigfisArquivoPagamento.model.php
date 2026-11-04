<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once modification ("interfaces/iPadArquivoTxtBase.interface.php");
require_once modification ("model/contabilidade/arquivos/sigfis/SigfisArquivoBase.model.php");
//
/**
 *
 * Classe Responsável pela geração dos dados necessários para o arquivo Pagamento
 * @author Andrio Costa
 * @package contabilidade
 * @subpackage sigfis
 *
 */
class SigfisArquivoPagamento extends SigfisArquivoBase implements iPadArquivoTXTBase {

    protected $iCodigoLayout     = 206;
    protected $sNomeArquivo      = 'PagEmp';
    protected $aMovimentoContabil = array();


        /**
         * Busca os dados para gerar o Arquivo do Pagamento
         */
        public function gerarDados() {

        /**
         * Busca os dados da db_config
         */

        $iInstituicaoSessao = db_getsession('DB_instit');



        $RetencaoReceitas = new \cl_conlancamemp;

        $this->setCodigoLayout(206);
        $iAnoSessao = db_getsession("DB_anousu");
        if( $iAnoSessao < 2013 ){
            $this->setCodigoLayout(129);
        }



        $this->addLog("=====Arquivo".$this->getNomeArquivo()." Erros:\n");
        $sSqlvalorPagoRetencoes    = $RetencaoReceitas->getSsqlBuscaPagamentos($this->iAnoUso,$this->dtDataInicial,$this->dtDataFinal, $iInstituicaoSessao);

        $rsvalorPagoRetencoes = $RetencaoReceitas->sql_record($sSqlvalorPagoRetencoes);

        if ($RetencaoReceitas->numrows > 0 ) {

            if (empty($this->sCodigoTribunal)) {
                throw new Exception("O código do tribunal deve ser informado para geração do arquivo");
            }


            for ($i = 0; $i < $RetencaoReceitas->numrows; $i++) {



                $oDadosvalorPagoRetencoes = db_utils::fieldsMemory($rsvalorPagoRetencoes, $i);

                $valorPagoRetencoes = $oDadosvalorPagoRetencoes->valor_pago;


                /**
                * Busca o valor pago de Retenções
                */


                $oDados                = new stdClass();
                $sUnidadeOrcamentaria = str_pad($oDadosvalorPagoRetencoes->o58_unidade,4, ' ', STR_PAD_LEFT);
                $dtPagamento           = $this->formataData($oDadosvalorPagoRetencoes->c70_data);

                $oDados->cd_Unidade             = str_pad($this->sCodigoTribunal,    4, ' ', STR_PAD_LEFT);
                $oDados->cd_UnidadeOrcamentaria = str_pad($sUnidadeOrcamentaria,     4, ' ', STR_PAD_LEFT);
                $oDados->nu_Empenho             = str_pad($oDadosvalorPagoRetencoes->e60_codemp, 10, ' ', STR_PAD_RIGHT);
                $oDados->dt_PagamentoEmpenho    = $dtPagamento;
                $oDados->dt_Ano                 = $oDadosvalorPagoRetencoes->e60_anousu;
                $oDados->vl_Pagamento           = str_pad($valorPagoRetencoes * 100 , 16, ' ', STR_PAD_LEFT);


                $aContas = array_unique((explode(',',$oDadosvalorPagoRetencoes->c60_estrut)));

                $aContasNovas = array();
                $aContasCodconNovas = array();
                foreach ($aContas as $a) {
                    if (!empty($a)) {
                        $contaS = explode('-',$a);
                        $estrut = $contaS[0];
                        $codcon = $contaS[1];
                        $aContasNovas[] = $estrut;

                        $sContaCorrente = "   select ( CASE WHEN $iAnoSessao < 2016 THEN c56_sequencial ELSE c56_contabancaria END)
                         as conta_bancaria
                         from contabilidade.conplano a
                         join conplanoreduz on  (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
                         left join conplanocontabancaria b on a.c60_codcon = b.c56_codcon
                        and a.c60_anousu = b.c56_anousu
                        and b.c56_reduz = c61_reduz
                        left join configuracoes.contabancaria c on c.db83_sequencial = b.c56_contabancaria
                        where a.c60_anousu = $iAnoSessao and a.c60_codcon = $codcon and c61_instit = " . $iInstituicaoSessao;

                        $rsContaCorrente = db_query($sContaCorrente);
                        $oDadosContaCorrente = db_utils::fieldsMemory($rsContaCorrente, 0);

                        $aContasCodconNovas[] = $oDadosContaCorrente->conta_bancaria;

                    }
                }
                $aContas = $aContasNovas;
                $aContasCodcon = $aContasCodconNovas;


                $oDados->cd_ContaContabil1      = str_pad($aContas[0], 34, ' ', STR_PAD_RIGHT);
                $oDados->cd_ContaContabil2      = str_pad($aContas[1], 34, ' ', STR_PAD_RIGHT); // str_repeat(' ', 34); // N?o usado no e-cidada
                $oDados->cd_ContaContabil3      = str_pad($aContas[2], 34, ' ', STR_PAD_RIGHT); // str_repeat(' ', 34); // N?o usado no e-cidada



                $oDados->dt_AnoMes              = $oDadosvalorPagoRetencoes->competencia;
                $oDados->cd_Orgao               = str_pad($oDadosvalorPagoRetencoes->o58_orgao,   4, ' ', STR_PAD_LEFT);
                $oDados->nu_EmpenhoSup          = str_pad(str_repeat(' ', 10), 10, ' ', STR_PAD_LEFT);
                $oDados->Reservado_tce          = str_repeat(' ', 41);

                if( $iAnoSessao < 2013 ){
                    $oDados->codigolinha            = 416;
                }else{
                    $oDados->Reservado_tce1         = str_repeat(' ', 10);
                    $oDados->Cd_ContaCorrente1      = str_pad($aContasCodcon[0],  30, ' ', STR_PAD_RIGHT);
                    $oDados->Cd_ContaCorrente2      = str_pad($aContasCodcon[1],  30, ' ', STR_PAD_RIGHT);
                    $oDados->Cd_ContaCorrente3      = str_pad($aContasCodcon[2],  30, ' ', STR_PAD_RIGHT);
                    $oDados->codigolinha            = 671;
                }

                $this->aDados[] = $oDados;

            }

        }

        $this->addLog("===== Fim do Arquivo: ".$this->getNomeArquivo()."\n");
    }
}
