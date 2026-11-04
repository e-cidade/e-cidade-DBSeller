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

/**
 *
 * Classe Basica para geracao de arquivo SIAI
 *
 */
abstract class SiaiArquivoBase
{
  
    protected $iAno;
    protected $iMes;
    protected $dtDataInicial;
    protected $dtDataFinal;
    protected $dtDataGeracao;
    protected $dtHoraGeracao;
    protected $sNomeArquivo;
    protected $sBimReferencia;
    protected $iCodigoLayout;
    protected $aDados = array();
    protected $codigoOrgaoTCE;
    protected $codigoOrgaoTCEXml;
    protected $codigoOrgao;
    protected $codigoUnidade;
    protected $nomeUnidade;
    protected $oDocumento;
    protected $sArquivo;
    protected $rsLogger;
  
    function __construct()
    {
    }
  
  /**
   *
   * Retorna um array de com os dados do Arquivo
   * @return array
   */
    public function getDados()
    {
    
        return $this->aDados;
    }
  
  /**
   *
   * Seta a data Inicial
   * @param String $sDataInicial
   */
    public function setDataInicial($sDataInicial)
    {
    
        $this->dtDataInicial = $sDataInicial;
    }
  
  /**
   *
   * Seta a data Final
   * @param String $sDataFinal
   */
    public function setDataFinal($sDataFinal)
    {
    
        $this->dtDataFinal = $sDataFinal;
    }
  
    public function setDataGeracao($sDataGeracao)
    {

        $this->dtDataGeracao = $sDataGeracao;
    }
  
    public function getDataGeracao()
    {
        return $this->dtDataGeracao;
    }

    public function setHoraGeracao($sHoraGeracao)
    {

        $this->dtHoraGeracao = $sHoraGeracao;
    }

    public function setBimReferencia($sBimRef)
    {

        $this->sBimReferencia = $sBimRef;
    }

    public function getBimReferencia()
    {
      
        return $this->sBimReferencia;
    }

  /**
  * Seta o Nome do Arquivo
  */
    public function setNomeArquivo($nomeArquivo)
    {

        $this->sNomeArquivo = $nomeArquivo;
    }

  /**
  * Retorna o Nome do Arquivo
  */
    public function getNomeArquivo()
    {

        return $this->sNomeArquivo;
    }

  /**
  * Seta o codigo Orgao TCE para geração em txt
  */
    public function setCodigoOrgaoTCE($codigoOrgaoTCE)
    {

        $this->codigoOrgaoTCE = $codigoOrgaoTCE;
    }

  /**
  * Retorna o codigo Orgao TCE para geração em txt
  */
    public function getCodigoOrgaoTCE()
    {

        return $this->codigoOrgaoTCE;
    }
  
  /**
   * Seta o codigo Orgao TCE para geração em xml
   */
    public function setCodigoOrgaoTCEXml($codigoOrgaoTCE)
    {
      
        $this->codigoOrgaoTCEXml = $codigoOrgaoTCE;
    }
  
  /**
   * Retorna o codigo Orgao TCE para geração em xml
   */
    public function getCodigoOrgaoTCEXml()
    {
      
        return $this->codigoOrgaoTCEXml;
    }

  /**
  * Seta o codigo do Orgao
  */
    public function setCodigoOrgao($codigoOrgao)
    {

        $this->codigoOrgao = $codigoOrgao;
    }

  /**
  * Retorna o codigo do orgao
  */
    public function getCodigoOrgao()
    {

        return $this->codigoOrgao;
    }

  /**
  * Seta o Nome da unidade
  */
    public function setCodigoUnidade($codigoUnidade)
    {

        $this->codigoUnidade = $codigoUnidade;
    }

  /**
  * Retorna o codigo da unidade
  */
    public function getCodigoUnidade()
    {

        return $this->codigoUnidade;
    }

  /**
  * Seta o Nome do Unidade
  */
    public function setNomeUnidade($nomeUnidade)
    {

        $this->nomeUnidade = $nomeUnidade;
    }

  /**
  * Retorna o Nome do Unidade
  */
    public function getNomeUnidade()
    {

        return $this->nomeUnidade;
    }
  
  /**
   * Seta o Nome do Arquivo
   */
    public function setArquivo($sArquivo)
    {
        $this->sArquivo = $sArquivo;
    }
    
    public function getArquivo()
    {
        return $this->sArquivo;
    }

  /**
   * Retorna o CÃ³digo do Layout
   */
    public function setCodigoLayout($iCodigoLayout)
    {
    
        $this->iCodigoLayout = $iCodigoLayout;
    }
  
  /**
   * Retorna o CÃ³digo do Layout
   */
    public function getCodigoLayout()
    {
   
        return $this->iCodigoLayout;
    }
  
    public function setTXTLogger($fp)
    {
    
        $this->rsLogger  = $fp;
    }
  
    public function setAno($iAno)
    {
        $this->iAno = $iAno;
    }
  
    public function getAno()
    {
        return $this->iAno;
    }
  
    public function setMes($iMes)
    {
        $this->iMes = $iMes;
    }
  
    public function getMes()
    {
        return $this->iMes;
    }

  /**
   * Adiciona um registro de log no arquivo
   * @param string $sLog
   */
    protected function addLog($sLog)
    {
    
        fputs($this->rsLogger, $sLog);
    }
  /**
   *
   * Formata valor para o formato esperado no SIAI
   * @param numeric $nValor
   * @return string
   */
    protected function formataValor($nValor = 0, $iTamanho = 0, $sCompleta = "0")
    {
    
        if (empty($nValor)) {
            $nValor = 0;
        }
    
        $nValor = number_format($nValor, 2, "", "");
        if ($iTamanho > 0) {
            if ($nValor < 0) {
                $nValor = "-".str_pad(abs($nValor), $iTamanho-1, $sCompleta, STR_PAD_LEFT);
            } else {
                $nValor = str_pad($nValor, $iTamanho, "{$sCompleta}", STR_PAD_LEFT);
            }
        }
        return $nValor;
    }
  
  /**
   *
   * Formata uma data para o padrao do SIAI
   * @param date $dtData
   * @param String $sParam1
   * @param String $sParam2
   */
    protected function formataData($dtData, $sParam1 = '/', $sParam2 = '-')
    {
  
        $sDataFormatada = implode($sParam1, array_reverse(explode($sParam2, $dtData)));
        return $sDataFormatada;
    }
  
    protected function addElementXML($oAtributos, $oElementoPai = null)
    {
      
        foreach ($oAtributos as $sAtributo => $sValor) {
            if (!empty($oElementoPai)) {
                $oElementoPai->appendChild($this->oDocumento->createElement($sAtributo, $sValor));
            } else {
                $this->oDocumento->createElement($sAtributo, $sValor);
            }
        }
      
        return true;
    }
    
    protected function escreveArquivo() {
        
        $handle = fopen("tmp/".$this->getNomeArquivo(), 'w+');
        
        foreach ($this->aDados as $detalhe) {
            fwrite($handle, implode((array)$detalhe, "")."\n");
        }
        
    }
  
  /**
   * @param DOMDocument $oDocumento
   * @param string $sCaminhoSchema
   * @throws Exception
   */
    protected function validarXML(DOMDocument $oDocumento, $sCaminhoSchema)
    {

        $oRetorno = new stdClass();
        $oRetorno->lErro = false;
        $oRetorno->sMsg = "";
    
        libxml_use_internal_errors(true);
        $lDocumentoValido = $oDocumento->schemaValidate($sCaminhoSchema);

        $aErros = libxml_get_errors();
        $iTotalErros = count($aErros);
        $aMensagem = array();

        if ($lDocumentoValido || $iTotalErros == 0) {
            return $oRetorno;
        }

        foreach ($aErros as $iIndice => $oErro) {
            $aMensagem[] = $iIndice+1 . ' - ' . $oErro->message;
        }

        libxml_clear_errors();
    
        $oRetorno->lErro = true;
        $oRetorno->sMsg = "XML gerado possui $iTotalErros erro(s).\n" . implode("", $aMensagem);
        return $oRetorno;
    }
}
