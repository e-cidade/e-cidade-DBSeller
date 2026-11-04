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

/**
 *
 * Classe Responsável pela geração dos dados necessários para o arquivo Itens da Receita
 * @author Andrio Costa
 * @package contabilidade
 * @subpackage sigfis
 *
 */
class SigfisArquivoItemReceita extends SigfisArquivoBase implements iPadArquivoTXTBase {

    protected $iCodigoLayout     = 116;
    protected $sNomeArquivo      = 'EspRec';

    /**
     * Busca os dados para gerar o Arquivo do Programa do Orçamento
     */
    public function gerarDados() {

        /**
         * Busca os dados da db_config
         */
        $sCampos       = "distinct orcfontes.o57_fonte, orcfontes.o57_descr, orcfontes.o57_anousu, orcfontes.o57_codfon, ";
        $sCampos      .= "case when o70_codrec is not null then '1' else '2' end as reduz ";
        $sOrder        = "orcfontes.o57_fonte";
        $clOrcFontes   = new cl_orcfontes();
        $sSqlOrcFontes = $clOrcFontes->sql_query_previsao(null, $this->iAnoUso, $sCampos, $sOrder, " o70_anousu = " . db_getsession("DB_anousu") . " and o70_instit = " . db_getsession("DB_instit"));

        $sSqlOrcFontes = "
          select substr(o57_fonte,1,14) as o57_fonte, 
                 max(o57_descr) as o57_descr, 
                 o57_anousu, 
                 max(o57_codfon) as o57_codfon, 
                 min(reduz) as reduz
            from (".$sSqlOrcFontes.") as x 
           group by substr(o57_fonte,1,14), o57_anousu order by o57_fonte ";

        $clausulaWhen = SigfisArquivoItemReceita::getCaseWhen();
        $sSqlOrcFontes = "
          select {$clausulaWhen} as o57_fonte, 
                  o57_descr, 
                  o57_anousu, 
                  o57_codfon, 
                  reduz 
             from ( {$sSqlOrcFontes} ) as x 
            group by {$clausulaWhen}, 
                     o57_descr, 
                     o57_anousu, 
                     o57_codfon, 
                     reduz 
               order by {$clausulaWhen}";


//        die($sSqlOrcFontes);
        $rsOrcFontes   = $clOrcFontes->sql_record($sSqlOrcFontes);

        $this->addLog("=====Arquivo: ".$this->getNomeArquivo()." Erros:\n");
        if ($clOrcFontes->numrows > 0) {

            if (empty($this->sCodigoTribunal)) {
                throw new Exception("O código do tribunal deve ser informado para geração do arquivo");
            }

            for($i = 0; $i < $clOrcFontes->numrows; $i++) {

                $oDadosQuery = db_utils::fieldsMemory($rsOrcFontes, $i);
                $oDados      = new stdClass();

                if (substr($oDadosQuery->o57_fonte, 1,  13) == '00000000') {
                    continue;
                }

                $oVinculo = SigfisVinculoReceita::getVinculoReceita($oDadosQuery->o57_codfon);

                if (empty($oVinculo)) {

                    $sErroLog  = "Receita {$oDadosQuery->o57_fonte} do ano de {$this->iAnoUso} ";
                    $sErroLog .= "não tem vinculo com Recita Sigfis.\n";
                    $this->addLog($sErroLog);
                    continue;
                }

                $oDados->cd_Unidade           = str_pad($this->sCodigoTribunal,                  4, ' ', STR_PAD_LEFT);
                $oDados->cd_ItemReceitaGestor = str_pad($oDadosQuery->o57_fonte, 13, " ", STR_PAD_RIGHT);
                $oDados->de_ItemReceita       = str_pad(substr($oDadosQuery->o57_descr, 0, 50), 50, ' ', STR_PAD_RIGHT);
                $oDados->cd_ItemReceita       = str_pad(substr($oVinculo->receitatce,0,13), 13, ' ', STR_PAD_RIGHT);
                $oDados->dt_ano               = $oDadosQuery->o57_anousu;
                $oDados->Cd_receblanc         = $oDadosQuery->reduz;
                $oDados->codigolinha          = 403;
                $this->aDados[] = $oDados;
            }
        }
        $this->addLog("===== Fim do Arquivo: ".$this->getNomeArquivo()."\n");
    }


    /**
     * Criei isso como segurança pois foi mexido nos parâmetros do substr. Visto que é
     * usado o mesmo diversas vezes, corrigindo aqui corrige todos os lugares.
     *
     * Desculpe! :-(
     * @return string
     */
    public static function getCaseWhen() {

        $retorno = "
             case when substr(o57_fonte,1,1) = '9' 
                  then substr(o57_fonte,1,1) || substr(o57_fonte,3,14) 
                  else substr(o57_fonte,2,13) 
              end
        ";
        return $retorno;
    }
}
