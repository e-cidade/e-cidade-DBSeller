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
final class dbFiltroRelatorio implements iGeradorRelatorio
{

    public $sOperador = "";
    public $sCampo = "";
    public $sCondicao = "";
    public $sValor = "";

    /**
     * Inclui atributos do filtro
     *
     * @param string $sCampo
     * @param string $sCondicao
     * @param string $sValor
     * @param string $sOperador
     *
     */
    public function __construct($sCampo = "", $sCondicao = "", $sValor = "", $sOperador = "")
    {
        $this->setCampo($sCampo);
        $this->setCondicao($sCondicao);
        $this->setValor($sValor);
        $this->setOperador($sOperador);
    }

    /**
     * @return string
     */
    public function getCampo()
    {
        if (db_utils::isUTF8($this->sCampo)) {
            return mb_convert_encoding($this->sCampo, 'ISO-8859-1', 'UTF-8');
        } else {
            return $this->sCampo;
        }
    }

    /**
     * @return string
     */
    public function getCondicao()
    {
        return $this->sCondicao;
    }

    /**
     * @return string
     */
    public function getOperador()
    {
        return $this->sOperador;
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
     * @param string $sCampo
     */
    public function setCampo($sCampo)
    {
        $this->sCampo = $sCampo;
    }

    /**
     * @param string $sCondicao
     */
    public function setCondicao($sCondicao)
    {
        $this->sCondicao = $sCondicao;
    }

    /**
     * @param string $sOperador
     */
    public function setOperador($sOperador)
    {
        $this->sOperador = $sOperador;
    }

    /**
     * @param string $sValor
     */
    public function setValor($sValor)
    {
        $this->sValor = $sValor;
    }

    /**
     * Enter description here...
     *
     * @return true
     */

    public function toXml(XMLWriter $oXmlWriter)
    {
        $oXmlWriter->startElement('Filtro');

        $oXmlWriter->writeAttribute('operador', mb_convert_encoding($this->sOperador, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('campo', mb_convert_encoding($this->sCampo, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('condicao', mb_convert_encoding($this->sCondicao, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('valor', mb_convert_encoding($this->sValor, 'UTF-8', 'ISO-8859-1'));

        $oXmlWriter->endElement();

        return true;
    }
}
