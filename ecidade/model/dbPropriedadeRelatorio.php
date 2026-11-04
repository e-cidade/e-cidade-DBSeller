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

require_once modification("interfaces/GeradorRelatorio.interface.php");

/**
 * @todo refazer essa classe em um estrutura mais moderna quando o Agata for descontinuado
 */
final class dbPropriedadeRelatorio implements iGeradorRelatorio
{
    private $iVersao = "";
    private $sNome = "";
    private $sLayout = "";
    private $sFormato = "";
    private $sOrientacao = "";
    private $iMargemSup = "";
    private $iMargemInf = "";
    private $iMargemDir = "";
    private $iMargemEsq = "";
    private $sTipoSaida = "";
    private $bDelimitarTexto = true;
    private $bImprimirCabecalho = true;

    /**
     * Método construtor da classe
     *
     * @param string $sNome
     * @param string $iVersao
     * @param string $sLayout
     * @param string $sFormato
     * @param string $sOrientacao
     * @param string $iMargemSup
     * @param string $iMargemInf
     * @param string $iMargemDir
     * @param string $iMargemEsq
     * @param string $sTipoSaida
     * @param bool $bDelimitarTexto
     * @param bool $bImprimirCabecalho
     */
    public function __construct(
        $sNome = "",
        $iVersao = "",
        $sLayout = "",
        $sFormato = "",
        $sOrientacao = "",
        $iMargemSup = "",
        $iMargemInf = "",
        $iMargemDir = "",
        $iMargemEsq = "",
        $sTipoSaida = "",
        $bDelimitarTexto = true,
        $bImprimirCabecalho = true
    ) {
        $this->setNome($sNome);
        $this->setLayout($sLayout);
        $this->setVersao($iVersao);
        $this->setFormato($sFormato);
        $this->setOrientacao($sOrientacao);
        $this->setMargemSup($iMargemSup);
        $this->setMargemInf($iMargemInf);
        $this->setMargemEsq($iMargemEsq);
        $this->setMargemDir($iMargemDir);
        $this->setTipoSaida($sTipoSaida);
        $this->setDelimitarTexto($bDelimitarTexto);
        $this->setImprimirCabecalho($bImprimirCabecalho);
    }


    /** Retorna versão do XMLDBSeller
     * @return string
     */
    public function getVersao()
    {
        return $this->iVersao;
    }


    /** Retorna formato da página
     * @return string
     */
    public function getFormato()
    {
        return $this->sFormato;
    }


    /** Retorna layout padrão do relatório
     * @return string
     */
    public function getLayout()
    {
        return $this->sLayout;
    }


    /** Retorna o nome do relatório
     * @return string
     */
    public function getNome()
    {
        if (db_utils::isUTF8($this->sNome)) {
            return mb_convert_encoding($this->sNome, 'ISO-8859-1', 'UTF-8');
        } else {
            return $this->sNome;
        }
    }


    /** Retorna orientação da página ( portrait ou landscape )
     * @return string
     */
    public function getOrientacao()
    {
        return $this->sOrientacao;
    }


    /** Seta versão do relatório
     * @param string $iVersao
     */
    public function setVersao($iVersao)
    {
        $this->iVersao = $iVersao;
    }


    /** Seta o formato da Página
     * @param string $sFormato
     */
    public function setFormato($sFormato)
    {
        $this->sFormato = $sFormato;
    }


    /** Seta  o layout padrão do relatório
     * @param string $sLayout
     */
    public function setLayout($sLayout)
    {
        $this->sLayout = $sLayout;
    }


    /** Seta o nome do relatório
     * @param string $sNome
     */
    public function setNome($sNome)
    {
        $this->sNome = $sNome;
    }


    /** Seta a orientação da página ( portrait ou landscape )
     * @param string $sOrientacao
     */
    public function setOrientacao($sOrientacao)
    {
        $this->sOrientacao = $sOrientacao;
    }


    /** Retorna margem direita do relatório
     * @return string
     */
    public function getMargemDir()
    {
        return $this->iMargemDir;
    }


    /** Retorna margem esquerda do relatório
     * @return string
     */
    public function getMargemEsq()
    {
        return $this->iMargemEsq;
    }


    /** Retorna margem inferior do relatório
     * @return string
     */
    public function getMargemInf()
    {
        return $this->iMargemInf;
    }


    /** Retorna margem superior do relatório
     * @return string
     */
    public function getMargemSup()
    {
        return $this->iMargemSup;
    }


    /** Seta margem direita do relatório
     * @param string $iMargemDir
     */
    public function setMargemDir($iMargemDir)
    {
        $this->iMargemDir = $iMargemDir;
    }


    /** Seta margem esquerda do relatório
     * @param string $iMargemEsq
     */
    public function setMargemEsq($iMargemEsq)
    {
        $this->iMargemEsq = $iMargemEsq;
    }


    /** Seta margem inferior do relatório
     * @param string $iMargemInf
     */
    public function setMargemInf($iMargemInf)
    {
        $this->iMargemInf = $iMargemInf;
    }


    /** Seta margem superior do relatório
     * @param string $iMargemSup
     */
    public function setMargemSup($iMargemSup)
    {
        $this->iMargemSup = $iMargemSup;
    }

    /**
     * Define o tipo de saída do relatório
     * Ex.: PDF, CSV, TXT
     * @param string $sTipoSaida
     */
    public function setTipoSaida($sTipoSaida)
    {
        $this->sTipoSaida = $sTipoSaida;
    }

    /**
     * Retorna o tipo de saída do formulário
     */
    public function getTipoSaida()
    {
        return $this->sTipoSaida;
    }

    public function setDelimitarTexto($bDelimitarTexto)
    {
        $this->bDelimitarTexto = $bDelimitarTexto;
    }

    public function getDelimitarTexto()
    {
        return $this->bDelimitarTexto;
    }

    public function setImprimirCabecalho($bImprimirCabecalho)
    {
        $this->bImprimirCabecalho = $bImprimirCabecalho;
    }

    public function getImprimirCabecalho()
    {
        return $this->bImprimirCabecalho;
    }

    /**
     * Retorna estrutura XML das propriedades
     *
     * @return boolean
     */
    public function toXml(XMLWriter $oXmlWriter)
    {
        $oXmlWriter->startElement('Propriedades');

        $oXmlWriter->writeAttribute('versao', mb_convert_encoding($this->iVersao, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('nome', mb_convert_encoding($this->sNome, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('layout', mb_convert_encoding($this->sLayout, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('formato', mb_convert_encoding($this->sFormato, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('orientacao', mb_convert_encoding($this->sOrientacao, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('margemsup', mb_convert_encoding($this->iMargemSup, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('margeminf', mb_convert_encoding($this->iMargemInf, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('margemesq', mb_convert_encoding($this->iMargemEsq, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('margemdir', mb_convert_encoding($this->iMargemDir, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('tiposaida', mb_convert_encoding($this->sTipoSaida, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute(
            'delimitartexto',
            mb_convert_encoding($this->bDelimitarTexto, 'UTF-8', 'ISO-8859-1')
        );
        $oXmlWriter->writeAttribute(
            'imprimircabecalho',
            mb_convert_encoding($this->bImprimirCabecalho, 'UTF-8', 'ISO-8859-1')
        );

        $oXmlWriter->endElement();

        return true;
    }
}
