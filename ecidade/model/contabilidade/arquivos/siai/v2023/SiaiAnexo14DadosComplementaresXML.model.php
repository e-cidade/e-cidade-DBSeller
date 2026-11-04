<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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

require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBase.model.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("std/DBString.php"));

class SiaiAnexo14DadosComplementaresXML extends SiaiArquivoBase
{
    
    function __construct()
    {
        
        $this->oDocumento = new DOMDocument('1.0', 'utf-8');
        $this->oDocumento->formatOutput = true;
        $this->oDocumento->encoding = 'utf-8';
    }
    
    /**
     * Busca os dados para gerar o Arquivo de Empenhos
     */
    public function gerarDados()
    {
        
        $sNomeArquivo =  $this->codigoOrgaoTCEXml . "_" . str_pad($this->getMes(), 2, STR_PAD_LEFT) . $this->getAno() . "_Anexo14DadosComplementares.xml";

        $this->setNomeArquivo($sNomeArquivo);
        
        $oNodeAnexo14DadosComplementares = $this->oDocumento->createElement("DadosComplementares");
        
        $oDadosAnexo14 = new stdClass();
        $oDadosAnexo14->CodigoUnidadeJurisdicionada = $this->codigoOrgaoTCEXml;
        $oDadosAnexo14->AnoReferencia               = $this->getAno();
        $oDadosAnexo14->SistemaGerador              = "SOFTWARE PUBLICO BRASILEIRO";
        $this->addElementXML($oDadosAnexo14, $oNodeAnexo14DadosComplementares);
        
        $oNodeListaPrograma            = $this->oDocumento->createElement("ListaPrograma");
        $oNodeListaInstrumentoPrograma = $this->oDocumento->createElement("ListaInstrumentoPrograma");
        
        
        $aDados = $this->getDados();
        if (count($aDados) == 0) {
            throw new Exception("Nenhum registro encontrado");
        }

        $aProgramas = array();
        
        foreach ($aDados as $oDados) {
            if (!in_array($oDados->o54_programa, $aProgramas)) {
                $oNodePrograma = $this->oDocumento->createElement("Programa");
                
                $oDadosPrograma = new stdClass();
                $oDadosPrograma->CodigoPrograma = $oDados->o54_programa;
                $oDadosPrograma->DescricaoPrograma = DBString::removerAcentuacao(DBString::removerCaracteresEspeciais($oDados->o54_descr));
                $this->addElementXML($oDadosPrograma, $oNodePrograma);
                
                $oNodeListaPrograma->appendChild($oNodePrograma);
                
                $aProgramas[] = $oDados->o54_programa;
            }
            
            $oNodeInstrumentoPrograma = $this->oDocumento->createElement("InstrumentoPrograma");
            
            $oDadosInstrumentoPrograma = new stdClass();
            $oDadosInstrumentoPrograma->CodigoPrograma               = $oDados->o54_programa;
            $oDadosInstrumentoPrograma->CodigoInstrumentoPrograma    = $oDados->o55_projativ;
            $oDadosInstrumentoPrograma->DescricaoInstrumentoPrograma = DBString::removerAcentuacao(DBString::removerCaracteresEspeciais($oDados->o55_descr));
            $oDadosInstrumentoPrograma->TipoInstrumentoPrograma      = $oDados->o55_tipo;
            $this->addElementXML($oDadosInstrumentoPrograma, $oNodeInstrumentoPrograma);
            
            $oNodeListaInstrumentoPrograma->appendChild($oNodeInstrumentoPrograma);
        }
        
        /*
         * Montamos a estrutura do arquivo
         */
        $oNodeAnexo14DadosComplementares->appendChild($oNodeListaPrograma);
        $oNodeAnexo14DadosComplementares->appendChild($oNodeListaInstrumentoPrograma);
        
        $this->oDocumento->appendChild($oNodeAnexo14DadosComplementares);
        
        $this->sArquivo = $this->oDocumento->saveXML();
        file_put_contents("tmp/{$this->getNomeArquivo()}", $this->getArquivo());
    }
    
    
    public function getDados()
    {

        $sOrcDotacaoWhere = "and ((o58_orgao = {$this->codigoOrgao}
                                  and o58_unidade = {$this->codigoUnidade})
                              or ({$this->codigoOrgao} = 0
                                  and o58_instit = {$this->codigoUnidade}))";
      
        if ($this->codigoOrgao == "29" && $this->codigoUnidade == "1") {
            $sOrcDotacaoWhere = "and ((o58_orgao = 29 and o58_unidade = 1)
                             or (o58_orgao = 29 and o58_unidade = 46)
                             or (o58_orgao = 29 and o58_unidade = 47))";
        }
      
        if ($this->codigoOrgao == "20" && $this->codigoUnidade == "1") {
            $sOrcDotacaoWhere = "and ((o58_orgao = 20 and o58_unidade = 1)
                             or (o58_orgao = 20 and o58_unidade = 49))";
        }
      
        if ($this->codigoOrgao == "34" && $this->codigoUnidade == "1") {
            $sOrcDotacaoWhere = "and ((o58_orgao = 34 and o58_unidade = 1)
                             or (o58_orgao = 34 and o58_unidade = 49))";
        }
      
        if ($this->codigoOrgao == "18" && $this->codigoUnidade == "1") {
            $sOrcDotacaoWhere = "and ((o58_orgao = 18 and o58_unidade = 1)
                             or (o58_orgao = 18 and o58_unidade = 45)
                             or (o58_orgao = 18 and o58_unidade = 46)
                             or (o58_orgao = 18 and o58_unidade = 47)
                             or (o58_orgao = 18 and o58_unidade = 48)
                             or (o58_orgao = 18 and o58_unidade = 49))";
        }
        
        $sSql = "select distinct 
                        o54_programa,
                        o54_descr, 
                        o55_projativ,
                        o55_descr, 
                        o55_tipo 
                   from orcdotacao 
                        inner join orcprograma  on orcprograma.o54_anousu   = orcdotacao.o58_anousu 
                                               and orcprograma.o54_programa = orcdotacao.o58_programa 
                        inner join orcprojativ  on orcprojativ.o55_anousu   = orcdotacao.o58_anousu 
                                               and orcprojativ.o55_projativ = orcdotacao.o58_projativ 
                  where o58_anousu  = ".$this->getAno()."
                    $sOrcDotacaoWhere 
                  order by o54_programa,o55_projativ";
        $rsDados = db_query($sSql);
        return db_utils::getCollectionByRecord($rsDados);
    }
}
