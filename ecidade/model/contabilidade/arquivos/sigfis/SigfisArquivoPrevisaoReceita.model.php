<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBSeller Servicos de Informatica             
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


require_once  modification("interfaces/iPadArquivoTxtBase.interface.php");
require_once  modification("model/contabilidade/arquivos/sigfis/SigfisArquivoBase.model.php");
require_once  modification("libs/db_liborcamento.php");

/**
 *
 * Classe Responsável pela geração dos dados necessários para o arquivo Previsao da Receita
 * @author Andrio Costa
 * @package contabilidade
 * @subpackage sigfis
 *
 */
class SigfisArquivoPrevisaoReceita extends SigfisArquivoBase implements iPadArquivoTXTBase {

    protected $iCodigoLayout     = 119;
    protected $sNomeArquivo      = 'PrevRec';

    /**
     * Busca os dados para gerar o Arquivo do Previsao da Receita
     */
    public function gerarDados() {

        /**
         * Busca os dados da db_config
         */
        $oDbConfig       = new db_stdClass();
        $oDadoConfig     = $oDbConfig->getDadosInstit();
        $sWhere          = "o70_instit = ".db_getsession("DB_instit");
        $rsReceitaSaldo = db_receitasaldo(11, 1, 2, true, $sWhere,
            $this->iAnoUso,
            $this->dtDataInicial, $this->dtDataFinal);


        $aReceitas = db_utils::getColectionByRecord($rsReceitaSaldo);
        $aReceitaSoma = array();

        if (empty($this->sCodigoTribunal)) {
            throw new Exception("O código do tribunal deve ser informado para geração do arquivo");
        }
        /**
         * Percorre o array retornado pelo metodo db_receitasaldo
         */
        foreach ($aReceitas as $iInd => $aReceita) {

            /**
             *
             * Como o metodo db_receitasaldo não retorna o campo orcfontes.o57_codfon precisamos fazer uma consulta
             * para descobri-lo
             */
            $oDaoOrcFontes = db_utils::getDao('orcfontes');
            $sWhereFontes  = "o57_anousu = {$this->iAnoUso} and o57_fonte = '$aReceita->o57_fonte'";
            $sSqlOrcFontes = $oDaoOrcFontes->sql_query_file(null, null, "*, ( select count(*) from orcreceita where o57_codfon = o70_codfon and o57_anousu = o70_anousu ) as quant_rec ", null, $sWhereFontes);
            $rsOrcFontes   = $oDaoOrcFontes->sql_record($sSqlOrcFontes);

            if ($oDaoOrcFontes->numrows == 1) {

                $sCodFon       = db_utils::fieldsmemory($rsOrcFontes, 0)->o57_codfon;
                $sEstrut       = db_utils::fieldsmemory($rsOrcFontes, 0)->o57_fonte;
                $iQuantReceita = db_utils::fieldsmemory($rsOrcFontes, 0)->quant_rec;

                if ( $iQuantReceita == 0 ) {
                    continue;
                }

                /**
                 * Para cada o57_codfon retornado verificamos se este possui vinculo com Recita Sigfis.
                 */
                $oVinculo = SigfisVinculoReceita::getVinculoReceita($sCodFon);
                if (empty($oVinculo)) {

                    $sErroLog  = "Receita {$aReceita->o57_fonte} do ano de {$this->iAnoUso} ";
                    $sErroLog .= "não tem vinculo com Receita Sigfis.\n";
                    $this->addLog($sErroLog);
                    continue;
                }

                //$sEstrut = $oVinculo->receitatce;
                if (substr($sEstrut, 0,  1) == '9' ) {
                    $sEstrut = '9' . substr($sEstrut, 2, 12);
                } else {
                    $sEstrut = substr($sEstrut, 0, 13);
                }

                if (!isset($aReceitaSoma[$sEstrut])) {
                    $aReceitaSoma[$sEstrut] = $aReceita->saldo_inicial ;
                } else {
                    $aReceitaSoma[$sEstrut] += $aReceita->saldo_inicial ;
                }

            } else {
                $sErroLog  = "Receita {$aReceita->o57_fonte} do ano de {$this->iAnoUso} retornou mais de um registro.($sSqlOrcFontes)\n";
                $this->addLog($sErroLog);
            }
        }

        if (count($aReceitaSoma) > 0) {

            foreach ($aReceitaSoma as $sFonte => $nValor) {

                $oDados      = new stdClass();

                $oDados->dt_Ano             = $this->iAnoUso;
                $oDados->Cd_Unidade         = str_pad($this->sCodigoTribunal, 4, ' ', STR_PAD_LEFT);
                $oDados->Cd_ItemReceita     = str_pad(substr($sFonte, 0, 13), 13, ' ', STR_PAD_RIGHT);
                $oDados->vl_Receita         = str_pad(number_format(abs($nValor), 2, '',''), 16, ' ', STR_PAD_LEFT);
                $oDados->codigolinha        = 406;
                $this->aDados[] = $oDados;

            }
        }
    }
}
