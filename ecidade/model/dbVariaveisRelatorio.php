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
final class dbVariaveisRelatorio implements iGeradorRelatorio
{
    private $sNome = "";
    private $sLabel = "";
    private $sTipoDado = "";
    private $sValor = "";
    private $sSql = "";


    /**
     * Inclui todos atributos da variável
     *
     * @param string $sNome
     *
     */
    public function __construct($sNome = "", $sLabel = "", $sValor = "", $sTipoDado = "", $sSql = "")
    {
        $this->setNome($sNome);
        $this->setLabel($sLabel);
        $this->setTipoDado($sTipoDado);
        $this->setValor($sValor);
        $this->setTipoDado($sTipoDado);
        $this->setSQL($sSql);
    }

    /**
     * @return string
     */
    public function getNome()
    {

        return $this->sNome;
    }

    /**
     * @param string $sNome
     */
    public function setNome($sNome)
    {
        $this->sNome = $sNome;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if (db_utils::isUTF8($this->sLabel)) {
            return mb_convert_encoding($this->sLabel, 'ISO-8859-1', 'UTF-8');
        } else {
            return $this->sLabel;
        }
    }

    /**
     * @param string $sLabel
     */
    public function setLabel($sLabel)
    {
        $this->sLabel = $sLabel;
    }


    /**
     * @return string
     */
    public function getValor()
    {
        if (db_utils::isUTF8($this->sValor)) {
            return mb_convert_encoding($this->sValor, 'ISO-8859-1', 'UTF-8');
        } else {
            return $this->sValor;
        }
    }


    /**
     * @param string $sValor
     */
    public function setValor($sValor)
    {
        $this->sValor = $sValor;
    }

    /**
     * @return string
     */
    public function getTipoDado()
    {
        return $this->sTipoDado;
    }

    /**
     * @param string
     */
    public function setTipoDado($sTipoDado)
    {
        $this->sTipoDado = $sTipoDado;
    }

    public function getSql()
    {
        return $this->sSql;
    }

    /**
     * @param string $sSql
     */
    public function setSql($sSql)
    {
        $this->sSql = $sSql;
    }

    /**
     * Enter description here...
     *
     * @return true
     */
    public function toXml(XMLWriter $oXmlWriter)
    {
        $oXmlWriter->startElement('Variavel');

        $oXmlWriter->writeAttribute('nome', mb_convert_encoding($this->sNome, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('label', mb_convert_encoding($this->sLabel, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('tipodado', mb_convert_encoding($this->sTipoDado, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('valor', mb_convert_encoding($this->sValor, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('sql', mb_convert_encoding($this->sSql, 'UTF-8', 'ISO-8859-1'));

        $oXmlWriter->endElement();

        return true;
    }
}
