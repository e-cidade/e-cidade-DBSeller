<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use stdClass;
use db_utils;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Exception;

class ArquivoPPAProgramaIndicador extends ArquivoBase
{
    protected $iCodigoLayout = 113;
    protected $sNomeArquivo  = 'PPAProgramaIndicador';

    /**
    * Busca os dados para gerar o Arquivo de Unidade Orcamentaria
    */
    public function gerarDados()
    {
        $oDaoPpaintegracao    = new \cl_ppaintegracao;
        /**
         * Retorna qual versao do ppa que foi realizada a integracao com o orcamento
         */
        $sCampos = " ppaintegracao.*,o01_anoinicio,o01_anofinal";
        $sWhereBuscaVersaoPpa  = "     ppaintegracao.o123_ano            = {$this->iAnoUsu}         ";
        $sWhereBuscaVersaoPpa .= " AND ppaintegracao.o123_situacao       = 1                     ";
        $sWhereBuscaVersaoPpa .= " AND ppaintegracao.o123_tipointegracao = 1                     ";
        $sWhereBuscaVersaoPpa .= " AND ppaintegracao.o123_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $sSqlBuscaVersaoPpa    = $oDaoPpaintegracao->sql_query(null, $sCampos, null, $sWhereBuscaVersaoPpa);
        $rsSqlBuscaVersaoPpa   = db_query($sSqlBuscaVersaoPpa);
    
        if (pg_num_rows($rsSqlBuscaVersaoPpa) == 1) {
            $oVersao     = db_utils::fieldsMemory($rsSqlBuscaVersaoPpa, 0);
            $versaoPPA = $oVersao->o123_ppaversao;
            
            $daoPpaDotacao = new \cl_ppadotacao;
            $where = "o08_ano = {$this->iAnoUsu}";
            $where .= " and o08_instit in (".implode(",", $this->getListaInstituicoes()).") ";
            $where .= " and o08_ppaversao =  {$versaoPPA}";
            $campos = "distinct o08_programa as codigo,o54_descr";
            $sqlPPADotacao = $daoPpaDotacao->sql_query_despesa_programa(null, $campos, null, $where);

            $rsPPADotacao = db_query($sqlPPADotacao);
            $aProgramas = db_utils::getCollectionByRecord($rsPPADotacao);
            
            $RemessaPPAProgramaIndicador = new stdClass();
            $programaIndicador = [];
          
            foreach ($aProgramas as $oPrograma) {
                $where  = " o18_orcprograma = {$oPrograma->codigo} ";
                $where .= " and o18_anousu  = {$this->iAnoUsu} ";
                $campos = "o10_indica,o10_descr";
                $oDaoIndicadoresPrograma = new \cl_orcindicaprograma;
                $sSqlIndicadoresPrograma = $oDaoIndicadoresPrograma->sql_query(null, $campos, null, $where);
                $rsIndicadoresPrograma = db_query($sSqlIndicadoresPrograma);
                
                if (pg_num_rows($rsIndicadoresPrograma) == 0) {
                    $msgLog = "Não há vínculo do indicador ao programa {$oPrograma->codigo}";
                    $msgLog .= " para o ano de {$this->iAnoUsu}";
                    $this->addLog($msgLog);
                    continue;
                }

                for ($iIndicador = 0; $iIndicador < pg_num_rows($rsIndicadoresPrograma); $iIndicador++) {
                    $oDadosIndicador  = db_utils::fieldsMemory($rsIndicadoresPrograma, $iIndicador);
                    
                    $oDaoEsperadoIndicadores = new \cl_orcindicaindiceesperado;
                    
                    $where = "o25_orcindica = $oDadosIndicador->o10_indica";
                    $where .= " and o25_anousu between $oVersao->o01_anoinicio and $oVersao->o01_anofinal";
                    $campos = "o25_sequencial,o25_valor,o25_anousu";
                    $orderBy = "o25_anousu";
                    $sqlEsperadoIndicadores = $oDaoEsperadoIndicadores->sql_query_file(null, $campos, $orderBy, $where);
                      
                    $rsEsperadoIndicadores = db_query($sqlEsperadoIndicadores);
                    
                    $numRows = pg_num_rows($rsEsperadoIndicadores);
                    
                    for ($j=0; $j<$numRows; $j++) {
                        $dadosEsperadoIndicador =  db_utils::fieldsMemory($rsEsperadoIndicadores, $j);
            
                        $identificador = date("Y", db_getsession("DB_datausu"));
                        $identificador .= date("m", db_getsession("DB_datausu"));
                        $identificador .= $oPrograma->codigo;
                        $identificador .= $dadosEsperadoIndicador->o25_sequencial;
                      
                        $valorFinal =  db_utils::fieldsMemory($rsEsperadoIndicadores, $numRows-1)->o25_valor;
            
                        $oDadosProgramaIndicador = new stdClass();
                        $oDadosProgramaIndicador->Identificador = $identificador;
                        $nomeIndicador = Helper::convertAndLimit("<![CDATA[".$oDadosIndicador->o10_descr, 209);
                        $oDadosProgramaIndicador->Nome = $nomeIndicador;
                        $oDadosProgramaIndicador->ValorModificado =  $dadosEsperadoIndicador->o25_valor;
                        $oDadosProgramaIndicador->ValorFinal = $valorFinal;
                        $oDadosProgramaIndicador->CodigoPPAPrograma = $oPrograma->codigo;
                        $oDadosProgramaIndicador->Ano = $dadosEsperadoIndicador->o25_anousu;
                        $oDadosProgramaIndicador->CodigoUnidadeGestora = $this->sCodigoTribunal;
                        
                        $programaIndicador[] = (object) ['PPAProgramaIndicador' => $oDadosProgramaIndicador];
                    }
                }
            }
        }
        
        $RemessaPPAProgramaIndicador->Indicadores = $programaIndicador;
        $this->aDados = $RemessaPPAProgramaIndicador;
    }
}
